<?php

session_start();

use app\database\tables\Category;
use app\database\tables\Food;

require_once '../vendor/autoload.php';
require_once '../constants.php';

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

$order_success_message = $_SESSION['order_sent'] ?? null;

// check if a order was sent
if (isset($order_success_message)) {

   unset($_SESSION['order_sent']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Awesome Foods</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <link rel="stylesheet" href="./css/site_style.css">
</head>

<body>
   <!-- header -->
   <div class="header">
      <div class="nav">
         <div class="container-flex-row">
            <i class="fas fa-bars" id="icon-bars"></i>
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
         </div>
      </div>
   </div>
   <!-- main -->
   <div class="main">
      <div class="search-food">
         <div class="background"></div>
         <div class="container-input-search">
            <form action="./search.php" method="get">
               <input type="text" class="input-search" name="search_food" id="search_food" placeholder="Search foods">
               <button type="submit"><i style="font-size:1.05rem;" class="fas fa-search"></i></button>
            </form>
         </div>
      </div>
      <div class="container-categories">
         <div class="categories">
            <h2 style="text-align: center;padding-bottom:30px;">Explore Categories</h2>
            <div class="categories-options">
               <?php if ($category->fetchCategoryByActive()) : ?>

                  <?php foreach ($category->fetchCategoryByActive() as $key => $value) : ?>

                     <a href="<?php echo "./foods.php?category_id=$value[category_id]&category_name=$value[title]" ?>">
                        <div class="category-card" style=<?php echo "background-image:url('../admin/uploads/category/$value[image]');" ?>>
                           <p><?php echo $value['title'] ?></p>
                        </div>
                     </a>

                  <?php endforeach; ?>

               <?php endif; ?>
            </div>
            <b style="margin-left:45%;padding:10px 0;cursor: pointer;"><a href="./categories.php">See All Categories</a></b>
         </div>
      </div>

      <div class="container-foods">
         <div class="foods">
            <h2 style="text-align: center; padding-top:25px;padding-bottom:8.5px;">Food Menu</h2>
            <div class="foods-options">

               <?php if ($food->fetchFoodByActive()) : ?>

                  <?php foreach ($food->fetchFoodByActive() as $key => $value) : ?>

                     <div class="food-card">
                        <div class="food-image" style=<?php echo "background-image:url('../admin/uploads/food/$value[image]');" ?>></div>
                        <div class="food-info-order">
                           <h4 style="word-wrap:break-word;"><?php echo $value['title'] ?></h4>
                           <p><b><?php echo '$' . $value['price'] ?></b></p>
                           <p class="food-description"><?php echo $value['description'] ?></p>
                           <a href=<?php echo "./order.php?food_id=$value[food_id]" ?> target="_blank" class="btn-order">Order Now</a>
                        </div>
                     </div>

                  <?php endforeach; ?>

               <?php endif; ?>
            </div>
            <!-- <b style="margin-left:45%;padding:10px 0;cursor: pointer;"><a href="./foods.php">See All Foods</a></b> -->
         </div>
      </div>
      <div class="footer">
         <p>All Rights Reserved. Developed by <a href="https://github.com/juliocesar740" target="_blank" style="color: #eb4c0e">Júlio César</a></p>
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
      <?php if (isset($order_success_message) && $order_success_message === true) : ?>
         <p class="success-message">Your order has successfully been sent.</p>
      <?php endif; ?>
   </div>
   <script>
      const success_message = document.querySelector('.success-message') || null;

      if (success_message) {
         document.querySelector('.success-message').classList.toggle('success-message-active');

         setTimeout(() => {
            document.querySelector('.success-message').classList.toggle('success-message-active');
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