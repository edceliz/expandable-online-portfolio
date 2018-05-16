<?php
  namespace Models;

  /**
   * Inheritable class that gives children class an ORM.
   */
  class Model {
    /**
     * Instance of database.
     *
     * @var mysqli
     */
    protected $db;

    /**
     * The name of the table the class represents on MySQL.
     *
     * @var string
     */
    protected $table;

    /**
     * Array of columns on the selected database table that can be modified.
     *
     * @var array
     */
    protected $fillables;

    /**
     * Array of query results.
     *
     * @var array
     */
    public $result = [];

    private function __construct() {
      $this->db = \Database::getInstance()->getConnection();
      $className = explode('\\', get_class($this));
      $this->table = $this->table ?: strtolower(end($className)) . 's';
    }

    static function all() {
      return self::find(1);
    }

    /**
     * Finds all values where the criteria is met.
     * 
     * If $operation and $value is not set, the application will query for all entry.
     * 
     * If $value is not set, the application will query where $column is equals to $operation (where $operation acts like a $value)
     *
     * @param string $column
     * @param string|boolean $operation
     * @param string|boolean $value
     * @return Model
     */
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
      $self->result = array_reverse($result);
      return $self;
    }

    /**
     * Create entries from the parameters.
     *
     * @param array $rows
     * @return void
     */
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
          $parameters_type .= gettype($parameter)[0] !== 'N' ? gettype($parameter)[0] : 's';
        }
        $stmt->bind_param($parameters_type, ...array_values($parameters));
        $stmt->execute();
      }
      $stmt->close();
    }

    /**
     * Update all values of $results with the value from parameter.
     *
     * Example: Model::find('id', 1)->update(['name' => 'Edcel'])
     * 
     * @param array $changes
     * @return void
     */
    function update($changes) {
      $parameters_type = '';
      $parameters = [];
      $query = "UPDATE {$this->table} SET ";
      foreach ($changes as $key => $value) {
        if (!in_array($key, $this->fillables)) {
          trigger_error('Invalid column name', E_USER_ERROR);
        }
        $parameters_type .= gettype($value)[0] !== 'N' ? gettype($value[0])[0] : 's';
        array_push($parameters, $value);
        $query .= "{$key} = ?, ";
      }
      $query = substr($query, 0, -2);
      $query .= ' WHERE id = ?';
      $parameters_type .= 'i';
      $stmt = $this->db->prepare($query);
      var_dump($parameters_type, $parameters);
      foreach ($this->result as $row) {
        array_push($parameters, $row['id']);
        $stmt->bind_param($parameters_type, ...$parameters);
        $stmt->execute();
        array_pop($parameters);
      }
      $stmt->close();
    }

    /**
     * Delete all entries from the result of select query.
     * 
     * Example: Model::find('id', 2)->delete()
     *
     * @return void
     */
    function delete() {
      $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
      foreach ($this->result as $row) {
        $stmt->bind_param('i', $row['id']);
        $stmt->execute();
      }
      $stmt->close();
    }
  }