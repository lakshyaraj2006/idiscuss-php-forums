<?php

session_start();
require_once __DIR__ . "/../../partials/_dbconnect.php";
require_once __DIR__ . "/../../constants.php";

$err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true) {
        http_response_code(401);
        echo "<h1>Unauthorized (401)</h1>\nAuthentication required";
        exit;
    } else {
        $userId = $_SESSION["user"]["id"];
        $catId = $_POST["catId"];
        $title = $_POST["newThreadTitle"];
        $shortDesc = $_POST["newThreadShortDesc"];
        $content = $_POST["newThreadContent"];

        if (empty(trim($title)) || empty(trim($shortDesc)) || empty(trim($content))) {
            $err = "Please fill all the fields";
            $_SESSION["alert"] = [
                "message" => $err,
                "type" => ALERT_ERROR
            ];
            header('Location: /forum/threadlist?catid=' . $catId);
            exit;
        } else {
            $sql = "SELECT * FROM categories WHERE category_id=?";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $param_catId);

                $param_catId = (int) $catId;

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) < 1) {
                        $err = "Category does not exist!";
                        $_SESSION["alert"] = [
                            "message" => $err,
                            "type" => ALERT_ERROR
                        ];
                        header('Location: /forum/threadlist?catid=' . $catId);
                        exit;
                    }
                } else {
                    echo "Something went wrong";
                }

                mysqli_stmt_close($stmt);
            }


            // Insert if category exists
            $sql = "INSERT INTO threads (thread_title, thread_short_desc, thread_content, thread_catid, thread_userid) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssii", $param_title, $param_short_desc, $param_content, $param_catId, $param_userId);

                $param_catId = (int) $catId;
                $param_userId = (int) $userId;
                $param_title = trim($title);
                $param_short_desc = trim($shortDesc);
                $param_content = trim($content);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION["alert"] = [
                        "message" => "Thread created successfully",
                        "type" => ALERT_SUCCESS
                    ];
                    header('Location: /forum/threadlist?catid=' . $catId);
                    exit;
                } else {
                    echo "Something went wrong";
                }

                mysqli_stmt_close($stmt);
            }
        }
    }
} else {
    http_response_code(400);
    echo "<h1>Bad Request (400)</h1>\nInvalid request method " . $_SERVER["REQUEST_METHOD"] . ".";
    exit;
}

?>