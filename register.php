<?php
// Database connection (XAMPP default user: root, password: empty)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "gmail_clone";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name  = htmlspecialchars($_POST['last_name']);
    $username   = htmlspecialchars($_POST['username']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm'];

    // Basic validation
    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Hash password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare statement to avoid SQL injection
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $first_name, $last_name, $username, $hashed_password);

        if ($stmt->execute()) {
            $success = "Account created successfully for $first_name $last_name ($username)";
        } else {
            if ($conn->errno == 1062) { // Duplicate username
                $error = "This username ($username) is already taken.";
            } else {
                $error = "Database error: " . $conn->error;
            }
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up – Gmail</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Roboto', sans-serif;
    }
    .signup-box {
      max-width: 900px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin: 40px auto;
      padding: 30px;
    }
    .google-logo {
      width: 75px;
      display: block;
      margin: 0 auto 20px;
    }
    .form-control {
      border-radius: 6px;
    }
    .side-panel {
      text-align: center;
    }
    .side-panel img {
      max-width: 200px;
      margin-bottom: 15px;
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

  <div class="signup-box row">
    <!-- Left form -->
    <div class="col-md-7">
      <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png" 
           alt="Google" class="google-logo">
      <h4 class="text-center">Create your Google Account</h4>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="row g-3 mt-3">
          <div class="col-md-6">
            <input type="text" name="first_name" class="form-control" placeholder="First name" required>
          </div>
          <div class="col-md-6">
            <input type="text" name="last_name" class="form-control" placeholder="Last name" required>
          </div>
          <div class="col-12">
            <input type="email" name="username" class="form-control" placeholder="Your username" required>
            <small class="text-muted">You can use letters, numbers & periods</small>
          </div>
          <div class="col-12">
            <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
            <input type="password" name="confirm" class="form-control" placeholder="Confirm" required>
            <small class="text-muted">Use 8 or more characters with a mix of letters, numbers & symbols</small>
          </div>
          <div class="col-12 d-flex justify-content-between mt-3">
            <a href="#">Sign in instead</a>
            <button type="submit" class="btn btn-primary px-4">Next</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Right side illustration -->
    <div class="col-md-5 side-panel d-flex flex-column justify-content-center">
      <img src="https://ssl.gstatic.com/accounts/signup/glif/account.svg" alt="Account illustration">
      <p>One account. All of Google working for you.</p>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
