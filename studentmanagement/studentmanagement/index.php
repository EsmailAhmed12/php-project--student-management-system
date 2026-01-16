<?php
session_start();

$loggedIn = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Management</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; }
body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #1d2671, #c33764); color: #fff; }
.container { background: rgba(0,0,0,0.2); padding: 30px; border-radius: 14px; width: 360px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.35); }
.container h1 { margin-bottom: 10px; letter-spacing: 0.5px; }
.container p { margin-bottom: 20px; color: #f0f0f0; }
.links { display: grid; gap: 12px; margin-top: 10px; }
.links a { display: block; padding: 12px; border-radius: 10px; text-decoration: none; color: #fff; font-weight: bold; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease; }
.links a:hover { transform: translateY(-2px); background: rgba(255,255,255,0.2); box-shadow: 0 8px 16px rgba(0,0,0,0.25); }
.note { margin-top: 16px; font-size: 13px; color: #e6e6e6; }
</style>
</head>
<body>
<div class="container">
    <h1>Student Management</h1>
    <p>Access the student system from here.</p>
    <div class="links">
        <?php if ($loggedIn): ?>
            <a href="dashboard.php">Go to Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Create an Account</a>
        <?php endif; ?>
    </div>
    <div class="note">You need an account to use the dashboard.</div>
</div>
</body>
</html>
