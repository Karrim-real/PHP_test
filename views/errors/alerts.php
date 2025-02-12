<?php
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<?php
if (isset($error)): ?>
  <div class='alert alert-danger'>
    <?php echo $error ?>
    <span class='alert-close'>x</span>
  </div>
<?php
endif;
?>

<?php

if (isset($success)): ?>
  <div class='alert alert-success'>
    <?php echo $success ?>
    <span class='alert-close'>x</span>
  </div>
<?php
endif;
?>