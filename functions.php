<?php

function checkLogin($admin, $fullname, $password)
{
   $errors = array();

   if ($admin->fetchSingleAdminByName($fullname)) {

      $admin_login = $admin->fetchSingleAdminByName($fullname);

      if (!password_verify($password, $admin_login['password'])) {
         $errors[] = 'password_error';
      }
   } else {
      $errors[] = 'fullname_error';
   }

   return (empty($errors)) ? true : false;
}

function checkAdminRegistration($fullname, $password)
{
   $fullname_errors = array();
   $password_errors = array();

   $fullname_constants = [
      'fullname_min_length' => 3,
      'fullname_max_length' => 25
   ];

   $password_constants = [
      'password_min_length' => 3,
      'password_max_length' => 25,
      'password_digtis_min_allowed' => 2,
      'password_lowercase_letters_min_allowed' => 2,
      'password_uppercase_letters_min_allowed' => 2,
      'password_special_characters_min_allowed' => 2
   ];

   //Check fullname length
   $checkFullnameLength = checkFullnameLength($fullname, $fullname_constants['fullname_min_length'], $fullname_constants['fullname_max_length']);

   $fullname_errors['min_length_error'] = !empty($checkFullnameLength && isset($checkFullnameLength['min_length_error'])) ? $checkFullnameLength['min_length_error'] : null;

   // check password length 
   $checkPasswordLength = checkPasswordLength($password, $password_constants['password_min_length'], $password_constants['password_max_length']);

   $password_errors['min_length_error'] = !empty($checkPasswordLength) && isset($checkPasswordLength['min_length_error']) ? $checkPasswordLength['min_length_error'] : null;
   $password_errors['max_length_error'] = !empty($checkPasswordLength) && isset($checkPasswordLength['max_length_error']) ? $checkPasswordLength['min_length_error'] : null;

   // check password's quantity of digits
   $checkPasswordDigits = checkPasswordDigits($password, $password_constants['password_digtis_min_allowed']);
   $password_errors['min_digits_error'] = !empty($checkPasswordDigits) && isset($checkPasswordDigits['min_digits_error']) ? $checkPasswordDigits['min_digits_error'] : null;

   // check password's quantity of lowercase letter
   $checkPasswordLowercaseLetters = checkPasswordLowercaseLetters($password, $password_constants['password_lowercase_letters_min_allowed']);
   $password_errors['lowercase_letters_error'] = !empty($checkPasswordLowercaseLetters) && isset($checkPasswordLowercaseLetters['lowercase_letters_error']) ? $checkPasswordLowercaseLetters['lowercase_letters_error'] : null;

   // check password's quantity of uppercase letter
   $checkPasswordUppercaseLetters = checkPasswordUppercaseLetters($password, $password_constants['password_uppercase_letters_min_allowed']);
   $password_errors['uppercase_letters_error'] = !empty($checkPasswordUppercaseLetters) && isset($checkPasswordUppercaseLetters['uppercase_letters_error']) ? $checkPasswordUppercaseLetters['uppercase_letters_error'] : null;

   // check password's quantity of special letter
   $checkPasswordSpecialChars = checkPasswordSpecialChars($password, $password_constants['password_special_characters_min_allowed']);
   $password_errors['special_letters_error'] = !empty($checkPasswordSpecialChars) && isset($checkPasswordSpecialChars['special_letters_error']) ? $checkPasswordSpecialChars['special_letters_error'] : null;

   foreach($fullname_errors as $key => $value){
      if($value === null){
         unset($fullname_errors[$key]);
      }
   }

   foreach($password_errors as $key => $value){
      if($value === null){
         unset($password_errors[$key]);
      }
   }

   return (empty($fullname_errors) && empty($password_errors)) ? true : false;

}

// verify the fullname length

function checkFullnameLength(string $fullname, int $min, int $max)
{
   $errors = array();

   return (strlen($fullname) < $min && strlen($fullname) > $max) ? $errors['min_length_error'] = `The username must be between {$min}-{$max} characteres` : $errors;
}

// verify the password length

function checkPasswordLength(string $password, int $min, int $max)
{
   $errors = array();

   if (strlen($password) < $min) {
      $errors['min_length_error'] = `The password should have at least {$min} characters`;
   } elseif (strlen($password) > $max) {
      $errors['max_length_error'] = 'The password is too long';
   }

   return $errors;
}

// verify the password's quantity of digits

function checkPasswordDigits(string $password, int $min)
{
   $errors = array();
   $pattern_digits = "/(?=.{{$min},}[0-9])/i";

   preg_match($pattern_digits, $password) ? $errors['min_digits_error'] = null : $errors['min_digits_error'] = 'The password should have at least 2 digits';

   return $errors;
}

// verify the password's quantity of lowercase letters

function checkPasswordLowercaseLetters(string $password, int $min)
{
   $errors = array();
   $pattern_lowercase = "/(?=.{{$min},}[a-z])/i";

   preg_match($pattern_lowercase, $password) ? $errors['lowercase_letters_error'] = null : $errors['lowercase_letters_error'] = 'The password should have at least 2 lowercase letters';

   return $errors;
}

// verify the password's quantity of uppercase letters

function checkPasswordUppercaseLetters(string $password, int $min)
{
   $errors = array();
   $pattern_uppercase = "/(?=.{{$min},}[A-Z])/i";

   preg_match($pattern_uppercase, $password) ? $errors['uppercase_letters_error'] = null : $errors['uppercase_letters_error'] = 'The password should have at least 2 uppercase letters';

   return $errors;
}

// verify the password's quantity of special letters

function checkPasswordSpecialChars(string $password, int $min)
{
   $errors = array();

   matchSpecialCharacteres($password, $min) ? $errors['special_letters_error'] = null : $errors['special_letters_error'] = 'The password should have at least 2 special letters';

   return $errors;
}

// function to verify if the password matches the special characters in the regex pattern

function matchSpecialCharacteres(string $str, int $quant_special_chars)
{

   $match = array_reduce(str_split($str), function ($previous, $current) {

      $pattern = '/[!"#\$%&\'\(\)\*\+,-\.\/:;<=>\?@[\]\^_`\{\|}~]/i';

      if (preg_match($pattern, $current)) {

         $previous++;
      }

      return $previous;
   }, 0);

   return $match === $quant_special_chars || $match > $quant_special_chars ? true : false;
}
