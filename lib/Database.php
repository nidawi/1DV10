<?php

namespace lib;

/**
 * Amateur Database Wrapper providing basic SQLInjection protection and Prepared Statement support.
 * This class relies on Mysqli.
 */
class Database {
  private $mysqli;
  private $address;
  private $user;
  private $password;
  private $database;

  private $verifyConnection = TRUE;

  private function connect() {
    if ($this->isConnected())
      throw new \Exception("Already connected");
    // Connect to the database using the stored information.
    $this->mysqli = new \mysqli($this->address, $this->user, $this->password, $this->database);
    $this->mysqli->set_charset('utf8'); // set charset to prevent awkward non-English characters (tip found on PHP.net)
    if ($this->mysqli->connect_errno) {
      $mysqli = null; // Just making sure.
      throw new \Exception("Database connection unsuccessful");
    }
  }
  private function convertTypeToMysqliTypeString($var) : string {
    $type = gettype($var);
    switch ($type) {
      case "string": case "integer": case "double":
        return substr($type, 0, 1);
      default:
        throw new \Exception("Invalid type");
    }
  }
  /**
   * Creates a prepared statement using the provided statement and values.
   * It will then execute the statement and return the statement object for the caller to use.
   * @throws \Exception
   * @version 1.0
   */
  private function createPreparedStatement(string $statement, array $values) {
    if ($this->verifyConnection && !$this->isConnected()) {
      $this->reconnect();
    }

    // make sure that the statement does not end with a ;
    $cleanStatement = preg_replace("/(?=.*);$/", "", $statement);

    // prepare statement
    $preppedStatement = $this->mysqli->prepare($cleanStatement); // such as "INSERT INTO TABLE_NAME (COLUMN1, COLUMN2) VALUES (?, ?)
    if (!$preppedStatement)
      throw new \Exception("Statement creation failed");

    // Bind the values. Use the lovely spread operator.
    if (count($values) > 0 && !$preppedStatement->bind_param(implode(array_map(function ($e) { return $this->convertTypeToMysqliTypeString($e); }, $values)), ...$values))
      throw new \Exception("Statement binding failed");

    // Execute the statement.
    if (!$preppedStatement->execute())
      throw new \Exception("Statement execution failed");
    
    return $preppedStatement;
  }
  /**
   * Creates a prepared statement, executes, and returns the result as an array of values.
   * If the result has 0 rows, an empty array is returned.
   * If the result has 1 or more rows, the array is populed with arrays containing each row.
   */
  public function executePreparedStatement(string $statement, array $values) {
    $preppedStatement = $this->createPreparedStatement($statement, $values);
    $results = $preppedStatement->get_result();
    $resultsArray = array();

    if ($results) {
      while ($row = $results->fetch_assoc()) {
        $resultsArray[] = $row;
      }
    }

    $preppedStatement->close();

    return $resultsArray;
  }

  /**
   * Connects to a MySQL server using Mysqli.
   */
  public function __construct(string $address,  string $user, string $password, string $database) {
    $this->address = $address;
    $this->user = $user;
    $this->password = $password;
    $this->database = $database;

    $this->connect();
  }

  public function isConnected() {
    try {
      return (isset($this->mysqli) && $this->mysqli->ping()) ? true : false;
    } catch (\Exception $err) {
      return false;
    }
  }
  public function reconnect() {
    $this->connect();
  }

  public function select(string $columns, string $table, string $where = "", string $orderBy = "", bool $caseSensitive = true) {
    if ($this->verifyConnection && !$this->isConnected()) {
      $this->reconnect();
    }

    $query = "SELECT {$columns} FROM {$table}" . ($where != "" ? " WHERE" . ($caseSensitive ? " BINARY" : "") . " {$where}" : "") . ($orderBy != "" ? " ORDER BY {$orderBy}" : "");
    $response = $this->mysqli->query($query);
    return $response;
  }

  public function update(string $table, string $columnsToSet, string $where = "") {
    if ($this->verifyConnection && !$this->isConnected()) {
      $this->reconnect();
    }

    $query = "UPDATE {$table} SET {$columnsToSet}" . ($where != "" ? " WHERE {$where}" : "");
    $response = $this->mysqli->query($query);
    return $response;
  }

  public function insert(string $table, string $columns, string $values) {
    if ($this->verifyConnection && !$this->isConnected()) {
      $this->reconnect();
    }

    $query = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
    $response = $this->mysqli->query($query);
    return $response;
  }

  public function kill() {
    $this->mysqli->close();
  }

  public function getHostInfo() {
    return isset($this->mysqli) ? $this->mysqli->host_info : "not connected";
  }
}