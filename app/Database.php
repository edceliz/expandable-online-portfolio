<?php
  class Database {
    protected static $instance;
    private $db;

    static function getInstance() {
      if (!self::$instance) {
        self::$instance = new self();
      }
      return self::$instance;
    }

    function getConnection() {
      return $this->db;
    }

    private function __construct() {
      try {
        $db = new mysqli('localhost', $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
      } catch(Exception $e) {
        die();
      }
      if ($db->connect_error) {
        echo $db->connect_error;
        die();
      }
      $this->db = $db;
    }

    private function __clone() {}
    private function __wakeup() {}

    function __destruct() {
      $this->db->close();
    }
  }