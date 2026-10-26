<?php

session_start();

require_once 'partials/_dbconnect.php';
require_once 'constants.php';

$path = $_GET["path"] ?? "/forum/";

if (!isset($_GET["search_query"]) || trim($_GET["search_query"]) === "") {
    $_SESSION["alert"] = [
        "message" => "Please enter your query",
        "type" => ALERT_ERROR
    ];

    header("Location: " . $path);
    exit;
}

$query = trim($_GET["search_query"]);

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title>Search Results - Coding Forums</title>

    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <?php include "partials/_header.php" ?>

    <main class="container my-3">
        <?php
        $sql = "SELECT COUNT(*) as count FROM threads WHERE thread_title LIKE '%$query%'";
        $result = mysqli_query($conn, $sql);
        $count = mysqli_fetch_assoc($result)["count"];
        ?>
        <h2>Search results for <em><?php echo $query ?></em> (<?php echo $count ?> search <?php echo ngettext("result", "results", $count) ?>)</h2>

        <hr class="my-4">
        <?php

        if ($count == 0) {
            echo '
                <div class="alert alert-secondary col-md-12" role="alert">
                    <p class="mb-0">No search results for <strong>' . $query . '</strong></p>
                </div>';
        } else {
            // Pagination logic
            $page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;

            $num_pages = (int) ceil($count / PAGE_LIMIT);
            $page = max(1, min($page, $num_pages));
            $offset = ($page - 1) * PAGE_LIMIT;

            $sql1 = "SELECT th.*, users.username as username, COUNT(ans.answer_id) as ans_count FROM threads th JOIN users ON th.thread_userid = users.user_id LEFT JOIN answers ans ON ans.answer_threadid = th.thread_id  WHERE th.thread_title LIKE '%$query%' GROUP BY th.thread_id ORDER BY th.created DESC LIMIT $offset, " . PAGE_LIMIT;
            $result1 = mysqli_query($conn, $sql1);
            while ($row = mysqli_fetch_assoc($result1)) {
                $characters_limit = 80;
                $id = $row["thread_id"];
                $title = $row["thread_title"];
                $short_desc = $row["thread_short_desc"];
                $updated = $row["updated"];
                $created = $row["created"];
                $username = $row["username"];
                $ans_count = $row["ans_count"];
                $isUpdated = $updated != $created;

                $info = '<small>' . $username . ' at <script>document.write(new Date("' . $created . '").toLocaleString())</script>';
                if ($isUpdated) {
                    $title .= " [edited]";
                    $info .= ' &bull; <span class="text-muted">Last modified: <script>document.write(new Date("' . $updated . '").toLocaleString())</script></span>';
                }
                $info .= "</small>";


                echo '<div class="media border rounded p-3 mb-2">
                    <img src="assets/icons/user.png" class="mr-3 rounded-circle" width="40px" alt="userImg">
                    <div class="media-body">
                        <p class="mb-1">' . $info . '</p>
                        <a href="/forum/thread?threadid=' . $id . '" class="text-dark" title="' . $title . '">
                            <h5 class="mt-0">' . $title . '</h5>
                        </a>
                        <p>
                            ' . $short_desc . '
                        </p>
                        <p class="text-muted mb-0">' . $ans_count . ' ' . ngettext("answer", "answers", $ans_count) . '</p>
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
                            <a class="page-link" href="/forum/results?search_query=' . $query . '&page=' . ($page - 1) . '" aria-label="Previous">
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
                    echo '<li class="page-item active"><a class="page-link" href="/forum/results?search_query=' . $query . '&page=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="/forum/results?search_query=' . $query . '&page=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                }
            }

            if ($page < $num_pages) {
                echo '<li class="page-item">
                            <a class="page-link" href="/forum/results?search_query=' . $query . '&page=' . ($page + 1) . '" aria-label="Next">
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

    <script>
        const url = new URL(window.location.href);

        if (url.searchParams.has("path")) {
            url.searchParams.delete("path");

            window.history.replaceState(
                window.history.state,
                "",
                url.pathname + url.search + url.hash
            );
        }
    </script>
</body>

</html>