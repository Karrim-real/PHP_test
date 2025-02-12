<style>
  .auth-container {
    max-width: 600px !important;
    padding: 20px;
  }
</style>
<div class="auth-container">
  <?php
  require_once(__DIR__ . "/../../views/partials/navbar.php");
  ?>
  <?php include(__DIR__ . '/../errors/alerts.php') ?>
  <div class="header">
    <h2>Add New Customer</h2>
    <a href="<?= BASE_PATH . "/dashboard" ?>">Back</a>
  </div>
  <form id="customer-form" enctype="multipart/form-data" method="POST">
    <?php include(__DIR__ . '/../errors/alerts.php') ?>
    <label> Name </label>
    <input type="text" id="name" name="name" placeholder="Name" value="<?php echo $_POST['name'] ?? null ?>" required>
    <label>Email</label>
    <input type="email" id="email" name="email" placeholder="Email" value="<?php echo $_POST['email'] ?? null ?>" required>
    <label>Phone</label>
    <input type="number" id="phone" name="phone" placeholder="Phone" value="<?php echo $_POST['phone'] ?? null ?>" required>
    <label>Password</label>
    <input type="password" id="password" name="password" placeholder="Password" value="<?php echo $_POST['password'] ?? null ?>" required>
    <label>CV <small>(pdf,doc,docx allowed)</small></label>
    <input type="file" name="cv" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
    <button type="submit">Add Customer</button>
  </form>
</div>