<?php

use app\database\tables\Category as Category;

require_once '../../vendor/autoload.php';
require_once '../constants.php';

session_start();

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable($ROOT_PATH);
$dotenv->load();

// set up database
$category = new Category([
   'db_dsn' => $_ENV['DB_DSN'],
   'db_user' => $_ENV['DB_USER'],
   'db_password' => $_ENV['DB_PASSWORD']
]);

$category_added_message = $_SESSION['category_added'] ?? null;

if (isset($category_added_message)) {
   unset($_SESSION['category_added']);
}

$category_updated_message = $_SESSION['category_updated'] ?? null;

if (isset($category_updated_message)) {
   unset($_SESSION['category_updated']);
}

$category_deleted_message = $_SESSION['category_deleted'] ?? null;

if (isset($category_deleted_message)) {
   unset($_SESSION['category_deleted']);
}


$admin_name = $_SESSION['admin_name'] ?? null;

if (!$admin_name) {
   require_once './partials/403_page.php';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_category'])) {

   $search_category = filter_input(INPUT_GET, 'search_category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $arr_category = $category->searchCategory($search_category);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id_delete'])) {

   $category_id = filter_input(INPUT_POST, 'category_id_delete', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   if (!$category->deleteSingleCategory($category_id)) {
      echo 'Error in trying to delete a row from the admin table';
      exit;
   } else {
      $_SESSION['category_deleted'] = "The category {$category_id} has been deleted";
      header('Location:./manage_category.php');
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
         <h2>Manage Category</h2>
         <div class="container-flex-row">
            <a class="btn-add" href="./add_category.php">Add Category</a>
            <form action="" method="get" style="margin-right: 20px;">
               <div class="container-input-search">
                  <input type="text" class="input-search" name="search_category" id="search_category" placeholder="Search Category" value="<?php if (isset($search_category)) {echo $search_category;} ?>">
                  <button type="submit"><i style="font-size:1.05rem;" class="fas fa-search"></i></button>
               </div>
            </form>
         </div>

         <?php if (isset($arr_category)) : ?>
            <table>
               <tr>
                  <td>Id</td>
                  <td>Title</td>
                  <td>Image</td>
                  <td>Active</td>
                  <td>Created_at</td>
                  <td>Actions</td>
               </tr>
               <?php foreach ($arr_category as $key => $value) : ?>
                  <tr>
                     <td><?php echo $value['category_id'] ?></td>
                     <td><?php echo $value['title'] ?></td>
                     <td><?php if (!$value['image']) {
                              echo '<p style=color:red;>image not uploaded</p>';
                           } else {
                              echo "<img src='./uploads/category/$value[image]' width='175px' height='175px'>";
                           } ?></td>
                     <td><?php echo $value['active'] ?></td>
                     <td><?php echo $value['created_at'] ?></td>
                     <td>
                        <div class="actions actions-column">
                           <a href="./update/update_category.php?category_id=<?php echo $value['category_id'] ?>" class="btn-update">Update Category</a>
                           <form action="" method="post">
                              <input type="hidden" name="category_id_delete" value="<?php echo $value['category_id'] ?>">
                              <button type="submit" class="btn-delete">Delete Category</button>
                           </form>
                        </div>
                     </td>
                  </tr>
               <?php endforeach; ?>
            </table>

         <?php elseif ($category->fetchCategory()) : ?>
            <table>
               <tr>
                  <td>Id</td>
                  <td>Title</td>
                  <td>Image</td>
                  <td>Active</td>
                  <td>Created_at</td>
                  <td>Actions</td>
               </tr>
               <?php foreach ($category->fetchCategory() as $key => $value) : ?>
                  <tr>
                     <td><?php echo $value['category_id'] ?></td>
                     <td><?php echo $value['title'] ?></td>
                     <td><?php if (!$value['image']) {
                              echo '<p style=color:red;>image not uploaded</p>';
                           } else {
                              echo "<img src='./uploads/category/$value[image]' width='175px' height='175px'>";
                           } ?></td>
                     <td><?php echo $value['active'] ?></td>
                     <td><?php echo $value['created_at'] ?></td>
                     <td>
                        <div class="actions actions-column">
                           <a href="./update/update_category.php?category_id=<?php echo $value['category_id'] ?>" class="btn-update">Update Category</a>
                           <form action="" method="post">
                              <input type="hidden" name="category_id_delete" value="<?php echo $value['category_id'] ?>">
                              <button type="submit" class="btn-delete">Delete Category</button>
                           </form>
                        </div>
                     </td>
                  </tr>
               <?php endforeach; ?>
            </table>
         <?php endif; ?>
      </div>
   </div>
   <?php if (isset($category_added_message)) : ?>

      <p class="add-message"><?php echo $category_added_message ?></p>

   <?php elseif (isset($category_updated_message)) : ?>

      <p class="update-message"><?php echo $category_updated_message ?></p>

   <?php elseif ($category_deleted_message) : ?>

      <p class="delete-message"><?php echo $category_deleted_message ?></p>

   <?php endif; ?>
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
   </script>
</body>

</html>