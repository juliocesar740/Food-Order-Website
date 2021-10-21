<?php

namespace app\database\tables;

use app\database\Database;
use PDO;
use PDOException;

/**
 * Class Food
 * @package app\database\tables;
 */

class Food extends Database
{
   /**
    * Initialiaze the properties and connect to the database
    * @param array $arr_database
    * @return void
    */

   public function __construct(array $arr_database)
   {
      // parent constructor will handle the pdo configuration
      parent::__construct($arr_database);
   }

   /**
    * Insert values to the food table
    * @param array $arr_food
    * @return bool|void
    */

   public function insertFood(array $arr_food)
   {
      $query = "INSERT INTO `order-food-database`.food (title,description,price,image,active,category_id) 
         VALUES(:title,:description,:price,:image,:active,:category_id)";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':title', $arr_food['title']);
         $statement->bindParam(':description', $arr_food['description']);
         $statement->bindParam(':price', $arr_food['price']);
         $statement->bindParam(':image', $arr_food['image']);
         $statement->bindParam(':active', $arr_food['active']);
         $statement->bindParam(':category_id', $arr_food['category_id']);
   
         return $statement->execute();
      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Fetch rows from the food table
    * @return array|false|void
    */

   public function fetchFood()
   {
      $query = "SELECT * FROM `order-food-database`.food ORDER BY title;";

      try {

         $statement = $this->pdo->prepare($query);

         return $statement->execute() ? $statement->fetchAll(PDO::FETCH_ASSOC) : false;

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }

   }

   /**
    * Fetch rows from the food table by the category id
    * @param int $category_id
    * @return array|false|void
    */

   public function fetchFoodByCategoryId(int $category_id)
   {
      $query = "SELECT * FROM `order-food-database`.food WHERE category_id=:category_id ORDER BY created_at DESC;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':category_id', $category_id);
   
         return $statement->execute() ? $statement->fetchAll(PDO::FETCH_ASSOC) : false;

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }

   }

   /**
    * Fetch rows from the food table by the food name
    * @param string $title
    * @return array|false|void
    */

   public function fetchFoodByTitle(string $title)
   {

      if (strlen($title) === 0) {
         return array();
      }

      $pattern = "%$title%";
      $query = "SELECT * FROM `order-food-database`.food WHERE title LIKE :title ORDER BY title;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':title', $pattern);
   
         return $statement->execute() ? $statement->fetchAll(PDO::FETCH_ASSOC) : false;

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Fetch rows from the food table by the active
    * @param string $active
    * @return array|false|void
    */

   public function fetchFoodByActive(string $active = 'yes')
   {
      $query = "SELECT * FROM `order-food-database`.food WHERE active=:active ORDER BY title;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':active', $active);
   
         return $statement->execute() ? $statement->fetchAll(PDO::FETCH_ASSOC) : false;

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Fetch the single row from the food table corresponding the id requested
    * @param int $id
    * @return array|false|void
    */

   public function fetchSingleFood(int $id)
   {
      $query = "SELECT * FROM `order-food-database`.food WHERE food_id = :id;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':id', $id);
   
         return $statement->execute() ? $statement->fetch(PDO::FETCH_ASSOC) : false;

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Update a row from the food table
    * @param array $arr_food
    * @param int $id
    * @return bool|void
    */

   public function updateFood(array $arr_food, int $id)
   {

      $query = "UPDATE `order-food-database`.food 
         SET title=:title,description=:description,price=:price,image=:image,active=:active,category_id=:category_id WHERE food_id=:id;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':title', $arr_food['title']);
         $statement->bindParam(':description', $arr_food['description']);
         $statement->bindParam(':price', $arr_food['price']);
         $statement->bindParam(':image', $arr_food['image']);
         $statement->bindParam(':active', $arr_food['active']);
         $statement->bindParam(':category_id', $arr_food['category_id']);
         $statement->bindParam(':id', $id);
   
         return $statement->execute();

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Delete a row from the food table
    * @param int $food_id
    * @return bool|void
    */
    
   public function deleteSingleFood(int $food_id)
   {
      $query = "DELETE FROM `order-food-database`.food WHERE food_id=:id";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':id', $food_id);
   
         return $statement->execute();

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Fetch all the rows from the food table that is matching the name
    * @param string $name
    * @return array|false|void
    */

   public function searchFood(string $name)
   {
      $pattern = "%$name%";
      $query = "SELECT * FROM `order-food-database`.food WHERE title LIKE :name";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':name', $pattern);
   
         return $statement->execute() ? $statement->fetchAll(PDO::FETCH_ASSOC) : false;

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * return the number of food table's rows 
    * @return int|false|void
    */

   public function rowsFood()
   {
      $query = "SELECT * FROM `order-food-database`.food;";

      try {

         $statement = $this->pdo->prepare($query);

         return $statement->execute() ? $statement->rowCount() : false;

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Check if there are errors in the food array parameter
    * @param array $arr_category
    * @return array
    */

   public function checkFoodErrors(array $arr_food)
   {
      $errors = array();

      foreach ($arr_food as $key => $value) {

         if ($key === 'title' && strlen($value) === 0) {

            $errors['title'] = "The food's title must be inserted";

         } 
         elseif ($key === 'description' && strlen($value) === 0) {

            $errors['description'] = "The food's description must be inserted";

         } 
         elseif ($key === 'price' && intval($value) === 0) {

            $errors['price'] = "The food's price must be inserted";

         } 
         elseif ($key === 'active' && $value === 'error') {

            $errors['active'] = "The food's activation must be inserted";

         } 
         elseif ($key === 'category_id' && $value === 'error') {

            $errors['category_id'] = "The food's category must be inserted";

         }
      }
      return $errors;
   }
}
