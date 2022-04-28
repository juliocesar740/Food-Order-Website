<?php

session_start();

use app\database\tables\Food;
use app\database\tables\Order as Order;

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

$order = new Order($arr_database);

$food = new Food($arr_database);


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['food_id'])) {

   $food_id = filter_input(INPUT_GET, 'food_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $food_arr = $food->fetchSingleFood($food_id);

   if (!$food_arr && !empty($food_arr)) {
      header('Location:./');
      exit;
   }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'], $_POST['food_name'], $_POST['price'], $_POST['quantity'], $_POST['customer_name'], $_POST['customer_email'], $_POST['customer_address'])) {

   $errors = array();
   $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);
   $food_name = filter_input(INPUT_POST, 'food_name', FILTER_SANITIZE_STRING);
   $price = (float) filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
   $quantity = (int) filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_STRING);
   $total = (float) $price * $quantity;
   $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $customer_name = filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_STRING);
   $customer_email = filter_input(INPUT_POST, 'customer_email', FILTER_SANITIZE_EMAIL);
   $customer_address = filter_input(INPUT_POST, 'customer_address', FILTER_SANITIZE_STRING);

   $order_errors = $order->checkOrderErrors(
      [
         'food_name' => $food_name,
         'price' => $price,
         'quantity' => $quantity,
         'total' => $total,
         'status' => $status,
         'customer_name' => $customer_name,
         'customer_email' => $customer_email,
         'customer_address' => $customer_address
      ]
   );

   if (empty($order_errors)) {
      $order_insert = $order->insertOrder([
         'food' => $food_name,
         'price' => $price,
         'quantity' => $quantity,
         'total' => $total,
         'status' => $status,
         'customer_name' => $customer_name,
         'customer_email' => $customer_email,
         'customer_address' => $customer_address
      ]);

      if (!$order_insert) {
         echo 'Error in trying to insert a row to the order table';
         exit;
      } else {
         $_SESSION['order_sent'] = true;
         header('Location:./');
         exit;
      }
   } else {
      $errors = $order_errors;
   }
} else {
   header('Location:./');
   exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Order your food</title>
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
   <div class="main">
      <div class="order-container">
         <form class="form" action="" method="post">
            <h2 style="text-align: left;padding:10px 0;">Fill this form to confirm your order</h2>
            <div class="selected-food">
               <div class="container-food-info">

                  <?php if (isset($image)) : ?>

                     <div class="food-image" style=<?php echo "background-image:url('../admin/uploads/food/$image');margin-left:0px;" ?>></div>
                     <input type="hidden" name="image" value="<?php echo $image ?>">

                  <?php elseif (isset($food_arr['image'])) : ?>

                     <div class="food-image" style=<?php echo "background-image:url('../admin/uploads/food/$food_arr[image]');margin-left:0px;" ?>></div>
                     <input type="hidden" name="image" value="<?php echo $food_arr['image'] ?>">

                  <?php endif; ?>

                  <div class="food-info-order">

                     <!-- food name -->

                     <?php if (isset($food_name)) : ?>

                        <input type="hidden" name="food_name" value="<?php echo $food_name ?>">
                        <h4><?php echo $food_name ?></h4>

                     <?php elseif (isset($food_arr['title'])) : ?>

                        <input type="hidden" name="food_name" value="<?php echo $food_arr['title'] ?>">
                        <h4><?php echo $food_arr['title'] ?></h4>

                     <?php endif; ?>

                     <!-- price -->

                     <?php if (isset($price)) : ?>

                        <input type="hidden" name="price" value="<?php echo $price ?>">
                        <p><b><?php echo '$' . $price ?></b></p>

                     <?php elseif (isset($food_arr['price'])) : ?>

                        <input type="hidden" name="price" value="<?php echo $food_arr['price'] ?>">
                        <p><b><?php echo '$' . $food_arr['price'] ?></b></p>

                     <?php endif; ?>

                     <div class="container-flex-column">

                        <label for="quantity">Quantity</label>

                        <?php if (isset($errors['quantity'])) : ?>

                           <input class="input" type="number" name="quantity" id="quantity" placeholder="Enter quantity" min="1">
                           <p class="warnig-message"><?php echo $errors['quantity'] ?></p>

                        <?php else : ?>
                           <input class="input" type="number" name="quantity" id="quantity" placeholder="Enter quantity" min="1" value="<?php if (isset($quantity)) {
                                                                                                                                             echo $quantity;
                                                                                                                                          } ?>">
                        <?php endif; ?>

                     </div>
                  </div>
               </div>
            </div>
            <div class="container-flex-column" id="input-quantity">

            </div>
            <div class="container-flex-column">
               <label for="customer_name">Your Name</label>
               <?php if (isset($errors['customer_name'])) : ?>
                  <input class="input" type="text" name="customer_name" id="customer_name" placeholder="Enter your name">
                  <p class="warnig-message"><?php echo $errors['customer_name'] ?></p>
               <?php else : ?>
                  <input class="input" type="text" name="customer_name" id="customer_name" placeholder="Enter your name" value="<?php if (isset($customer_name)) {
                                                                                                                                    echo $customer_name;
                                                                                                                                 } ?>">
               <?php endif; ?>

            </div>

            <div class="container-flex-column">
               <label for="customer_email">Your Email</label>

               <?php if (isset($errors['customer_email'])) : ?>
                  <input class="input" type="text" name="customer_email" id="customer_email" placeholder="Enter your email" value="<?php if (isset($customer_email)) {
                                                                                                                                       echo $customer_email;
                                                                                                                                    } ?>">
                  <p class="warnig-message"><?php echo $errors['customer_email'] ?></p>
               <?php else : ?>
                  <input class="input" type="text" name="customer_email" id="customer_email" placeholder="Enter your email" value="<?php if (isset($customer_email)) {
                                                                                                                                       echo $customer_email;
                                                                                                                                    } ?>">
               <?php endif; ?>
            </div>

            <div class="container-flex-column">
               <label for="customer_address">Address</label>

               <?php if (isset($errors['customer_address'])) : ?>
                  <input class="input" type="text" name="customer_address" id="customer_address" placeholder="Enter your address">
                  <p class="warnig-message"><?php echo $errors['customer_address'] ?></p>
               <?php else : ?>
                  <input class="input" type="text" name="customer_address" id="customer_address" placeholder="Enter your address" value="<?php if (isset($customer_address)) {
                                                                                                                                             echo $customer_address;
                                                                                                                                          } ?>">
               <?php endif; ?>

            </div>
            <input type="hidden" name="status" value="Ordered">
            <button type="submit" class="btn-submit">Send Message</button>
         </form>
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