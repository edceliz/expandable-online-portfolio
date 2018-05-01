<?php
  class Database {
    static function getConnection() {
      try {
        $db = new mysqli('localhost', $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
      } catch(Exception $e) {
        die();
      }
      if ($db->connect_error) {
        echo $db->connect_error;
        die();
      }
      return $db;
    }
  }