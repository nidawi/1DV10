<?php

namespace Login\model;

/**
 * Is responsible for account management.
 */
class AccountRegister {
  const USERNAME_MIN_LENGTH = 3;
  const USERNAME_VALIDITY_REGEXP = "/^\w+$/i";
  const PASSWORD_MIN_LENGTH = 6;
  const TABLE_NAME = "accounts";

  private $database;

  public function __construct(\lib\Database $database) {
    $this->database = $database;
  }

  private function isUsernameValid(string $username) : bool {
    return preg_match(self::USERNAME_VALIDITY_REGEXP, $username);
  }
  private function runAccountValidators(string $username, string $password, string $repeatedPassword) {
    $collectedErrors = "";

    if (strlen($username) < self::USERNAME_MIN_LENGTH)
      $collectedErrors .= "Username has too few characters, at least 3 characters." . PHP_EOL;
    else if (!$this->isUsernameValid($username))
      $collectedErrors .= "Username contains invalid characters." . PHP_EOL;
    else if ($this->isAccountCreated($username))
      $collectedErrors .= "User exists, pick another username." . PHP_EOL;

    if (strlen($password) < self::PASSWORD_MIN_LENGTH)
      $collectedErrors .= "Password has too few characters, at least 6 characters." . PHP_EOL;
    else if ($password !== $repeatedPassword)
      $collectedErrors .= "Passwords do not match." . PHP_EOL;

    if ($collectedErrors !== "")
      throw new \Exception($collectedErrors);
  }

  public function getAccountByUsername(string $username) : Account {
    $accountMatches = $this->database->executePreparedStatement('select * from ' . self::TABLE_NAME . ' where binary username=?', array($username));
    
    if (!isset($accountMatches))
      throw new \Exception("Internal server error");
    else if (count($accountMatches) < 1)
      throw new \Exception("Account does not exist");
    else if (count($accountMatches) > 1)
      throw new \Exception("Multiple account matches found");

    return new Account($accountMatches[0]);
  }
  public function getAccountById(string $id) : Account {
    throw new \Exception("Not implemented");
  }
  public function getAccounts() : array {
    throw new \Exception("Not implemented");
  }
  public function createAccount(string $username, string $password, string $repeatedPassword) {
    $this->runAccountValidators($username, $password, $repeatedPassword);
    $this->database->executePreparedStatement('insert into ' . self::TABLE_NAME . ' (username, password) values (?, ?)', array($username, password_hash($password, PASSWORD_BCRYPT)));
  }
  public function deleteAccount() {
    throw new \Exception("Not implemented");
  }
  /**
   * Updates the provided account with the provided updates and returns the updated account.
   * The $updates array is an associative array containing key=>value pairs with new account information.
   * Valid entries: ["password"=>string, "type"=>"admin|user", "username"=>string]
   * If an empty array is passed, only the user's "updatedAt" will be updated.
   * If null is passed, no changes will be made (including "updatedAt").
   */
  public function updateAccount(Account $account, array $updates) : Account {
    if (!$this->isAccountCreated($account->getUsername()))
      throw new \Exception("Account does not exist");

    if ($updates === null)
      return $account;
    else if (count($updates) > 0)
      throw new \Exception("Not implemented");
    else if (count($updates) === 0) {
      $this->database->update(self::TABLE_NAME, "updatedat=NOW()", "username='" . $account->getUsername() . "'");
      return $this->getAccountByUsername($account->getUsername());
    }
  }
  
  public function isAccountCreated(string $username) : bool {
    // TODO: Refactor
    $accountMatches = $this->database->executePreparedStatement('select * from ' . self::TABLE_NAME . ' where binary username=?', array($username));
    return count($accountMatches) > 0;
  }
}