<?php
// Database connection
$host = "localhost";
$user = "root";   // XAMPP default user
$pass = "";       // XAMPP default password is blank
$db   = "gmail_clone";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle login
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    // Fetch user
    $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $first_name, $last_name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $first_name . " " . $last_name;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with that username.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign In – Gmail</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Roboto', sans-serif;
    }
    .login-box {
      max-width: 500px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin: 80px auto;
      padding: 30px;
    }
    .google-logo {
      width: 75px;
      display: block;
      margin: 0 auto 20px;
    }
    .btn-primary {
      background-color: #1a73e8;
      border: none;
    }
    .btn-primary:hover {
      background-color: #1669c1;
    }
    a {
      color: #1a73e8;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png" 
         alt="Google" class="google-logo">
    <h4 class="text-center">Sign in</h4>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <input type="email" name="username" class="form-control" placeholder="Email or phone" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
      </div>
      <div class="d-flex justify-content-between align-items-center">
        <a href="register.php">Create account</a>
        <button type="submit" class="btn btn-primary px-4">Next</button>
      </div>
    </form>
  </div>

</body>
</html>
