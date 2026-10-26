<?php session_start(); ?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title>Welcome to iDiscuss - Coding Forums</title>

    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <?php include "partials/_header.php" ?>

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images/slider1.jpg" class="d-block w-100" alt="slider1">
            </div>
            <div class="carousel-item">
                <img src="assets/images/slider2.jpg" class="d-block w-100" alt="slider2">
            </div>
            <div class="carousel-item">
                <img src="assets/images/slider3.jpg" class="d-block w-100" alt="slider3">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <main class="container my-3">
        <h2 class="text-center">iDiscuss - Categories</h2>
        <div id="categories">
            <?php
            require_once "partials/_dbconnect.php";
            require_once "constants.php";
            $sql = "SELECT COUNT(*) as count FROM categories";
            $result = mysqli_query($conn, $sql);
            $count = mysqli_fetch_assoc($result)["count"];

            if ($count == 0) {
                echo '
                <div class="alert alert-secondary col-md-12" role="alert">
                    <h5 class="mb-0">No categories to be displayed!</h5>
                </div>';
            } else {
                // Pagination logic
                $page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;

                $num_pages = (int) ceil($count / PAGE_LIMIT);
                $page = max(1, min($page, $num_pages));
                $offset = ($page - 1) * PAGE_LIMIT;

                $sql1 = "SELECT c.*, count(th.thread_id) as total_threads FROM categories c LEFT JOIN threads th ON th.thread_catid = c.category_id GROUP BY c.category_id LIMIT $offset, ". PAGE_LIMIT;
                $result1 = mysqli_query($conn, $sql1);

                echo '<div class="row mx-0">
                ';
                while ($row = mysqli_fetch_assoc($result1)) {
                    $characters_limit = 80;
                    $id = $row["category_id"];
                    $name = $row["category_name"];
                    $description = $row["category_description"];
                    $created = $row["created"];
                    $total_threads = $row["total_threads"];
                    $truncated = $description;

                    if (mb_strlen($description) > $characters_limit) $truncated = mb_substr($truncated, 0, $characters_limit);
                    $last_space = mb_strrpos($truncated, ' ');
                    if ($last_space !== false) {
                        $truncated = mb_substr($truncated, 0, $last_space);
                    }
                    $truncated = $truncated . '...';


                    echo '<div class="col-md-4">
                        <div class="card my-2" style="width: 18rem;">
                            <img src="assets/images/card.jpg" class="card-img-top" alt="cat' . $id . 'Image">
                            <div class="card-body">
                                <a href="/forum/threadlist?catid=' . $id . '" class="text-dark">
                                    <h5 class="card-title">' . $name . '</h5> 
                                </a>
                                <p class="card-text mb-1 text-muted">'.$total_threads.' '.ngettext("question", "questions", $total_threads).'</p>
                                <p class="card-text">' . $truncated . '</p>
                                <a href="/forum/threadlist?catid=' . $id . '" class="btn btn-primary">Visit Thread &raquo;</a>
                            </div>
                        </div>
                    </div>
                    ';
                }
                echo '</div>
                ';

                // Pagination div
                echo '<div class="d-flex align-items-center justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">';

                    if ($page > 1) {
                        echo '<li class="page-item">
                            <a class="page-link" href="?page='.($page-1).'" aria-label="Previous">
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
                            echo '<li class="page-item active"><a class="page-link" href="?page=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                        } else {
                            echo '<li class="page-item"><a class="page-link" href="?page=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
                        }
                    }

                    if ($page < $num_pages) {
                        echo '<li class="page-item">
                            <a class="page-link" href="?page='.($page+1).'" aria-label="Next">
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

        </div>
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