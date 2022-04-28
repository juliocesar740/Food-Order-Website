<?php

namespace app\database\tables;

use app\database\Database;
use PDO;
use PDOException;

class Order extends Database
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
    * Insert values to the order table
    * @param array $arr_order
    * @return bool|void
    */

   public function insertOrder(array $arr_order)
   {

      $query = "INSERT INTO `order-food-database`.order (food,price,quantity,total,status,customer_name,customer_email,customer_address)
         VALUES(:food,:price,:quantity,:total,:status,:customer_name,:customer_email,:customer_address)";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':food', $arr_order['food']);
         $statement->bindParam(':price', $arr_order['price']);
         $statement->bindParam(':quantity', $arr_order['quantity']);
         $statement->bindParam(':total', $arr_order['total']);
         $statement->bindParam(':status', $arr_order['status']);
         $statement->bindParam(':customer_name', $arr_order['customer_name']);
         $statement->bindParam(':customer_email', $arr_order['customer_email']);
         $statement->bindParam(':customer_address', $arr_order['customer_address']);

         return $statement->execute();
      } catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Fetch rows from the order table
    * @return array|false|void
    */

   public function fetchOrder()
   {
      $query = "SELECT * FROM `order-food-database`.order ORDER BY created_at DESC;";

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
    * Fetch the single row from the order table corresponding the id requested
    * @param int $id
    * @return array|false|void
    */

   public function fetchSingleOrder(int $id)
   {
      $query = "SELECT * FROM `order-food-database`.order WHERE order_id = :id;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':id', $id);

         return $statement->execute() ? $statement->fetch(PDO::FETCH_ASSOC) : false;
      } catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Update a row from the order table
    * @param array $arr_order
    * @param int $id
    * @return bool|void
    */

   public function updateOrder(array $arr_order, int $id)
   {

      $query = "UPDATE `order-food-database`.order 
         SET food=:food,price=:price,quantity=:quantity,total=:total,status=:status,
         customer_name=:customer_name,customer_email=:customer_email,customer_address=:customer_address WHERE order_id=:id;";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':food', $arr_order['food']);
         $statement->bindParam(':price', $arr_order['price']);
         $statement->bindParam(':quantity', $arr_order['quantity']);
         $statement->bindParam(':total', $arr_order['total']);
         $statement->bindParam(':status', $arr_order['status']);
         $statement->bindParam(':customer_name', $arr_order['customer_name']);
         $statement->bindParam(':customer_email', $arr_order['customer_email']);
         $statement->bindParam(':customer_address', $arr_order['customer_address']);
         $statement->bindParam(':id', $id);

         return $statement->execute();
      } catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Delete a row from the order table
    * @param int $order_id
    * @return bool|void
    */

   public function deleteSingleOrder(int $order_id)
   {
      $query = "DELETE FROM `order-food-database`.order WHERE order_id=:id";

      try {

         $statement = $this->pdo->prepare($query);
         $statement->bindParam(':id', $order_id);

         return $statement->execute();
      } catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Return the number of order table's rows 
    * @return int|false|void
    */

   public function rowsOrder()
   {
      $query = "SELECT * FROM `order-food-database`.order;";

      try {

         $statement = $this->pdo->prepare($query);

         return $statement->execute() ? $statement->rowCount() : false;
      } catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Return the sum of the order's total column
    * @return array|false|void
    */

   public function sumTotal()
   {
      $query = "SELECT SUM(total) AS 'Revenue generated' FROM `order-food-database`.order WHERE status = 'Delivered'";

      try {

         $statement = $this->pdo->prepare($query);

         return $statement->execute() ? $statement->fetch(PDO::FETCH_COLUMN) : false;
      } catch (PDOException $e) {
         echo $e->getMessage();
         exit;
      }
   }

   /**
    * Check if there are errors in the order array parameter
    * @param array $arr_order
    * @return array
    */

   public function checkOrderErrors(array $arr_order)
   {
      $errors = array();

      foreach ($arr_order as $key => $value) {

         if ($key === 'food_name' && strlen($value) === 0) {

            $errors['food_name'] = "The food's name must be inserted";
         } elseif ($key === 'price' && intval($value) === 0) {

            $errors['price'] = "The food's price must be inserted";
         } elseif ($key === 'quantity' && $value === 0) {

            $errors['quantity'] = "The food's quantity must be inserted";
         } elseif ($key === 'customer_name' && strlen($value) === 0) {

            $errors['customer_name'] = "The customer's name must be inserted";
         } elseif ($key === 'customer_name' && strlen($value) < 3 || strlen($value) > 200) {

            $errors['customer_name'] = "The customer's name must be between 3-200 characters";
         } elseif ($key === 'customer_email' && strlen($value) === 0) {

            $errors['customer_email'] = "The customer's email must be inserted";
         } elseif ($key === 'customer_email' && !preg_match('/(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/', $value)) {

            $errors['customer_email'] = "The customer's email is invalid";
         } elseif ($key === 'customer_address' && strlen($value) === 0) {

            $errors['customer_address'] = "The customer's address must be inserted";
         }
      }

      return $errors;
   }
}
