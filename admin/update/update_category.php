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
   require_once '../partials/403_page.php';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

   if (isset($_GET['category_id'])) {

      $category_id = $_GET['category_id'];
      $category_values = $category->fetchSingleCategory($category_id) ?? null;

      if (!$category_values) {
         echo '<b>Database error</b>';
         exit;
      }
   } else {
      echo "<b>Category's id needed to go on with the update of the order in the database</b>";
      exit;
   }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   if (!empty($_FILES) && isset($_FILES['image_upload'])) {

      $image_upload = new ImageUpload($_FILES['image_upload'], 'category');

      if (!$image_upload->upload($_FILES['image_upload'])) {
         echo 'This image already exists or other error happened in the image';
         exit;
      }
   }

   $errors = array();
   $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);
   $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
   $active = filter_input(INPUT_POST, 'active', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   $category_errors = $category->checkCategoryErrors(['title' => $title,'active' => $active,]);

   if (empty($category_errors)) {

      $category_update = $category->updateCategory(['image' => $image, 'title' => $title, 'active' => $active], $category_id);

      if (!$category_update) {
         echo 'Error in trying to insert a row to the category table';
         exit;
      } else {
         $_SESSION['category_updated'] = "The cateogry {$category_id} has been updated";
         header('Location:../manage_category.php');
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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <link rel="stylesheet" href="../css/admin.css">
</head>
<?php require_once '../partials/header.php' ?>

<body class="overflow-y-scroll">
   <div class="main">
      <div class="form-container">
         <h2>Update Category</h2>
         <a href="../manage_category.php" class="btn-add">Manage Category</a>

         <?php if (isset($image)) : ?>
            <img style="display:block;margin: 30px 0px;" src="<?php echo "../uploads/category/$image" ?>"" alt=" <?php echo $image ?>" height="150px" width="150px">
         <?php elseif (isset($category_values['image'])) : ?>
            <img style="display:block;margin: 30px 0px;" src="<?php echo "../uploads/category/$category_values[image]" ?>"" alt=" <?php echo $category_values['image'] ?>" height="150px" width="150px">
         <?php endif; ?>

         <form class="form" action="" method="post" enctype="multipart/form-data">
            <div class="container-flex-column">
               <label for="image">Select Image</label>

               <?php if (isset($image)) : ?>
                  <input type="hidden" name="image" id="image_name" value="<?php echo $image ?>">
               <?php elseif (isset($category_values['image'])) : ?>
                  <input type="hidden" name="image" id="image_name" value="<?php echo $category_values['image'] ?>">
               <?php endif; ?>

               <input class="input-file" type="file" name="" id="image_upload">
               <p id="filename"><?php echo $image ?? $category_values['image'] ?></p>
            </div>
            <div class="container-flex-column">
               <label for="title">Title</label>

               <?php if (isset($title)) : ?>

                  <input type="text" class="input" name="title" id="title" placeholder="Enter title" value="<?php echo $title ?>">

                  <?php if(isset($errors['title'])): ?>
                     <p class="warnig-message"><?php echo $errors['title'] ?></p>
                  <?php endif; ?>   

               <?php elseif (isset($category_values['title'])) : ?>

                  <input type="text" class="input" name="title" id="title" placeholder="Enter title" value="<?php echo $category_values['title'] ?>">

               <?php endif; ?>

            </div>
            <div class="container-input">
               <p style="display: inline-block;">Active: </p>

               <?php if (isset($active)) : ?>

                  <input type="radio" name="active" value="yes" id="active" <?php if ($active === 'yes') {echo 'checked';} ?>>
                  <label for="active">Yes</label>

                  <input type="radio" name="active" value="no" id="active" <?php if ($active === 'no') {echo 'checked';} ?>>
                  <label for="active">No</label>

                  <?php if(isset($errors['active'])): ?>
                     <p class="warnig-message"><?php echo $errors['active'] ?></p>
                  <?php endif; ?> 

               <?php elseif (isset($category_values['active'])) : ?>

                  <input type="radio" name="active" value="yes" id="active" <?php if ($category_values['active'] === 'yes') {echo 'checked';} ?>>
                  <label for="active">Yes</label>

                  <input type="radio" name="active" value="no" id="active" <?php if ($category_values['active'] === 'no') {echo 'checked';} ?>>
                  <label for="active">No</label>

               <?php endif; ?>

            </div>
            <input type="hidden" name="category_id" value="<?php echo $category_id ?>">
            <button type="submit" class="btn-submit">Submit</button>
         </form>
      </div>
   </div>
   <?php require_once '../partials/sidebar.php' ?>
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