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
   <div class="main">
      <h2 style="text-align: center;padding:20px;">Contact</h2>

      <?php if (isset($email_error_message)) : ?>
         <p style="color: red;"><?php echo $email_error_message ?></p>
      <?php elseif (isset($email_message_success)) : ?>
         <p style="color: green;"><?php echo $email_message_success ?></p>
      <?php endif; ?>

      <div class="contact-container">
         <form class="form" action="" method="post">
            <div class="container-flex-column">
               <label for="fullname">Your Name</label>
               <input type="text" class="input" name="fullname" id="fullname" placeholder="Enter your name">
               <p style="color: red;" id="username_error"></p>
            </div>
            <div class="container-flex-column">
               <label for="email">Your Email</label>
               <input type="email" class="input" name="email" id="email" placeholder="Enter your email">
               <p style="color: red;" id="username_error"></p>
            </div>
            <div class="container-flex-column">
               <label for="message">Message</label>
               <textarea class="textarea-message" name="message" id="message" placeholder="Message Text..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Send Message</button>
         </form>
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