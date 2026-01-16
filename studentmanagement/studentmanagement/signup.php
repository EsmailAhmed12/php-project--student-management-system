<?php
session_start();
include "connection.php";

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

// Stop early if DB connection failed
if ($conn === false || $conn->connect_error) {
    $error = "Database connection failed. Please check DB settings.";
}

if (isset($_POST['signup']) && !$error) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if ($username === "" || $password === "") {
        $error = "Username and password are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Ensure username is unique
        $check = $conn->prepare("SELECT 1 FROM users WHERE username = ?");

        if ($check) {
            $check->bind_param("s", $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "Username is already taken.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

                if ($insert) {
                    $insert->bind_param("ss", $username, $hash);

                    if ($insert->execute()) {
                        $_SESSION['user'] = $username;
                        $_SESSION['time'] = time();
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        // Log the specific DB error for troubleshooting
                        error_log("Signup insert failed: " . $conn->error);
                        $error = "Could not create account. Please try again.";
                    }

                    $insert->close();
                } else {
                    error_log("Signup prepare failed: " . $conn->error);
                    $error = "Signup unavailable (DB schema error). Please contact admin.";
                }
            }

            $check->close();
        } else {
            // Prepare failed; attempt fallback with escape
            $safeUser = $conn->real_escape_string($username);
            $existing = $conn->query("SELECT 1 FROM users WHERE username='".$safeUser."'");

            if ($existing && $existing->num_rows > 0) {
                $error = "Username is already taken.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, password) VALUES ('".$safeUser."','".$conn->real_escape_string($hash)."')";
                if ($conn->query($sql)) {
                    $_SESSION['user'] = $username;
                    $_SESSION['time'] = time();
                    header("Location: dashboard.php");
                    exit;
                } else {
                    error_log("Signup fallback insert failed: " . $conn->error);
                    $error = "Could not create account. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sign Up</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
body { height: 100vh; background: linear-gradient(135deg, #1d2671, #c33764); display: flex; justify-content: center; align-items: center; }
.signup-box { background: white; padding: 35px; width: 380px; border-radius: 12px; box-shadow: 0 15px 30px rgba(0,0,0,0.3); }
.signup-box h2 { text-align: center; margin-bottom: 25px; color: #333; }
.input-group { margin-bottom: 18px; }
.input-group input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px; }
.input-group input:focus { outline: none; border-color: #c33764; box-shadow: 0 0 5px rgba(195,55,100,0.5); }
button { width: 100%; padding: 12px; background: #c33764; border: none; color: white; font-size: 16px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s; }
button:hover { background: #1d2671; }
.message { background: #e6ffe6; color: #2d7a2d; padding: 10px; margin-bottom: 15px; border-radius: 6px; text-align: center; font-size: 14px; }
.error { background: #ffe0e0; color: #b30000; padding: 10px; margin-bottom: 15px; border-radius: 6px; text-align: center; font-size: 14px; }
.footer { text-align: center; margin-top: 15px; font-size: 13px; color: #777; }
</style>
</head>
<body>

<div class="signup-box">
    <h2>Create Account</h2>

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

        <div class="input-group">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>

        <button type="submit" name="signup">Sign Up</button>
    </form>

    <div class="footer">
        <a href="index.php">Home</a> · <a href="login.php">Back to Login</a>
    </div>
</div>

</body>
</html>
