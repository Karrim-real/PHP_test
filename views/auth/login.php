<?php
$meta = "Login in to your account";
?>

<div class="auth-container">
  <h2>Login in to your account</h2>

  <form id="login-form" class="auth-form" method="POST" action="login">
    <?php include(__DIR__ . '/../errors/alerts.php') ?>
    <input type="email" id="email" name="email" value="<?= $_POST['email'] ?? null ?>" placeholder="Email" required>
    <input type="password" id="password" name="password" value="<?= $_POST['password'] ?? null ?>" placeholder="Password" required>
    <button type="submit">Login</button>
    <p>Don't have an account? <a href="register">Register</a></p>
  </form>
</div>