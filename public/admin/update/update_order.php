<?php

use app\database\tables\Order as Order;

require_once '../../../vendor/autoload.php';
require_once '../../../constants.php';

session_start();

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable($ROOT_PATH);
$dotenv->load();

// set up database
$order = new Order([
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

   if (isset($_GET['order_id'])) {

      $order_id = $_GET['order_id'];
      $order_values = $order->fetchSingleOrder($order_id) ?? null;

      if (!$order_values) {
         echo '<b>Database error</b>';
         exit;
      }
   } else {
      echo "<b>Order's id needed to go on with the update of the order in the database</b>";
      exit;
   }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   $errors = array();
   $food_name = filter_input(INPUT_POST, 'food_name', FILTER_SANITIZE_STRING);
   $price = (float) filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
   $quantity = (int) filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_STRING);
   $total = (float) $price * $quantity;
   $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $customer_name = filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_STRING);
   $customer_email = filter_input(INPUT_POST, 'customer_email', FILTER_SANITIZE_EMAIL);
   $customer_address = filter_input(INPUT_POST, 'customer_address', FILTER_SANITIZE_STRING);
   $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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

      $order_update = $order->updateOrder([
         'food' => $food_name,
         'price' => $price,
         'quantity' => $quantity,
         'total' => $total,
         'status' => $status,
         'customer_name' => $customer_name,
         'customer_email' => $customer_email,
         'customer_address' => $customer_address
      ], $order_id);

      if (!$order_update) {
         echo 'Error in trying to insert a row to the order table';
         exit;
      } else {
         $_SESSION['order_updated'] = "The order {$order_id} has been updated";
         header('Location:../manage_order.php');
         exit;
      }
   } else {
      $errors = $order_errors;
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
   <link rel="stylesheet" href="../css/admin.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<?php require_once '../partials/header.php' ?>

<body class="overflow-y-scroll">
   <div class="main">
      <div class="form-container">
         <h2>Update Order</h2>
         <a href="../manage_order.php" class="btn-add">Manage Order</a>
         <form class="form" action="" method="post">
            <div class="container-flex-column">
               <label for="food_name">Food Name</label>
               <?php if (isset($errors['food_name'])) : ?>
                  <input type="text" class="input" name="food_name" id="food_name" placeholder="Enter food name">
                  <p class="warnig-message"><?php echo $errors['food_name'] ?></p>
               <?php else : ?>
                  <input type="text" class="input" name="food_name" id="food_name" placeholder="Enter food name" value="<?php if (isset($food_name)) {echo $food_name;} else {echo $order_values['food'];} ?>">
               <?php endif; ?>
            </div>
            <div class="container-flex-column">
               <label for="price">Price</label>

               <?php if (isset($errors['price'])) : ?>
                  <input class="input" type="text" name="price" id="price" placeholder="Enter price">
                  <p class="warnig-message"><?php echo $errors['price'] ?></p>
               <?php else : ?>
                  <input class="input" type="text" name="price" id="price" placeholder="Enter price" value="<?php if (isset($price)) {echo $price;} else {echo $order_values['price'];} ?>">
               <?php endif; ?>

            </div>
            <div class="container-flex-column">
               <label for="quantity">Quantity</label>

               <?php if (isset($errors['quantity'])) : ?>
                  <input class="input" type="text" name="quantity" id="quantity" placeholder="Enter quantity">
                  <p class="warnig-message"><?php echo $errors['quantity'] ?></p>
               <?php else : ?>
                  <input class="input" type="text" name="quantity" id="quantity" placeholder="Enter quantity" value="<?php if (isset($quantity)) {echo $quantity;} else {echo $order_values['quantity'];} ?>">
               <?php endif; ?>

            </div>
            <div class="container-flex-column">
               <label for="status">Status</label>
               <select class="input" name="status" id="status">
                  <option value="Ordered" <?php if (isset($status) && $status === 'Ordered') {
                                             echo 'selected';
                                          } elseif (isset($order_values['status']) && $order_values['status']  === 'Ordered') {
                                             echo 'selected';
                                          } ?>>Ordered</option>


                  <option value="On_delivery" <?php if (isset($status) && $status === 'On_delivery') {
                                             echo 'selected';
                                          } elseif (isset($order_values['status']) && $order_values['status'] === 'On_delivery') {
                                             echo 'selected';
                                          } ?>>On delivery</option>

                  <option value="Delivered" <?php if (isset($status) && $status === 'Delivered') {
                                             echo 'selected';
                                          } elseif (isset($order_values['status']) && $order_values['status'] === 'Delivered') {
                                             echo 'selected';
                                          } ?>>Delivered</option>
                                             
                  <option value="Canceled" <?php if (isset($status) && $status === 'Canceled') {
                                             echo 'selected';
                                          } elseif (isset($order_values['status']) && $order_values['status'] === 'Canceled') {
                                             echo 'selected';
                                          } ?>>Canceled</option>
               </select>
            </div>
            <div class="container-flex-column">
               <label for="customer_name">Customer Name</label>

               <?php if (isset($errors['customer_name'])) : ?>
                  <input class="input" type="text" name="customer_name" id="customer_name" placeholder="Enter customer name">
                  <p class="warnig-message"><?php echo $errors['customer_name'] ?></p>
               <?php else : ?>
                  <input class="input" type="text" name="customer_name" id="customer_name" placeholder="Enter customer name" value="<?php if (isset($customer_name)) {
                                                                                                                                       echo $customer_name;
                                                                                                                                    } else {
                                                                                                                                       echo $order_values['customer_name'];
                                                                                                                                    } ?>">
               <?php endif; ?>

            </div>
            <div class="container-flex-column">
               <label for="customer_email">Customer email</label>

               <?php if (isset($errors['customer_email'])) : ?>
                  <input class="input" type="text" name="customer_email" id="customer_email" placeholder="Enter customer email" value="<?php if (isset($customer_email)) {
                                                                                                                                          echo $customer_email;
                                                                                                                                       } ?>">
                  <p class="warnig-message"><?php echo $errors['customer_email'] ?></p>
               <?php else : ?>
                  <input class="input" type="text" name="customer_email" id="customer_email" placeholder="Enter customer email" value="<?php if (isset($customer_email)) {
                                                                                                                                          echo $customer_email;
                                                                                                                                       } else {
                                                                                                                                          echo $order_values['customer_email'];
                                                                                                                                       } ?>">
               <?php endif; ?>

            </div>
            <div class="container-flex-column">
               <label for="customer_address">Customer Address</label>
               <?php if (isset($errors['customer_address'])) : ?>
                  <input class="input" type="text" name="customer_address" id="customer_address" placeholder="Enter customer address">
                  <p class="warnig-message"><?php echo $errors['customer_address'] ?></p>
               <?php else : ?>
                  <input class="input" type="text" name="customer_address" id="customer_address" placeholder="Enter customer address" value="<?php if (isset($customer_address)) {
                                                                                                                                                echo $customer_address;
                                                                                                                                             } else {
                                                                                                                                                echo $order_values['customer_address'];
                                                                                                                                             } ?>">
               <?php endif; ?>

            </div>
            <input type="hidden" name="order_id" value="<?php echo $order_id ?>">
            <button type="submit" class="btn-submit">Submit</button>
         </form>
      </div>
   </div>
   <?php require_once '../partials/sidebar.php' ?>
   <script>
      document.querySelector('.close-button').addEventListener('click', function() {
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });

      document.querySelector('#icon-bars').addEventListener('click', function() {
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });
   </script>
</body>