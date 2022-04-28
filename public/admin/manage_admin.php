<?php

use app\database\tables\Admin as Admin;

require_once '../../vendor/autoload.php';
require_once '../../constants.php';

session_start();

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable($ROOT_PATH);
$dotenv->load();

// set up database
$admin = new Admin([
   'db_dsn' => $_ENV['DB_DSN'],
   'db_user' => $_ENV['DB_USER'],
   'db_password' => $_ENV['DB_PASSWORD']
]);

$admin_added_message = $_SESSION['admin_added'] ?? null;

if (isset($admin_added_message)) {
   unset($_SESSION['admin_added']);
}

$admin_updated_message = $_SESSION['admin_updated'] ?? null;

if (isset($admin_updated_message)) {
   unset($_SESSION['admin_updated']);
}

$admin_deleted_message = $_SESSION['admin_deleted'] ?? null;

if (isset($admin_deleted_message)) {
   unset($_SESSION['admin_deleted']);
}

$admin_name = $_SESSION['admin_name'] ?? null;

if (!$admin_name) {
   require_once './partials/403_page.php';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_admin'])) {

   $search_admin = filter_input(INPUT_GET, 'search_admin', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $arr_admin = $admin->searchAdmin($search_admin);
} 
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id_delete'])) {

   $admin_id = filter_input(INPUT_POST, 'admin_id_delete', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   if (!$admin->deleteSingleAdmin($admin_id)) {
      echo 'Error in trying to delete a row from the admin table';
      exit;
   } else {
      $_SESSION['admin_deleted'] = "The admin {$admin_id} has been deleted";
      header('Location:./manage_admin.php');
   }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Restaurant Website</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <link rel="stylesheet" href="./css/admin.css">
</head>
<?php require_once './partials/header.php' ?>

<body class="overflow-y-scroll">
   <div class="main">
      <div class="dashboard">
         <h2>Manage Admin</h2>
         <div class="container-flex-row">
            <a class="btn-add" href="./add_admin.php">Add Admin</a>
            <form action="" method="get" class="form-search">
               <div class="container-input-search">
                  <input type="text" class="input-search" name="search_admin" id="search_admin" placeholder="Search admin" value="<?php if (isset($search_admin)) {echo $search_admin;} ?>">
                  <button type="submit"><i style="font-size:1.05rem;" class="fas fa-search"></i></button>
               </div>
            </form>
         </div>
         <?php if (isset($arr_admin)) : ?>

            <table>
               <tr>
                  <td>Id</td>
                  <td>Full Name</td>
                  <td>Created_at</td>
                  <td>Actions</td>
               </tr>
               <?php foreach ($arr_admin as $key => $value) : ?>
                  <tr>
                     <td><?php echo $value['admin_id'] ?></td>
                     <td><?php echo $value['name'] ?></td>
                     <td><?php echo $value['created_at'] ?></td>
                     <td>
                        <div class="actions actions-column">
                           <a href="./update/update_admin.php?admin_id=<?php echo $value['admin_id'] ?>" class="btn-update">Update Admin</a>
                           <form action="" method="post">
                              <input type="hidden" name="admin_id_delete" value="<?php echo $value['admin_id'] ?>">
                              <button type="submit" class="btn-delete">Delete Admin</button>
                           </form>
                        </div>
                     </td>
                  </tr>
               <?php endforeach; ?>
            </table>

         <?php elseif ($admin->fetchAdmin()) : ?>
            <table>
               <tr>
                  <td>Id</td>
                  <td>Full Name</td>
                  <td>Created_at</td>
                  <td>Actions</td>
               </tr>
               <?php foreach ($admin->fetchAdmin() as $key => $value) : ?>
                  <tr>
                     <td><?php echo $value['admin_id'] ?></td>
                     <td><?php echo $value['name'] ?></td>
                     <td><?php echo $value['created_at'] ?></td>
                     <td>
                        <div class="actions actions-column">
                           <a href="./update/update_admin.php?admin_id=<?php echo $value['admin_id'] ?>" class="btn-update">Update Admin</a>
                           <form action="" method="post">
                              <input type="hidden" name="admin_id_delete" value="<?php echo $value['admin_id'] ?>">
                              <button type="submit" class="btn-delete">Delete Admin</button>
                           </form>
                        </div>
                     </td>
                  </tr>
               <?php endforeach; ?>
            </table>
         <?php endif; ?>
      </div>
   </div>
   <?php if (isset($admin_added_message)) : ?>

      <p class="add-message"><?php echo $admin_added_message ?></p>

   <?php elseif (isset($admin_updated_message)) : ?>

      <p class="update-message"><?php echo $admin_updated_message ?></p>

   <?php elseif ($admin_deleted_message) : ?>

      <p class="delete-message"><?php echo $admin_deleted_message ?></p>

   <?php endif; ?>
   <?php require_once './partials/sidebar.php' ?>
   <script>
      const add_message = document.querySelector('.add-message') || null;
      const update_message = document.querySelector('.update-message') || null;
      const delete_message = document.querySelector('.delete-message') || null;

      if (add_message) {
         add_message.classList.toggle('add-message-active');

         setTimeout(() => {
            add_message.classList.toggle('add-message-active');
         }, 3500);
      }

      if (update_message) {
         update_message.classList.toggle('update-message-active');

         setTimeout(() => {
            update_message.classList.toggle('update-message-active');
         }, 3500);
      }

      if (delete_message) {
         delete_message.classList.toggle('delete-message-active');

         setTimeout(() => {
            delete_message.classList.toggle('delete-message-active');
         }, 3500);
      }

      document.querySelector('.close-button').addEventListener('click', function() {
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });

      document.querySelector('#icon-bars').addEventListener('click', function() {
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });

   </script>
</body>

</html>