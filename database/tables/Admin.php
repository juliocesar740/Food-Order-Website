<?php

namespace app\database\tables;

use app\database\Database;
use PDO;
use PDOException;

/**
 * Class Admin
 * @package app\database\tables;
 */

class Admin extends Database
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
    * Insert values to the admin table
    * @param array $arr_admin
    * @return bool|void
    */

   public function insertAdmin(array $arr_admin)
   {
      $query = "INSERT INTO `order-food-database`.admin (name,password) VALUES(:name,:password)";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':name', $arr_admin['name']);
         $statement->bindParam(':password', $arr_admin['password']);

         return $statement->execute();
      } catch (PDOException $e) {
         echo $e;
         exit;
      }
   }

   /**
    * Fetch rows from the admin table
    * @return array|false|void
    */

   public function fetchAdmin()
   {
      $query = "SELECT * FROM `order-food-database`.admin ORDER BY created_at DESC;";

      try {

         $statement = $this->pdo->prepare($query);

         return $statement->execute() ? $statement->fetchAll(PDO::FETCH_ASSOC) : false;
      } catch (PDOException $e) {
         echo $e;
         exit;
      }
   }

   /**
    * Fetch the single row from the admin table corresponding the id requested
    * @param int $id
    * @return array|false|void  
    */

   public function fetchSingleAdmin(int $id)
   {
      $query = "SELECT * FROM `order-food-database`.admin WHERE admin_id = :id;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':id', $id);

         return $statement->execute() ? $statement->fetch(PDO::FETCH_ASSOC) : false;

      } catch (PDOException $e) {
         echo $e;
         exit;
      }
   }

   /**
    * Update a row from the admin table
    * @param array $arr_admin
    * @param int $id
    * @return bool|void  
    */

   public function updateAdmin(array $arr_admin, int $id)
   {
      $query = "UPDATE `order-food-database`.admin SET name=:name,password=:password WHERE admin_id=:id;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':name', $arr_admin['name']);
         $statement->bindParam(':password', $arr_admin['password']);
         $statement->bindParam(':id', $id);

         return $statement->execute();

      } catch (PDOException $e) {
         echo $e;
         exit;
      }
   }

   /**
    * Delete a row from the admin table
    * @param int $admin_id
    * @return bool|void  
    */

   public function deleteSingleAdmin(int $admin_id)
   {
      $query = "DELETE FROM `order-food-database`.admin WHERE admin_id=:id";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':id', $admin_id);

         return $statement->execute();

      } catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Fetch all the rows from the admin table that is matching the name column
    * @param string $name
    * @return array|false|void
    */

   public function searchAdmin(string $name)
   {
      $pattern = "%$name%";
      $query = "SELECT * FROM `order-food-database`.admin WHERE name LIKE :name";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':name', $pattern);

         return $statement->execute() ? $statement->fetchAll(PDO::FETCH_ASSOC) : false;

      } catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Return the number of admin table's rows 
    * @return array|false|void
    */

   public function rowsAdmin()
   {
      $query = "SELECT * FROM `order-food-database`.admin;";

      try {

         $statement = $this->pdo->prepare($query);

         return $statement->execute() ? $statement->rowCount() : false;

      } catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Fetch the single row from the admin table corresponding the name requested
    * @param string $name
    * @return array|false|void
    */

   public function fetchSingleAdminByName(string $name)
   {
      $query = "SELECT * FROM `order-food-database`.admin WHERE name = :name;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':name', $name);

         return $statement->execute() ? $statement->fetch(PDO::FETCH_ASSOC) : false;

      } 
      catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }
}
