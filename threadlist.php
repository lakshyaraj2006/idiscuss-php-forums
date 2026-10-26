<?php

session_start();
require_once "partials/_dbconnect.php";
require_once "constants.php";

if (!isset($_GET["catid"])) {
    $_SESSION["alert"] = [
        "message" => "Category id required",
        "type" => ALERT_ERROR
    ];
    header("location: /forum/");
    exit;
}

$catid = (int) $_GET["catid"];
$cat = null;

$sql = "SELECT * FROM categories WHERE category_id=$catid";
$result = mysqli_query($conn, $sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $cat = mysqli_fetch_assoc($result);
    } else {
        $_SESSION["alert"] = [
            "message" => "Category does not exist",
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

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title><?php echo $cat["category_name"] ?> Forums - Coding Forums</title>

    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <?php include "partials/_header.php" ?>

    <main class="container my-3">
        <div class="jumbotron">
            <h1><?php echo $cat["category_name"] ?> Forums</h1>
            <p class="text-muted"><?php echo $cat["category_description"] ?></p>
            <hr class="my-4">
            <p class="mb-0">This forums is only for questions under <strong><?php echo $cat["category_name"] ?></strong> category.</p>
        </div>

        <hr class="my-4">

        <div class="d-flex align-items-center justify-content-between">
            <?php
            $sql = "SELECT COUNT(*) as count FROM threads WHERE thread_catid=$catid";
            $result = mysqli_query($conn, $sql);
            $count = mysqli_fetch_assoc($result)["count"];
            ?>
            <h2>Threads: (<?php echo $count ?>)</h2>

            <?php
            if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
                $category_id = $catid;
                include "partials/_thread_form.php";
            }
            ?>
        </div>
        <?php

        if ($count == 0) {
            echo '
                <div class="alert alert-secondary col-md-12" role="alert">
                    <p class="mb-0">No threads yet! Be the first one to post a thread.</p>
                </div>';
        } else {
            // Pagination logic
            $page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;

            $num_pages = (int) ceil($count / PAGE_LIMIT);
            $page = max(1, min($page, $num_pages));
            $offset = ($page - 1) * PAGE_LIMIT;

            $sql1 = "SELECT th.*, users.username as username, COUNT(ans.answer_id) as ans_count FROM threads th JOIN users ON th.thread_userid = users.user_id LEFT JOIN answers ans ON ans.answer_threadid = th.thread_id  WHERE th.thread_catid=$catid GROUP BY th.thread_id ORDER BY th.created DESC LIMIT $offset, " . PAGE_LIMIT;
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

                $info = '<small>'.$username.' at <script>document.write(new Date("' . $created . '").toLocaleString())</script>';
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

    <script>
        const newThreadForm = document.querySelector('#newThreadForm');
        const submitBtn = document.querySelector('#submitBtn');
        const saveLocalIndicator = document.querySelector('#saveLocalIndicator');

        let data = localStorage.getItem('idiscuss-saved-threads') ? JSON.parse(localStorage.getItem('idiscuss-saved-threads')) : [];
        let saveTimeout;

        // Save when a field's value changes.
        const handleFormChange = () => {
            saveLocalIndicator.classList.remove('invisible');
            clearTimeout(saveTimeout);

            saveTimeout = setTimeout(() => {
                saveThreadToLocal();
            }, 300);
        };

        newThreadForm.addEventListener("input", handleFormChange);
        newThreadForm.addEventListener("change", handleFormChange);

        const saveThreadToLocal = () => {

            if (!newThreadForm) {
                console.error('New thread form not found.');
                return;
            }

            const formData = new FormData(newThreadForm);
            const jsonData = Object.fromEntries(formData.entries());

            if (data.length > 0) {
                // Check if thread exists
                const threadIdx = data.findIndex(e => Number(e.catId) === <?php echo $catid ?>);

                // Update if threadIdx is not -1
                if (threadIdx != -1) {
                    data[threadIdx] = jsonData;
                } else {
                    data.push(jsonData);
                }
            } else {
                data.push(jsonData);
            }

            localStorage.setItem('idiscuss-saved-threads', JSON.stringify(data));
            saveLocalIndicator.classList.add('invisible');
        }

        const loadSavedThread = () => {
            if (data.length > 0) {
                const thisThread = data.find(e => Number(e.catId) === <?php echo $catid ?>);

                if (!thisThread || !newThreadForm) {
                    return;
                }

                Object.keys(thisThread).forEach(key => {
                    const field = newThreadForm.elements[key];

                    if (!field) {
                        return;
                    }

                    const value = thisThread[key];

                    if (field instanceof RadioNodeList) {
                        // Restore radio groups.
                        field.value = value;
                    } else if (field.type === 'checkbox') {
                        // Restore individual checkboxes.
                        if (Array.isArray(value)) {
                            field.checked = value.includes(field.value);
                        } else {
                            field.checked = Boolean(value);
                        }
                    } else if (field.type === 'radio') {
                        // Restore an individual radio input.
                        field.checked = field.value === String(value);
                    } else if (
                        field.tagName === 'SELECT' &&
                        field.multiple
                    ) {
                        // Restore multiple selections.
                        const selectedValues = Array.isArray(value) ?
                            value : [];

                        Array.from(field.options).forEach(option => {
                            option.selected = selectedValues.includes(
                                option.value
                            );
                        });
                    } else {
                        // Restore text, hidden, textarea and other inputs.
                        field.value = value;
                    }

                    // Notify listeners that the field value has changed.
                    field.dispatchEvent(
                        new Event('change', {
                            bubbles: true
                        })
                    );
                });
            }
        }

        loadSavedThread();
    </script>
</body>

</html>