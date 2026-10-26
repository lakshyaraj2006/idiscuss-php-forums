<?php

session_start();

require_once __DIR__ . '/../../partials/_dbconnect.php';
require_once __DIR__ . '/../../constants.php';

$err = "";
$id = $db_username = $hashed_password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $path = $_POST["path"];

    echo $username;
    echo $password;
    echo $path;

    if (empty(trim($username)) || empty(trim($password))) {
        $err = "Please fill up all fields to signup";
        $_SESSION["alert"] = [
            "message" => $err,
            "type" => ALERT_ERROR
        ];
        header("Location: " . $path);
        exit;
    } else {
        // Check if username or email already taken
        $sql = "SELECT user_id, username, user_pass FROM users WHERE username=?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = trim($username);

            if (mysqli_stmt_execute($stmt)) {
                // Store the result       
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $db_username, $hashed_password);

                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            session_regenerate_id();

                            $_SESSION["user"] = [
                                "id" => $id,
                                "username" => $db_username
                            ];
                            $_SESSION["loggedin"] = true;

                            // Redirect user to welcome page
                            $_SESSION["alert"] = [
                                "message" => "Logged in successfully",
                                "type" => ALERT_SUCCESS
                            ];
                            header("Location: " . $path);
                            exit;
                        } else {
                            $err = "Invalid credentials";
                            $_SESSION["alert"] = [
                                "message" => $err,
                                "type" => ALERT_ERROR
                            ];
                            header("Location: " . $path);
                            exit;
                        }
                    }
                } else {
                    $err = "Invalid credentials";
                    $_SESSION["alert"] = [
                        "message" => $err,
                        "type" => ALERT_ERROR
                    ];
                    header("Location: " . $path);
                    exit;
                }
            } else {
                echo "Something went wrong :(";
            }

            mysqli_stmt_close($stmt);
        }
    }
} else {
    http_response_code(400);
    echo "<h1>Bad Request (400)</h1>\nInvalid request method " . $_SERVER["REQUEST_METHOD"] . ".";
    exit;
}

?>
