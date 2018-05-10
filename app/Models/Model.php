<?php
  namespace Models;

  class Model {
    protected $db;
    protected $table;
    protected $fillables;
    public $result = [];

    private function __construct() {
      $this->db = \Database::getInstance()->getConnection();
      $className = explode('\\', get_class($this));
      $this->table = $table ?: strtolower(end($className)) . 's';
    }

    static function all() {
      return self::find(1);
    }

    static function find($column, $operation = false, $value = false) {
      $self = new static();
      if ($operation && $value) {
        $stmt = $self->db->prepare("SELECT * FROM {$self->table} WHERE {$column} {$operation} ?");
        $stmt->bind_param('s', $value);
      } else if ($operation) {
        $stmt = $self->db->prepare("SELECT * FROM {$self->table} WHERE {$column} = {$operation}");
      } else {
        $stmt = $self->db->prepare("SELECT * FROM {$self->table} WHERE 1");
      }
      $stmt->execute();
      $rows = $stmt->get_result();
      $result = [];
      while ($row = $rows->fetch_assoc()) {
        array_push($result, $row);
      }
      $stmt->free_result();
      $stmt->close();
      $self->result = $result;
      return $self;
    }

    static function create($rows) {
      $self = new static();
      $fillables = implode(', ', $self->fillables);
      $fillables_placeholder = substr(str_repeat('?, ', sizeof($self->fillables)), 0, -2);
      $query = "INSERT INTO {$self->table} ({$fillables}) VALUES ({$fillables_placeholder})";
      $stmt = $self->db->prepare($query);
      $blank_parameters = array_fill_keys($self->fillables, NULL);
      foreach ($rows as $row) {
        $parameters = array_merge($blank_parameters, $row);
        foreach (array_diff(array_keys($row), $self->fillables) as $excess) {
          unset($parameters[$excess]);
        }
        $parameters_type = '';
        foreach ($parameters as $parameter) {
          $parameters_type .= gettype($parameter)[0];
        }
        $stmt->bind_param($parameters_type, ...array_values($parameters));
        $stmt->execute();
      }
      $stmt->close();
    }

    function update($changes) {
      $parameters_type = '';
      $parameters = [];
      $query = "UPDATE {$this->table} SET ";
      foreach ($changes as $key => $value) {
        if (!in_array($key, $this->fillables)) {
          trigger_error('Invalid column name', E_USER_ERROR);
        }
        $parameters_type .= gettype($value)[0];
        array_push($parameters, $value);
        $query .= "{$key} = ?, ";
      }
      $query = substr($query, 0, -2);
      $query .= ' WHERE id = ?';
      $parameters_type .= 'i';
      $stmt = $this->db->prepare($query);
      foreach ($this->result as $row) {
        array_push($parameters, $row['id']);
        $stmt->bind_param($parameters_type, ...$parameters);
        $stmt->execute();
        array_pop($parameters);
      }
      $stmt->close();
    }

    function delete() {
      $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
      foreach ($this->result as $row) {
        $stmt->bind_param('i', $row['id']);
        $stmt->execute();
      }
      $stmt->close();
    }
  }