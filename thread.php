<?php

session_start();
require_once "partials/_dbconnect.php";
require_once "constants.php";

if (!isset($_GET["threadid"])) {
    $_SESSION["alert"] = [
        "message" => "Thread id required",
        "type" => ALERT_ERROR
    ];
    header("location: /forum/");
    exit;
}

$threadid = (int) $_GET["threadid"];
$thread = null;

$sql = "SELECT * FROM threads WHERE thread_id=$threadid";
$result = mysqli_query($conn, $sql);
$title = $short_desc = $description = $updated = $created = $isUpdated = $info = $nextQ = $prevQ = null;

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $catid = $row["thread_catid"];
        $title = $row["thread_title"];
        $short_desc = $row["thread_short_desc"];
        $description = $row["thread_content"];
        $updated = $row["updated"];
        $created = $row["created"];
        $isUpdated = $updated != $created;

        $info = '<small>Anonymous at <script>document.write(new Date("' . $created . '").toLocaleString())</script>';
        if ($isUpdated) {
            $title .= " [edited]";
            $info .= ' &bull; <span class="text-muted">Last modified: <script>document.write(new Date("' . $updated . '").toLocaleString())</script></span>';
        }
        $info .= "</small>";

        $nextSql = "SELECT thread_id FROM threads 
                    WHERE thread_catid = $catid 
                    AND (created > '$created' OR (created = '$created' AND thread_id > $threadid))
                    ORDER BY created ASC, thread_id ASC 
                    LIMIT 1";
        $nextResult = mysqli_query($conn, $nextSql);

        if ($nextResult && mysqli_num_rows($nextResult) > 0) {
            $nextQ = "/forum/thread?threadid=" . mysqli_fetch_assoc($nextResult)["thread_id"];
        }

        $prevSql = "SELECT thread_id FROM threads 
                    WHERE thread_catid = $catid 
                    AND (created < '$created' OR (created = '$created' AND thread_id < $threadid))
                    ORDER BY created DESC, thread_id DESC 
                    LIMIT 1";
        $prevResult = mysqli_query($conn, $prevSql);

        if ($prevResult && mysqli_num_rows($prevResult) > 0) {
            $prevQ = "/forum/thread?threadid=" . mysqli_fetch_assoc($prevResult)["thread_id"];
        }
    } else {
        $_SESSION["alert"] = [
            "message" => "Thread not found",
            "type" => ALERT_INFO
        ];
        header("location: /forum/");
        exit;
    }
} else {
    echo "Something went wrong :(";
}

?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo $short_desc ?>">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title><?php echo $title ?> - Coding Forums</title>

    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <?php include "partials/_header.php" ?>

    <main class="container my-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <?php if ($prevQ): ?>
                <a href="<?php echo $prevQ; ?>" class="btn btn-sm btn-outline-danger">&laquo; Previous</a>
            <?php else: ?>
                <button class="btn btn-sm btn-outline-danger" style="cursor: not-allowed;" disabled>&laquo; Previous</button>
            <?php endif; ?>

            <?php if ($nextQ): ?>
                <a href="<?php echo $nextQ; ?>" class="btn btn-sm btn-outline-danger">Next &raquo;</a>
            <?php else: ?>
                <button class="btn btn-sm btn-outline-danger" style="cursor: not-allowed;" disabled>Next &raquo;</button>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <h1><?php echo $title ?></h1>
            <p class="text-muted"><?php echo $info ?></p>
        </div>

        <hr class="my-4">
        <p><?php echo $description ?></p>
        <hr class="my-4">

        <div class="d-flex align-items-center justify-content-between">
            <?php
            $sql = "SELECT COUNT(*) as count FROM answers WHERE answer_threadid=$threadid";
            $result = mysqli_query($conn, $sql);
            $count = mysqli_fetch_assoc($result)["count"];
            ?>
            <h2>Answers: (<?php echo $count ?>)</h2>

            <?php
            if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
                $thread_id = $threadid;
                include "partials/_answer_form.php";
            }
            ?>
        </div>

        <?php
        if ($count == 0) {
            echo '
                <div class="alert alert-secondary col-md-12" role="alert">
                    <p class="mb-0">No answers yet! Be the first one to post an answer.</p>
                </div>';
        } else {
            // Pagination logic
            $page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;

            $num_pages = (int) ceil($count / PAGE_LIMIT);
            $page = max(1, min($page, $num_pages));
            $offset = ($page - 1) * PAGE_LIMIT;

            $sql1 = "SELECT answer_id, answer_content, updated, created, users.username as username FROM answers JOIN users ON answers.answer_userid = users.user_id WHERE answer_threadid=$threadid ORDER BY created DESC LIMIT $offset, " . PAGE_LIMIT;
            $result1 = mysqli_query($conn, $sql1);
            while ($row = mysqli_fetch_assoc($result1)) {
                $characters_limit = 80;
                $id = $row["answer_id"];
                $content = $row["answer_content"];
                $updated = $row["updated"];
                $created = $row["created"];
                $username = $row["username"];
                $isUpdated = $updated != $created;

                $info = '<small>'.$username.' at <script>document.write(new Date("' . $created . '").toLocaleString())</script>';
                if ($isUpdated) {
                    $info .= ' &bull; <span class="text-muted">Last modified: <script>document.write(new Date("' . $updated . '").toLocaleString())</script></span>';
                }
                $info .= "</small>";


                echo '<div class="media border rounded p-3 mb-2">
                    <img src="assets/icons/user.png" class="mr-3 rounded-circle" width="40px" alt="userImg">
                    <div class="media-body">
                        ' . $info . '
                        <pre class="mt-2">' . $content . '</pre>
                    </div>
                </div>
                    ';
            }

            // Pagination div
            echo '<div class="d-flex align-items-center justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">';

            if ($page > 1) {
                echo '<li class="page-item">
                            <a class="page-link" href="/forum/threadlist?catid=' . $catid . '&page=' . ($page - 1) . '" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>';
            } else {
                echo '<li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>';
            }

            for ($i = 0; $i < $num_pages; $i++) {
                if ($page === $i + 1) {
                    echo '<li class="page-item active"><a class="page-link" href="/forum/threadlist?catid=' . $catid . '&page=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="/forum/threadlist?catid=' . $catid . '&page=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                }
            }

            if ($page < $num_pages) {
                echo '<li class="page-item">
                            <a class="page-link" href="/forum/threadlist?catid=' . $catid . '&page=' . ($page + 1) . '" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>';
            } else {
                echo '<li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>';
            }
            echo '</ul>
                    </nav>
                </div>';
        }
        ?>
    </main>

    <?php include "partials/_footer.php" ?>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <!-- Option 2: jQuery, Popper.js, and Bootstrap JS
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    -->
</body>

</html>