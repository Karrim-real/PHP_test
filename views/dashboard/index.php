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
    <h2>Customer Records</h2>
    <a href="<?= BASE_PATH . "/dashboard/create" ?>">New Customer</a>
  </div>
  <?php
  if (count($users) == 0):

    echo "<h4 style='color:#721c24;text-center'>
          No customers found
        </h4>";
  endif;
  ?>
  <div style="overflow:auto;width:100%">
    <table id="customer-table" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>CV</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Customers will be loaded here -->
        <?php
        if (count($users) > 0):
          $counter = 0;

          foreach ($users as $user):
            $cv_path = $user->cv ?  BASE_PATH . "/assets/uploads/cvs/$user->cv" : "";
        ?>
            <tr>
              <td><?php echo ++$counter ?></td>
              <td><?php echo $user->name ?></td>
              <td><?php echo $user->email ?></td>
              <td><?php echo $user->phone ?></td>
              <td><?php echo $user->cv ? "<a style='color:teal' class='href' href='$cv_path' target='_blank'>View</a>" : 'none' ?></td>
              <td style="display: flex;align-items: center;">
                <a style="color:teal" class="href" href="<?= BASE_PATH . "/dashboard/edit/$user->id" ?>">Edit</a>
                <form method="POST" onsubmit="return window.confirm('Are you sure you want to delete this user')" action="<?= BASE_PATH . "/dashboard/delete/$user->id" ?>" style="display:inline-block">
                  <button type="submit" style=" color:red" class="href">Delete</button>
                </form>
              </td>
            </tr>
        <?php
          endforeach;
        endif;
        ?>
      </tbody>
    </table>
  </div>

</div>