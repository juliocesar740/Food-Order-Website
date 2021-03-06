<?php

use app\database\tables\Admin;
use app\database\tables\Category;
use app\database\tables\Food;
use app\database\tables\Order;

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

$admin = new Admin($arr_database);

$category = new Category($arr_database);

$food = new Food($arr_database);

$order = new Order($arr_database);

$arr_order = $order->fetchOrder() ?? null;

$admin_name = $_SESSION['admin_name'] ?? null;

if (!$admin_name) {
   require_once './partials/403_page.php';
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <link rel="stylesheet" href="./css/admin.css">
</head>
<?php require_once './partials/header.php' ?>

<body>
   <div class="main">
      <div class="dashboard">

         <h2 style="color:#0881f1;">Dashboard</h2>
         <p><?php if (isset($admin_name)) {
               echo 'Admin : ' . "<b style=color:#0881f1;>$admin_name</b>";
            } ?></p>

         <div class="data">
            <div class="data-card">
               <p><?php echo $admin->rowsAdmin() ?></p>
               <p>administrators</p>
            </div>
            <div class="data-card">
               <p><?php echo $category->rowsCategory() ?></p>
               <p>Categories</p>
            </div>
            <div class="data-card">
               <p><?php echo $food->rowsFood() ?></p>
               <p>Foods</p>
            </div>
            <div class="data-card">
               <p><?php echo $order->rowsOrder() ?></p>
               <p>Orders</p>
            </div>
            <div class="data-card">
               <p><?php echo '$' . ($order->sumTotal() ?? '0.00') ?></p>
               <p>Revenue Generated</p>
            </div>
         </div>
         <div class="recent-orders">
            <div class="flex-row-container">
               <h2 style="color: #0881f1;">Recent Orders</h2>
               <a href="./manage_order.php" class="btn-add">View all</a>
            </div>
            <?php if ($arr_order) : ?>
               <table class="dashboard-table">
                  <tr>
                     <td><b>Customer name</b></td>
                     <td><b>Food</b></td>
                     <td><b>Price</b></td>
                     <td><b>status</b></td>
                  </tr>

                  <?php foreach ($arr_order as $key => $value) : ?>

                     <tr>
                        <td><?php echo $value['customer_name'] ?></td>
                        <td><?php echo $value['food'] ?></td>
                        <td><?php echo $value['price'] ?></td>

                        <?php if ($value['status'] === 'Ordered') : ?>
                           <td style="color: gray;font-weight:500;"><?php echo $value['status'] ?></td>
                        <?php elseif ($value['status'] === 'On_delivery') : ?>
                           <td style="color: #F1C40F;font-weight:500;"><?php echo $value['status'] ?></td>
                        <?php elseif ($value['status'] === 'Delivered') : ?>
                           <td style="color: #0CD708;font-weight:500;"><?php echo $value['status'] ?></td>
                        <?php elseif ($value['status'] === 'Canceled') : ?>
                           <td style="color: red;font-weight:500;"><?php echo $value['status'] ?></td>
                        <?php endif; ?>

                     </tr>

                  <?php endforeach; ?>

               </table>

            <?php endif; ?>
         </div>
      </div>
   </div>
   <?php if (isset($_SESSION['login_message'])) : ?>
      <p class="success-message"><?php echo $_SESSION['login_message'] ?></p>

      <?php
      // remove all session variables
      unset($_SESSION['login_message']);
      ?>

   <?php endif; ?>
   <?php require_once './partials/sidebar.php' ?>
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