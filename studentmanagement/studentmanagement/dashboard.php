<?php
session_start();

// Require authentication
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

/* ================= DATABASE ================= */
$conn = mysqli_connect("localhost","root","","studentmanagement");
if(!$conn) die("Database connection failed");

/* ================= LOGOUT ================= */
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: login.php");
    exit;
}

// Single page app focused only on students
$page = 'students';

/* =====================================================
   STUDENTS : CREATE | UPDATE | DELETE
===================================================== */
if(isset($_POST['add_student'])){
    mysqli_query($conn,"INSERT INTO student(name,gender,phone,address,country)
    VALUES('{$_POST['name']}','{$_POST['gender']}','{$_POST['phone']}','{$_POST['address']}','{$_POST['country']}')");
}

if(isset($_POST['update_student'])){
    mysqli_query($conn,"UPDATE student SET
        name='{$_POST['name']}',
        gender='{$_POST['gender']}',
        phone='{$_POST['phone']}',
        address='{$_POST['address']}',
        country='{$_POST['country']}'
        WHERE student_id={$_POST['student_id']}
    ");
}

if(isset($_GET['delete_student'])){
    mysqli_query($conn,"DELETE FROM student WHERE student_id=".$_GET['delete_student']);
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>
<style>
body{font-family:Arial;margin:0;background:#f4f6f8}
.dashboard{display:flex;min-height:100vh}
.sidebar{width:220px;background:#1e1e2f;color:#fff;padding:20px}
.sidebar a{color:#fff;text-decoration:none;display:block;padding:10px;margin:5px 0}
.sidebar a:hover{background:#4fc3f7}
.main{flex:1;padding:25px}
table{width:100%;border-collapse:collapse;margin-top:15px}
th,td{border:1px solid #ccc;padding:8px}
form input,button{padding:7px;margin:5px}
button{background:#4fc3f7;border:none;color:#fff}
.delete{color:red}
.edit{color:green}
</style>
</head>

<body>
<div class="dashboard">

<div class="sidebar">
<h3>Student System</h3>
<a href="?page=students">Home</a>
<a href="?logout=1">Logout</a>
</div>

<div class="main">

<!-- ================= STUDENTS ================= -->
<?php if($page=='students'):
$edit = null;
if(isset($_GET['edit_student'])){
    $edit = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM student WHERE student_id=".$_GET['edit_student']));
}
$students = mysqli_query($conn,"SELECT * FROM student");
?>
<h2>Students</h2>

<form method="post">
<input type="hidden" name="student_id" value="<?= $edit['student_id'] ?? '' ?>">

<input name="name" placeholder="Name" value="<?= $edit['name'] ?? '' ?>" required>
<input name="gender" placeholder="Gender" value="<?= $edit['gender'] ?? '' ?>" required>
<input name="phone" placeholder="Phone" value="<?= $edit['phone'] ?? '' ?>" required>
<input name="address" placeholder="Address" value="<?= $edit['address'] ?? '' ?>" required>
<input name="country" placeholder="Country" value="<?= $edit['country'] ?? '' ?>" required>

<?php if($edit): ?>
<button name="update_student">Update</button>
<?php else: ?>
<button name="add_student">Add</button>
<?php endif; ?>
</form>

<table>
<tr><th>ID</th><th>Name</th><th>Gender</th><th>Phone</th><th>Address</th><th>Country</th><th>Action</th></tr>
<?php while($s=mysqli_fetch_assoc($students)): ?>
<tr>
<td><?= $s['student_id'] ?></td>
<td><?= $s['name']?></td>
<td><?= $s['gender'] ?></td>
<td><?= $s['phone'] ?></td>
<td><?= $s['address'] ?></td>
<td><?= $s['country'] ?></td>
<td>
<a class="edit" href="?page=students&edit_student=<?= $s['student_id'] ?>">Edit</a> |
<a class="delete" href="?page=students&delete_student=<?= $s['student_id'] ?>">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; ?>

</div>
</div>
</body>
</html>