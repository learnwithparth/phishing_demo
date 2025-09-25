<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container text-center mt-5">
    <h2>Welcome, <?= $_SESSION['name']; ?> 🎉</h2>
    <p>You have successfully logged in.</p>
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>
</body>
</html>
