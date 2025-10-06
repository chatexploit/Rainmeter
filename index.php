<?php
session_start();
include 'config.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (isset($_POST['register'])) {
        if (strlen($username) < 3 || strlen($password) < 4) {
            $err = "Username must be at least 3 chars and password at least 4 chars.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            if ($stmt->execute()) {
                $err = "Registered successfully. You can login now.";
            } else {
                $err = "Registration failed (username may exist).";
            }
        }
    }

    if (isset($_POST['login'])) {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash);
        if ($stmt->fetch() && password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            header("Location: dashboard.php");
            exit;
        } else {
            $err = "Invalid login!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>RainMeter - Login</title>
<link rel="stylesheet" href="assets/style.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body class="page-login">
  <main class="center-box">
    <div class="card login-card">
      <img src="assets/my_logo.png" class="logo" alt="RainMeter logo">
      <h1>RainMeter</h1>
      <?php if($err): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="POST" class="form">
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <div class="row">
          <button name="login" class="btn">Login</button>
          <button name="register" class="btn alt" type="submit">Register</button>
        </div>
      </form>
      <p class="muted">Upload old data via CSV on the Import page after you login.</p>
    </div>
  </main>
</body>
</html>
