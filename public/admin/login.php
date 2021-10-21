<?php

use app\database\tables\Admin;

require_once '../../vendor/autoload.php';
require_once './functions.php';
require_once '../constants.php';

session_start();

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable($ROOT_PATH);
$dotenv->Load();

// set up database
$admin = new Admin([
   'db_dsn' => $_ENV['DB_DSN'],
   'db_user' => $_ENV['DB_USER'],
   'db_password' => $_ENV['DB_PASSWORD']
]);

if (isset($_SESSION['admin_name'])) {
   header('Location:/admin/index_admin.php');
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fullname']) && isset($_POST['password'])) {

   $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   if (checkLogin($admin, $fullname, $password)) {

      unset($_SESSION['login_error']);

      session_regenerate_id();

      $_SESSION['admin_name'] = $fullname;
      $_SESSION['login_message'] = "Welcome {$_SESSION['admin_name']}";

      header('Location: ./index_admin.php');
      exit();
   } else {
      $_SESSION['login_error'] = 'Name or password not correct. Please try again';
      header('Location:/admin/login.php');
   }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login admin</title>
   <link rel="stylesheet" href="./css/login.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
   <div class="login-container">
      <h1>Admin Login</h1>
      <form class="form" action="" method="post">
         <div class="container-flex-column">
            <?php if (isset($_SESSION['login_error'])) : ?>
               <p class="warnig-message"><?php echo $_SESSION['login_error'] ?></p>
            <?php endif; ?>
            <label for="fullname">Full Name</label>
            <input type="text" class="input" name="fullname" id="fullname" placeholder="Enter your full name">
         </div>

         <div class="container-flex-column">
            <label for="Password">Password</label>
            <input type="password" class="input" name="password" id="password" placeholder="Enter your password">
            <i class="fas fa-eye" id="eye-password"></i>
         </div>

         <?php if (isset($errors) && $errors === true) : ?>
            <button style="margin:15px 0;" type="submit" class="btn-submit btn-pointer">Login</button>
         <?php else : ?>
            <button type="submit" class="btn-submit btn-pointer">Login</button>
         <?php endif; ?>


      </form>
   </div>
   <script>
      const eye_password = document.querySelector("#eye-password");

      eye_password.addEventListener('click', function() {

         if (document.querySelector('#password').type === 'password') {
            this.className = 'fas fa-eye-slash'
            document.querySelector('#password').type = 'text';
         } else if (document.querySelector('#password').type === 'text') {
            this.className = 'fas fa-eye';
            document.querySelector('#password').type = 'password';
         }

      });
   </script>
</body>

</html>