<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="/forum">iDiscuss</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="/forum">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/forum/about">About</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Categories
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?php
          require_once "partials/_dbconnect.php";
          $sql = "SELECT * FROM categories LIMIT 5";
          $result = mysqli_query($conn, $sql);
          $count = mysqli_num_rows($result);

          if ($count != 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              echo '
                <a class="dropdown-item" href="/forum/threadlist?catid=' . $row["category_id"] . '">' . $row["category_name"] . '</a>
                ';
            }
            echo '
                <div class="dropdown-divider"></div>
                
                <a class="dropdown-item" href="/forum/#categories">View all Categories</a>';
          } else {
            echo '<a class="dropdown-item disabled" href="#" tabindex="-1" aria-disabled="true">No categories</a>';
          }
          ?>

        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Contact</a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0 mr-3" action="/forum/results" method="get">
      <input type="hidden" name="path" value="<?php echo $_SERVER["REQUEST_URI"] ?>">
      <input class="form-control mr-sm-2" type="search" name="search_query" id="search_query" placeholder="Search" aria-label="Search">
      <button class="btn btn-danger my-2 my-sm-0" type="submit">Search</button>
    </form>
    <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true): ?>
    <div>
      <button class="btn btn-danger" data-toggle="modal" data-target="#loginModal">Login</button>
      <button class="btn btn-danger" data-toggle="modal" data-target="#signupModal">SignUp</button>
    </div>
    <?php else: ?>
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle font-weight-bold active" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Welcome - <?php echo $_SESSION["user"]["username"] ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="/forum/logout?path=<?php echo $_SERVER["REQUEST_URI"] ?>">Logout</a>
        </div>
      </li>
    </ul>
    <?php endif; ?>
  </div>
</nav>

<?php
require_once "constants.php";
include "partials/_alert.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true) {
  include "partials/_loginModal.php";
  include "partials/_signupModal.php";
}
?>