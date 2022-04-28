<?php

use app\database\tables\Category as Category;
use app\files_uploads\ImageUpload;

require_once '../../vendor/autoload.php';
require_once '../../constants.php';

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

$admin_name = $_SESSION['admin_name'] ?? null;

if (!$admin_name) {
   require_once './partials/403_page.php';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   $errors = array();

   if (isset($_FILES['image_upload'])) {

      $image_upload = new ImageUpload($_FILES['image_upload'], 'category');

      if (!$image_upload->upload($_FILES['image_upload'])) {
         echo 'This image already exists or other error happened in the image';
         exit;
      }
   }

   $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);
   $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
   $active = filter_input(INPUT_POST, 'active', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? null;

   $category_errors = $category->checkCategoryErrors(['title' => $title,'active' => $active,]);

   if (empty($category_errors)) {

      $category_insert = $category->insertCategory(['image' => $image, 'title' => $title, 'active' => $active]);

      if (!$category_insert) {
         echo 'Error in trying to insert a row to the category table';
         exit;
      } else {
         $_SESSION['category_added'] = 'A new category has been added';
         header('Location:./manage_category.php');
         exit;
      }
   } else {
      $errors = $category_errors;
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
         <h2>Add Category</h2>
         <a href="./manage_category.php" class="btn-add">Manage Category</a>
         <form class="form" action="" method="post" enctype="multipart/form-data">
            <div class="container-flex-column">
               <label for="image">Select Image</label>
               <input type="hidden" name="image" id="image_name">
               <input class="input-file" type="file" name="" id="image_upload">
               <p id="filename"></p>
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