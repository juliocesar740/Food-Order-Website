<?php

use app\database\tables\Admin;

require_once '../../vendor/autoload.php';
require_once '../../functions.php';
require_once '../../constants.php';

session_start();

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable($ROOT_PATH);
$dotenv->load();

// set up database
$admin = new Admin([
   'db_dsn' => $_ENV['DB_DSN'],
   'db_user' => $_ENV['DB_USER'],
   'db_password' => $_ENV['DB_PASSWORD']
]);


if (isset($_POST['fullname']) && isset($_POST['password'])) {

   $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
   $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

   if (checkAdminRegistration($fullname, $password)) {

      $password = password_hash(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);

      $admin_insert = $admin->insertAdmin(['name' => $fullname, 'password' => $password]);

      if (!$admin_insert) {
         echo 'Error in trying to insert a row to the admin table';
         exit;
      } 
      else {
         
         unset($_SESSION['login_error']);

         session_regenerate_id();
   
         $_SESSION['admin_name'] = $fullname;
         $_SESSION['login_message'] = "Welcome {$_SESSION['admin_name']}";
   
         header('Location: ./index_admin.php');
         exit();

         $_SESSION['admin_added'] = 'A new administrator has been added';
         header('Location:./manage_admin.php');
         exit;
      }
   } else {
      $_SESSION['register_admin_error'] = 'name or password incorrect.Try again';
      header('Location: /admin/add_admin.php');
      exit;
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
   <link rel="stylesheet" href="./css/login.css">
</head>

<body>
   <div class="main">
      <div class="login-container">
         <h1>Create admin</h1>
         <form class="form" action="" method="post">
            <div class="container-flex-column">
               <label for="fullname">Full Name</label>
               <input type="text" class="input" name="fullname" id="fullname" placeholder="Enter full name">
               <p style="color: red;" id="username_error"></p>
            </div>
            <div class="container-flex-column">
               <label for="Password">Password</label>
               <div style="position:relative;">
                  <input type="password" class="input" name="password" id="password" placeholder="Enter password">
                  <i class="fas fa-eye" id="eye-password"></i>
               </div>
               <p style="color: red;" id="password_error"></p>
            </div>
            <button style="cursor: not-allowed;" type="submit" class="btn-submit" data-username_check="" data-password_check="" disabled>Submit</button>
         </form>
      </div>
   </div>
   <script>
      const eye_password = document.querySelector("#eye-password");
      const btn_submit = document.querySelector('.btn-submit');

      document.querySelector('#fullname').addEventListener('input', function() {

         if (this.value.length < 3 || this.value.length > 25) {

            document.querySelector('#username_error').textContent = 'The username must be between 3-25 characteres.';
            btn_submit.dataset.username_check = 'false';

            if (!checkBtnSubmit(btn_submit)) {
               btn_submit.disabled = true;
               btn_submit.style.cursor = 'not-allowed';
            }

         } else {

            document.querySelector('#username_error').textContent = '';
            btn_submit.dataset.username_check = 'true';

            if (checkBtnSubmit(btn_submit)) {
               btn_submit.disabled = false;
               btn_submit.style.cursor = 'pointer';
            }
         }
      });

      document.querySelector('#password').addEventListener('input', function() {

         const pattern_digits = /(?=.{2,}[0-9])/g;
         const pattern_lowercase = /(?=.{2,}[a-z])/g;
         const pattern_uppercase = /(?=.{2,}[A-Z])/g;

         if (this.value.length < 8) {
            document.querySelector('#password_error').textContent = 'The password should have at least 8 characters';
            btn_submit.dataset.password_check = 'false';

            if (!checkBtnSubmit(btn_submit)) {
               btn_submit.disabled = true;
               btn_submit.style.cursor = 'not-allowed';
            }

         } else if (this.value.length > 21) {
            document.querySelector('#password_error').textContent = 'The password is too long';
            btn_submit.dataset.password_check = 'false';

            if (!checkBtnSubmit(btn_submit)) {
               btn_submit.disabled = true;
               btn_submit.style.cursor = 'not-allowed';;
            }

         } else if (!pattern_digits.test(this.value)) {
            document.querySelector('#password_error').textContent = 'The password should have at least 2 digits';
            btn_submit.dataset.password_check = 'false';

            if (!checkBtnSubmit(btn_submit)) {
               btn_submit.disabled = true;
               btn_submit.style.cursor = 'not-allowed';
            }

         } else if (!pattern_lowercase.test(this.value)) {
            document.querySelector('#password_error').textContent = 'The password should have at least 2 lowercase letters';
            btn_submit.dataset.password_check = 'false';

            if (!checkBtnSubmit(btn_submit)) {
               btn_submit.disabled = true;
               btn_submit.style.cursor = 'not-allowed';
            }
         } else if (!pattern_uppercase.test(this.value)) {
            document.querySelector('#password_error').textContent = 'The password should have at least 2 uppercase letters';
            btn_submit.dataset.password_check = 'false';

            if (!checkBtnSubmit(btn_submit)) {
               btn_submit.disabled = false;
               btn_submit.style.cursor = 'pointer';
            }
         } else if (!matchSpecialCharacteres(this.value.split(''))) {
            document.querySelector('#password_error').textContent = 'The password should have at least 2 special characters';
            btn_submit.dataset.password_check = 'false';

            if (!checkBtnSubmit(btn_submit)) {
               btn_submit.disabled = true;
               btn_submit.style.cursor = 'not-allowed';
            }
         } else {
            document.querySelector('#password_error').textContent = '';
            btn_submit.dataset.password_check = 'true';

            if (checkBtnSubmit(btn_submit)) {
               btn_submit.disabled = false;
               btn_submit.style.cursor = 'pointer';
            }
         }

      });

      eye_password.addEventListener('click', function() {

         if (document.querySelector('#password').type === 'password') {
            this.className = 'fas fa-eye-slash'
            document.querySelector('#password').type = 'text';
         } else if (document.querySelector('#password').type === 'text') {
            this.className = 'fas fa-eye';
            document.querySelector('#password').type = 'password';
         }

      });

      function matchSpecialCharacteres(arr, special_chars = 2) {

         const match = arr.reduce((acc, currentValue) => {

            const pattern = /[!"#\$%&'\(\)\*\+,-\.\/:;<=>\?@[\]\^_`\{\|}~]/g;

            if (currentValue.match(pattern)) {
               acc += 1;
            }

            return acc;

         }, 0);

         if (match === special_chars || match > special_chars) {
            return true;
         } else {
            return false;
         }

      }

      function checkBtnSubmit(btn_submit) {
         if (btn_submit.dataset.username_check === 'true' && btn_submit.dataset.password_check === 'true') {
            return true;
         } else {
            return false;
         }
      }

      document.querySelector('.close-button').addEventListener('click', function() {
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });

      document.querySelector('#icon-bars').addEventListener('click', function() {
         document.querySelector('.sidebar-nav').classList.toggle('sidebar-nav-active');
      });
   </script>
</body>