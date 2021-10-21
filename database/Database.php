<?php

namespace app\database;

use PDO;
use PDOException;

/**
 * Class Database
 * @package app\database
 */

class Database
{
   protected PDO $pdo;
   protected string $dsn;
   protected string $username;
   protected string $password;

   /**
    * Initialiaze the properties and connect to the database
    * @param array $arr_database
    * @return void
    */

   public function __construct(array $arr_database)
   {
      // database configuration 
      $this->dsn = $arr_database['db_dsn'];
      $this->username = $arr_database['db_user'];
      $this->password = $arr_database['db_password'];

      try {
         
         $this->pdo = new PDO($this->dsn, $this->username, $this->password);

      } catch (PDOException $e) {

         echo $e->getMessage();
      }
   }

}
