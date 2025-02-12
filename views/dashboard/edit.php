<style>
  .auth-container {
    max-width: 600px !important;
    padding: 20px;
  }
</style>
<div class="auth-container">


  <div class="header">
    <?php
    require_once(__DIR__ . "/../../views/partials/navbar.php");
    ?>
    <?php include(__DIR__ . '/../errors/alerts.php') ?>
    <?php if (!$user): ?>
      <h4 style="color:#721c24;">
        Customer not found
      </h4>
    <?php else: ?>
      <h2>Edit Customer <?= $user?->name ?? null ?></h2>
    <?php endif; ?>
    <a href="<?= BASE_PATH . "/dashboard" ?>">Back</a>
  </div>
  <?php if ($user):
    $cv_path = $user->cv ?  BASE_PATH . "/assets/uploads/cvs/$user->cv" : "";
  ?>

    <form id="customer-form" method="post" enctype="multipart/form-data">
      <label>Name</label>
      <input type="hidden" name="id" value="<?= $user?->id ?? null ?>">
      <input type="text" name="name" value="<?= $user?->name ?? null ?>" placeholder="Name" required>
      <label>Email</label>
      <input type="email" name="email" value="<?= $user?->email ?? null ?>" placeholder="Email" required>
      <label>Phone</label>
      <input type="text" name="phone" value="<?= $user?->phone ?? null ?>" placeholder="Phone" required>
      <label>CV <small>(pdf,doc,docx allowed)</small></label>
      <small><?= $user?->cv ? "<a style='color:teal' class='href' href='$cv_path' target='_blank'>View</a>" : null ?></small>
      <input type="file" name="cv" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
      <button type="submit">Update Customer</button>
    </form>
  <?php endif; ?>
</div>