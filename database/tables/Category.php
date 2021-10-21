<?php

namespace app\database\tables;

use app\database\Database;
use PDO;
use PDOException;

/**
 * Class Category
 * @package app\database\tables;
 */

class Category extends Database
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
    * Insert values to the category table
    * @param array $arr_category
    * @return bool|void
    */

   public function insertCategory(array $arr_category)
   {
      $query = "INSERT INTO `order-food-database`.category (title,image,active) VALUES(:title,:image,:active)";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':title', $arr_category['title']);
         $statement->bindParam(':image', $arr_category['image']);
         $statement->bindParam(':active', $arr_category['active']);

         return $statement->execute();

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Fetch rows from the category table
    * @return array|false|void
    */

   public function fetchCategory()
   {
      $query = "SELECT * FROM `order-food-database`.category ORDER BY title;";

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
    * Fetch rows from the category table by the active
    * @param string $active
    * @return array|false|void
    */

   public function fetchCategoryByActive(string $active = 'yes')
   {
      $query = "SELECT * FROM `order-food-database`.category WHERE active=:active ORDER BY title;";

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
    * Fetch the single row from the category table corresponding the id requested
    * @param int $id
    * @return array|false|void 
    */

   public function fetchSingleCategory(int $id)
   {
      $query = "SELECT * FROM `order-food-database`.category WHERE category_id = :id;";

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
    * Fetch the single row from the category table corresponding the title requested
    * @param string $title
    * @return array|false|void 
    */
  
   public function fetchSingleCategoryByTitle(string $title)
   {
      $query = "SELECT * FROM `order-food-database`.category WHERE title = :title;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':title', $title);

         return $statement->execute() ? $statement->fetch(PDO::FETCH_ASSOC) : false;

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }


   /**
    * Update a row from the category table
    * @param array $arr_category
    * @param int $id
    * @return bool|void
    */

   public function updateCategory(array $arr_category, int $id)
   {
      $query = "UPDATE `order-food-database`.category SET title=:title,image=:image,active=:active WHERE category_id=:id;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':title', $arr_category['title']);
         $statement->bindParam(':image', $arr_category['image']);
         $statement->bindParam(':active', $arr_category['active']);
         $statement->bindParam(':id', $id);

         return $statement->execute();

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * delete a row from the category table
    * @param int $category_id
    * @return bool|void
    */

   public function deleteSingleCategory(int $category_id)
   {

      $query = "DELETE FROM `order-food-database`.category WHERE category_id=:id";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':id', $category_id);

         return $statement->execute();

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * fetch all the rows from the category table that is matching the name
    * @param string $name
    * @return array|false|void
    */

   public function searchCategory(string $name)
   {
      $pattern = "%$name%";
      $query = "SELECT * FROM `order-food-database`.category WHERE title LIKE :name";

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
    * return the number of category table's rows 
    * @return array|false|void
    */

   public function rowsCategory()
   {
      $query = "SELECT * FROM `order-food-database`.category;";

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
    * Check if there are errors in the category array parameter
    * @param array $arr_category
    * @return array
    */

   public function checkCategoryErrors(array $arr_category)
   {
      $errors = array();

      foreach ($arr_category as $key => $value) {

         if ($key === 'title' && strlen($value) === 0) {

            $errors['title'] = "The category's title must be inserted";

         } 
         elseif ($key === 'active' && !$value) {

            $errors['active'] = "The category's activation must be inserted";

         }
      }

      return $errors;
   }
}
