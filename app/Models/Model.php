<?php
  namespace Models;

  class Model {
    private $db;
    protected $table;
    protected $fillables;

    private function __construct() {
      $this->db = \Database::getInstance()->getConnection();
      $className = explode('\\', get_class($this));
      $this->table = strtolower(end($className)) . 's';
    }

    static function all() {
      // $self = new static();
      self::find(1);
    }

    static function find($column, $operation = false, $value = false) {
      $self = new static();
    }

    static function create($data) {

    }

    static function update($data) {

    }

    static function delete() {

    }
  }