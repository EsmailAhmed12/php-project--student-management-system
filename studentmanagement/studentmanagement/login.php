<?php
session_start();
include "connection.php";

$error = "";

// Bail early if DB connection failed
if ($conn === false || $conn->connect_error) {
    $error = "Database connection failed. Please check DB settings.";
}

// Redirect already logged-in users
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['login']) && !$error) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepared statement
    $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ?");

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Support hashed passwords; plus legacy formats (plain, md5, sha1)
            $valid = (
                password_verify($password, $user['password']) ||
                $password === $user['password'] ||
                md5($password) === $user['password'] ||
                sha1($password) === $user['password']
            );

            if ($valid) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['time'] = time();
                header("Location: dashboard.php");
                exit;
            }

            $error = "Incorrect password";
        } else {
            $error = "Username not found";
        }

        $stmt->close();
    } else {
        // Prepare failed; attempt fallback query with manual escaping
        $safeUser = $conn->real_escape_string($username);
        $result = $conn->query("SELECT username, password FROM users WHERE username = '".$safeUser."'");

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            $valid = (
                password_verify($password, $user['password']) ||
                $password === $user['password'] ||
                md5($password) === $user['password'] ||
                sha1($password) === $user['password']
            );

            if ($valid) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['time'] = time();
                header("Location: dashboard.php");
                exit;
            }

            $error = "Incorrect password";
        } else {
            $error = "Username not found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    height: 100vh;
    background: linear-gradient(135deg, #1d2671, #c33764);
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    background: white;
    padding: 35px;
    width: 350px;
    border-radius: 12px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.3);
}

.login-box h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
}

.input-group {
    margin-bottom: 18px;
}

.input-group input {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

.input-group input:focus {
    outline: none;
    border-color: #c33764;
    box-shadow: 0 0 5px rgba(195,55,100,0.5);
}

button {
    width: 100%;
    padding: 12px;
    background: #c33764;
    border: none;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #1d2671;
}

.error {
    background: #ffe0e0;
    color: #b30000;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    text-align: center;
    font-size: 14px;
}

.footer {
    text-align: center;
    margin-top: 15px;
    font-size: 13px;
    color: #777;
}
</style>
</head>
<body>

<div class="login-box">
    <h2>Student Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="input-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" name="login">Login</button>
    </form>

    <div class="footer">
        <a href="index.php">Home</a> · <a href="signup.php">Create an account</a>
    </div>
</div>

</body>
</html>
