<?php

use app\database\tables\Category;
use app\database\tables\Food;
use app\files_uploads\ImageUpload;

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

$category = new Category($arr_database);

$food = new Food($arr_database);

$admin_name = $_SESSION['admin_name'] ?? null;

if (!$admin_name) {
   require_once './partials/403_page.php';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   $errors = array();

   if (isset($_FILES['image_upload'])) {

      $image_upload = new ImageUpload($_FILES['image_upload'], 'food');

      if (!$image_upload->upload($_FILES['image_upload'])) {
         echo 'This image already exists or other error happened in the image';
         exit;
      }
   }

   $errors = array();
   $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
   $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
   $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
   $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);
   $active = filter_input(INPUT_POST, 'active', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'error';
   $food_category = $category->fetchSingleCategoryByTitle(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
   $category_id = $food_category['category_id'] ?? 'error';
   $category_title = $food_category['title'] ?? 'error';

   $food_errors = $food->checkFoodErrors(
      [
         'title' => $title,
         'description' => $description,
         'price' => $price,
         'active' => $active,
         'category_id' => $food_category
      ]
   );

   if (empty($food_errors)) {

      $food_insert = $food->insertFood([
         'title' => $title,
         'description' => $description,
         'price' => $price,
         'image' => $image,
         'active' => $active,
         'category_id' => $category_id
      ]);

      if (!$food_insert) {
         echo 'Error in trying to insert a row to the food table';
         exit;
      } else {
         $_SESSION['food_added'] = 'A new food has been added';
         header('Location:./manage_food.php');
         exit;
      }
   } else {
      $errors = $food_errors;
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
   <link rel="stylesheet" href="./css/admin.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<?php require_once './partials/header.php' ?>

<body class="overflow-y-scroll">
   <div class="main">
      <div class="form-container">
         <h2>Add Food</h2>
         <a href="./manage_food.php" class="btn-add">Manage Food</a>
         <form class="form" action="" method="post" enctype="multipart/form-data">
            <div class="container-flex-column">
               <label for="image">Select Image</label>
               <input type="hidden" name="image" id="image_name">
               <input class="input-file" type="file" name="" id="image_upload">
               <p id="filename"></p>
            </div>
            <div class="container-flex-column">

               <?php if ($category->fetchCategory()) : ?>

                  <label for="category">Category</label>
                  <select class="input" name="category" id="category">

                     <option value="none">Select a category</option>

                     <?php foreach ($category->fetchCategory() as $key => $value) : ?>

                        <option value="<?php echo $value['title'] ?>"><?php echo $value['title'] ?></option>

                     <?php endforeach; ?>

                  </select>
                  <?php if (isset($errors['category_id'])) : ?>
                     <p class="warnig-message"><?php echo $errors['category_id'] ?></p>
                  <?php endif; ?>

               <?php endif; ?>
            </div>
            <div class="container-flex-column">
               <label for="title">Title</label>

               <?php if (isset($errors['title'])) : ?>
                  <input type="text" class="input" name="title" id="title" placeholder="Enter title">
                  <p class="warnig-message"><?php echo $errors['title'] ?></p>
               <?php else : ?>
                  <input type="text" class="input" name="title" id="title" placeholder="Enter title" value="<?php if (isset($title)) {echo $title;} ?>">
               <?php endif; ?>
            </div>
            <div class="container-flex-column">
               <label for="description">Description</label>

               <?php if (isset($errors['description'])) : ?>
                  <textarea class="textarea-description" name="description" id="description" placeholder="Enter description"></textarea>
                  <p class="warnig-message"><?php echo $errors['description'] ?></p>
               <?php else : ?>
                  <textarea class="textarea-description" name="description" id="description" placeholder="Enter description"><?php if (isset($description)) {echo $description;} ?></textarea>
               <?php endif; ?>
            </div>
            <div class="container-flex-column">
               <label for="price">Price</label>

               <?php if (isset($errors['price'])) : ?>
                  <input type="text" class="input" name="price" id="price" placeholder="Enter price">
                  <p style="color: red;"><?php echo $errors['price'] ?></p>
               <?php else : ?>
                  <input type="text" class="input" name="price" id="price" placeholder="Enter price" value="<?php if (isset($price)) {echo $price;} ?>">
               <?php endif; ?>
            </div>
            <div class="container-input">

               <p style="display: inline-block;">Active: </p>

               <?php if (isset($errors['active'])) : ?>

                  <input type="radio" name="active" id="active" value="yes">
                  <label for="active">Yes</label>
                  <input type="radio" name="active" id="active" value="no">
                  <label for="active">No</label>
                  <p class="warnig-message"><?php echo $errors['active'] ?></p>

               <?php else : ?>

                  <input type="radio" name="active" id="active" value="yes" <?php if (isset($active) && $active === 'yes') {echo 'checked';} ?>>
                  <label for="active">Yes</label>
                  <input type="radio" name="active" id="active" value="no" <?php if (isset($active) && $active === 'no') {echo 'checked';} ?>>
                  <label for="active">No</label>

               <?php endif; ?>

            </div>
            <button type="submit" class="btn-submit">Submit</button>
         </form>
      </div>
   </div>
   <?php require_once './partials/sidebar.php' ?>
   <script>

      document.querySelector('#image_upload').addEventListener('input', function() {
         this.name = 'image_upload';
         document.querySelector('#image_name').value = this.files[0]['name'];
         document.querySelector('#filename').textContent = this.files[0]['name'];
      });

      document.querySelector('.close-button').addEventListener('click', function() {
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });

      document.querySelector('#icon-bars').addEventListener('click', function() {
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });

   </script>
</body>