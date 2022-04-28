<?php

use app\database\tables\Category;
use app\database\tables\Food;

require_once '../../vendor/autoload.php';
require_once '../../constants.php';

session_start();

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable($ROOT_PATH);
$dotenv->load();

// set up database
$arr_database = [
   'db_dsn' => $_ENV['DB_DSN'],
   'db_user' => $_ENV['DB_USER'],
   'db_password' => $_ENV['DB_PASSWORD']
];

$food_added_message = $_SESSION['food_added'] ?? null;

if (isset($food_added_message)) {
   unset($_SESSION['food_added']);
}

$food_updated_message = $_SESSION['food_updated'] ?? null;

if (isset($food_updated_message)) {
   unset($_SESSION['food_updated']);
}

$food_deleted_message = $_SESSION['food_deleted'] ?? null;

if (isset($food_deleted_message)) {
   unset($_SESSION['food_deleted']);
}

$food = new Food($arr_database);

$category = new Category($arr_database);

$admin_name = $_SESSION['admin_name'] ?? null;

if (!$admin_name) {
   require_once './partials/403_page.php';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_food'])) {

   $search_food = filter_input(INPUT_GET, 'search_food', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $arr_food = $food->searchFood($search_food);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_id_delete'])) {

   $food_id = filter_input(INPUT_POST, 'food_id_delete', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   if (!$food->deleteSingleFood($food_id)) {
      echo 'Error in trying to delete a row from the admin table';
      exit;
   } else {
      $_SESSION['food_deleted'] = "The food {$food_id} has been deleted";
      header('Location:./manage_food.php');
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
         <h2>Manage Food</h2>
         <div class="container-flex-row">
            <a class="btn-add" href="./add_food.php">Add Food</a>
            <form action="" method="get" class="form-search">
               <div class="container-input-search">
                  <input type="text" class="input-search" name="search_food" id="search_food" placeholder="Search food" value="<?php if (isset($search_food)) {
                                                                                                                                    echo $search_food;
                                                                                                                                 } ?>">
                  <button type="submit"><i style="font-size:1.05rem;" class="fas fa-search"></i></button>
               </div>
            </form>
         </div>
         <?php if (isset($arr_food)) : ?>

            <table>
               <tr>
                  <td>Id</td>
                  <td>Title</td>
                  <td>Description</td>
                  <td>Image</td>
                  <td>Price</td>
                  <td>Active</td>
                  <td>category</td>
                  <td>Created_at</td>
                  <td>Actions</td>
               </tr>
               <?php foreach ($arr_food as $key => $value) : ?>
                  <tr>
                     <td><?php echo $value['food_id'] ?></td>
                     <td><?php echo $value['title'] ?></td>
                     <td><?php echo $value['description'] ?></td>

                     <?php if (!$value['image']) : ?>

                        <td><?php echo '<p style=color:red;>image not uploaded</p>' ?></td>

                     <?php else : ?>

                        <td><?php echo "<img src='./uploads/food/$value[image]' width='175px' height='175px'>" ?></td>

                     <?php endif; ?>

                     <td><?php echo $value['price'] ?></td>
                     <td><?php echo $value['active'] ?></td>
                     <td><?php echo $category->fetchSingleCategory($value['category_id'])['title'] ?></td>
                     <td><?php echo $value['created_at'] ?></td>
                     <td>
                        <div class="actions actions-column">
                           <a href="./update/update_food.php?food_id=<?php echo $value['food_id'] ?>" class="btn-update">Update Food</a>
                           <form action="" method="post">
                              <input type="hidden" name="food_id_delete" value="<?php echo $value['food_id'] ?>">
                              <button type="submit" class="btn-delete">Delete Food</button>
                           </form>
                        </div>
                     </td>
                  </tr>
               <?php endforeach; ?>
            </table>

         <?php elseif ($food->fetchFood()) : ?>
            <table>
               <tr>
                  <td>Id</td>
                  <td>Title</td>
                  <td>Description</td>
                  <td>Image</td>
                  <td>Price</td>
                  <td>Active</td>
                  <td>category</td>
                  <td>Created_at</td>
                  <td>Actions</td>
               </tr>
               <?php foreach ($food->fetchFood() as $key => $value) : ?>
                  <tr>
                     <td><?php echo $value['food_id'] ?></td>
                     <td><?php echo $value['title'] ?></td>
                     <td><?php echo $value['description'] ?></td>

                     <?php if (!$value['image']) : ?>

                        <td><?php echo '<p style=color:red;>image not uploaded</p>' ?></td>

                     <?php else : ?>

                        <td><?php echo "<img src='./uploads/food/$value[image]' width='175px' height='175px'>" ?></td>

                     <?php endif; ?>

                     <td><?php echo $value['price'] ?></td>
                     <td><?php echo $value['active'] ?></td>
                     <td><?php echo $category->fetchSingleCategory($value['category_id'])['title'] ?></td>
                     <td><?php echo $value['created_at'] ?></td>
                     <td>
                        <div class="actions actions-column">
                           <a href="./update/update_food.php?food_id=<?php echo $value['food_id'] ?>" class="btn-update">Update Food</a>
                           <form action="" method="post">
                              <input type="hidden" name="food_id_delete" value="<?php echo $value['food_id'] ?>">
                              <button type="submit" class="btn-delete">Delete Food</button>
                           </form>
                        </div>
                     </td>
                  </tr>
               <?php endforeach; ?>
            </table>
         <?php endif; ?>
      </div>
   </div>
   <?php if (isset($food_added_message)) : ?>

      <p class="add-message"><?php echo $food_added_message ?></p>

   <?php elseif (isset($food_updated_message)) : ?>

      <p class="update-message"><?php echo $food_updated_message ?></p>

   <?php elseif ($food_deleted_message) : ?>

      <p class="delete-message"><?php echo $food_deleted_message ?></p>

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