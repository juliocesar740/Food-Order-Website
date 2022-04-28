<?php

use app\database\tables\Order;

require_once '../../vendor/autoload.php';
require_once '../../constants.php';

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

$order_added_message = $_SESSION['order_added'] ?? null;

if (isset($order_added_message)) {
   unset($_SESSION['order_added']);
}

$order_updated_message = $_SESSION['order_updated'] ?? null;

if (isset($order_updated_message)) {
   unset($_SESSION['order_updated']);
}

$order_deleted_message = $_SESSION['order_deleted'] ?? null;

if (isset($order_deleted_message)) {
   unset($_SESSION['order_deleted']);
}

$admin_name = $_SESSION['admin_name'] ?? null;

if (!$admin_name) {
   require_once './partials/403_page.php';
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id_delete'])) {

   $order_id = filter_input(INPUT_POST, 'order_id_delete', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   if (!$order->deleteSingleOrder($order_id)) {
      echo 'Error in trying to delete a row from the admin table';
      exit;
   } else {
      $_SESSION['order_deleted'] = "The order {$order_id} has been deleted";
      header('Location:./manage_order.php');
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
      <div class="dashboard order-page-width">
         <h2>Manage Order</h2>
         <a class="btn-add" href="./add_order.php">Add Order</a>
         <?php if ($order->fetchOrder()) : ?>
            <table>
               <tr>
                  <td>Id</td>
                  <td>Food</td>
                  <td>Price</td>
                  <td>quantity</td>
                  <td>total</td>
                  <td>status</td>
                  <td>Customer name</td>
                  <td>Customer email</td>
                  <td>Customer Address</td>
                  <td>Created_at</td>
                  <td>Actions</td>
               </tr>
               <?php foreach ($order->fetchOrder() as $key => $value) : ?>
                  <tr>
                     <td><?php echo $value['order_id'] ?></td>
                     <td><?php echo $value['food'] ?></td>
                     <td><?php echo $value['price'] ?></td>
                     <td><?php echo $value['quantity'] ?></td>
                     <td><?php echo $value['total'] ?></td>
                     <?php if ($value['status'] === 'Ordered') : ?>
                        <td style="color: gray;font-weight:500;"><?php echo $value['status'] ?></td>
                     <?php elseif ($value['status'] === 'On_delivery') : ?>
                        <td style="color: #F1C40F;font-weight:500;"><?php echo $value['status'] ?></td>
                     <?php elseif ($value['status'] === 'Delivered') : ?>
                        <td style="color: #0CD708;font-weight:500;"><?php echo $value['status'] ?></td>
                     <?php elseif ($value['status'] === 'Canceled') : ?>
                        <td style="color: red;font-weight:500;"><?php echo $value['status'] ?></td>
                     <?php endif; ?>
                     <td><?php echo $value['customer_name'] ?></td>
                     <td><?php echo $value['customer_email'] ?></td>
                     <td><?php echo $value['customer_address'] ?></td>
                     <td><?php echo $value['created_at'] ?></td>
                     <td>
                        <div class="actions actions-column">
                           <a href="./update/update_order.php?order_id=<?php echo $value['order_id'] ?>" class="btn-update">Update Order</a>
                           <form action="" method="post">
                              <input type="hidden" name="order_id_delete" value="<?php echo $value['order_id'] ?>">
                              <button type="submit" class="btn-delete">Delete Order</button>
                           </form>
                        </div>
                     </td>
                  </tr>
               <?php endforeach; ?>
            </table>
         <?php endif; ?>
      </div>
   </div>
   <?php if (isset($order_added_message)) : ?>

      <p class="add-message"><?php echo $order_added_message ?></p>

   <?php elseif (isset($order_updated_message)) : ?>

      <p class="update-message"><?php echo $order_updated_message ?></p>

   <?php elseif ($order_deleted_message) : ?>

      <p class="delete-message"><?php echo $order_deleted_message ?></p>

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