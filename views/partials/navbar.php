<div class="navbar">
  <?php if (isset($_SESSION['user'])): ?>
    <h4> Welcome, <?= $currentUser?->name ?? null; ?></h4>
  <?php endif; ?>
  <a class="href btn-href" href="<?= BASE_PATH . "/logout" ?>">Logout</a>
</div>