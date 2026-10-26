<?php

session_start();

require_once __DIR__ . '/../../partials/_dbconnect.php';
require_once __DIR__ . '/../../constants.php';

$err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    $path = $_POST["path"];

    if (empty(trim($username)) || empty(trim($email)) || empty(trim($password)) || empty(trim($cpassword))) {
        $err = "Please fill up all fields to signup";
        $_SESSION["alert"] = [
            "message" => $err,
            "type" => ALERT_ERROR
        ];
        header("Location: " . $path);
        exit;
    } else if (!preg_match(USERNAME_PATTERN, $username)) {
        $err = "Invalid username. Must be 4-12 characters and contain only letters, numbers, or underscores.";
        $_SESSION["alert"] = [
            "message" => $err,
            "type" => ALERT_ERROR
        ];
        header("Location: " . $path);
        exit;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "Invalid email format";
        $_SESSION["alert"] = [
            "message" => $err,
            "type" => ALERT_ERROR
        ];
        header("Location: " . $path);
        exit;
    } else {
        // Check if username or email already taken
        $sql = "SELECT * FROM users WHERE username=? OR user_email=?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_email);

            $param_username = trim($username);
            $param_email = trim($email);

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_num_rows($stmt) > 1) {
                    $err = "Username or email already taken";
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

        // Check if password and confirm password match
        if (strlen($password) < 8) {
            $err = "Password must contain atleast 8 characters";
            $_SESSION["alert"] = [
                "message" => $err,
                "type" => ALERT_ERROR
            ];
            header("Location: " . $path);
            exit;
        } else if ($password !== $cpassword) {
            $err = "Passwords do not match";
            $_SESSION["alert"] = [
                "message" => $err,
                "type" => ALERT_ERROR
            ];
            header("Location: " . $path);
            exit;
        } else {
            $hashed = password_hash($password, PASSWORD_ARGON2ID);

            $sql = "INSERT INTO users (username, user_email, user_pass) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);

                $param_username = trim($username);
                $param_email = trim($email);
                $param_password = $hashed;

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION["alert"] = [
                        "message" => "User account created successfully",
                        "type" => ALERT_SUCCESS
                    ];
                    header("Location: " . $path);
                    exit;
                } else {
                    echo "Something went wrong :(";
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
