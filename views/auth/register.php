<?php
$meta = "Register an account";
?>

<div class="auth-container">
  <h2>Register an account</h2>
  <form id="register-form" class="auth-form" method="POST">
    <?php include(__DIR__ . '/../errors/alerts.php') ?>
    <input type="text" id="name" name="name" placeholder="Name" value="<?php echo $_POST['name'] ?? null ?>" required>
    <input type="email" id="email" name="email" placeholder="Email" value="<?php echo $_POST['email'] ?? null ?>" required>
    <input type="number" id="phone" name="phone" placeholder="Phone" value="<?php echo $_POST['phone'] ?? null ?>" required>
    <input type="password" id="password" name="password" placeholder="Password" value="<?php echo $_POST['password'] ?? null ?>" required>
    <button type="submit">Register</button>
    <p>Already have an account ? <a href="login">Login</a></p>
  </form>
</div>