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
        $threadId = $_POST["threadId"];
        $content = $_POST["newAnswerContent"];

        if (empty(trim($content))) {
            $err = "Please fill all the fields";
            $_SESSION["alert"] = [
                "message" => $err,
                "type" => ALERT_ERROR
            ];
            header('Location: /forum/thread?threadid=' . $threadId);
            exit;
        } else {
            $sql = "SELECT * FROM threads WHERE thread_id=?";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $param_threadId);

                $param_threadId = (int) $threadId;

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) < 1) {
                        $err = "Thread does not exist!";
                        $_SESSION["alert"] = [
                            "message" => $err,
                            "type" => ALERT_ERROR
                        ];
                        header('Location: /forum/thread?threadid=' . $threadId);
                        exit;
                    }
                } else {
                    echo "Something went wrong";
                }

                mysqli_stmt_close($stmt);
            }


            // Insert if category exists
            $sql = "INSERT INTO answers (answer_content, answer_threadid, answer_userid) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssi", $param_content, $param_threadId, $param_userId);

                $param_threadId = (int) $threadId;
                $param_userId = (int) $userId;
                $param_content = trim($content);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION["alert"] = [
                        "message" => "Answer added successfully",
                        "type" => ALERT_SUCCESS
                    ];
                    header('Location: /forum/thread?threadid=' . $threadId);
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