<?php

use app\database\tables\Food;

require_once '../vendor/autoload.php';
require_once './constants.php';

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable($ROOT_PATH);
$dotenv->load();

// set up database
$food = new Food([
   'db_dsn' => $_ENV['DB_DSN'],
   'db_user' => $_ENV['DB_USER'],
   'db_password' => $_ENV['DB_PASSWORD']
]);

if (isset($_GET['search_food'])) {

   $search_food = filter_input(INPUT_GET, 'search_food', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $search_array = $food->fetchFoodByTitle($search_food);
   $number_search = count($search_array);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Categories</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <link rel="stylesheet" href="./css/site_style.css">
</head>

<body>
   <!-- header -->
   <div class="header">
      <div class="nav">
         <div class="container-flex-row">
            <div class="logo">
               <i class="fas fa-utensils" style="font-size: 1.35rem;color: #eb4c0e;"></i>
               <h3 style="display: inline-block;color: #eb4c0e;">Awesome Foods</h3>
            </div>
            <ul>
               <li><a href="./index.php">Home</a></li>
               <li><a href="./categories.php">Categories</a></li>
               <li><a href="./foods.php">Foods</a></li>
               <li><a href="./contact.php">Contact</a></li>
            </ul>
            <i class="fas fa-bars" id="icon-bars"></i>
         </div>
      </div>
   </div>
   <!-- main -->
   <div class="main">
      <div class="search-food">
         <div class="background"></div>
         <div class="container-input-search">
            <form action="" method="get">
               <input type="text" class="input-search" name="search_food" id="search_food" placeholder="Search foods">
               <button type="submit"><i style="font-size:1.05rem;" class="fas fa-search"></i></button>
            </form>
         </div>
      </div>

      <?php if (isset($search_array) && !empty($search_array)) : ?>
         <div class="container-foods">
            <div class="foods-margin-0-auto">

               <h2 style="text-align: center; padding-top:25px;padding-bottom:5px;">Food Menu</h2>

               <?php if ($number_search === 1) : ?>

                  <p class="results-message"><?php echo "$number_search result was found" ?></p>

               <?php elseif ($number_search > 1) : ?>

                  <p class="results-message"><?php echo "$number_search results were found" ?></p>

               <?php endif; ?>

               <div class="foods-options">

                  <?php foreach ($search_array as $key => $value) : ?>

                     <div class="food-card">
                        <div class="food-image" style=<?php echo "background-image:url('../admin/uploads/food/$value[image]');" ?>></div>
                        <div class="food-info-order">
                           <h4><?php echo $value['title'] ?></h4>
                           <p><b><?php echo '$' . $value['price'] ?></b></p>
                           <p class="food-description"><?php echo $value['description'] ?></p>
                           <a href=<?php echo "./order.php?food_id=$value[food_id]" ?> target="_blank" class="btn-order">Order Now</a>
                        </div>
                     </div>

                  <?php endforeach ?>
               </div>
            </div>
         </div>
      <?php elseif (isset($search_array) && empty($search_array)) : ?>

         <div style="height: 195px;display:flex;justify-content:center;align-items:center;">
            <p class="no-results-message">No results found</p>
         </div>

      <?php endif; ?>

      <div class="footer">
         <p>All Rights Reserved. Developed by <a href="https://github.com/juliocesar740" target="_blank" style="color: #eb4c0e">Júlio César</a></p>
      </div>
   </div>
   <div class="sidebar-nav">
      <ul>
         <li><a href="./index.php">Home</a></li>
         <li><a href="./categories.php">Categories</a></li>
         <li><a href="./foods.php">Foods</a></li>
         <li><a href="./contact.php">Contact</a></li>
      </ul>
      <div class="close-button">x</div>
   </div>
   <script>
      document.querySelector('.close-button').addEventListener('click', function() {
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });

      document.querySelector('#icon-bars').addEventListener('click', function() {
         // console.log('a');
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });
   </script>
</body>

</html>