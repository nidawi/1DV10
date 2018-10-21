<?php

namespace Login\model;

require_once 'TemporaryPasswordRegister.php';
require_once 'AccountRegisterDAO.php';
require_once 'AccountInfo.php';

/**
 * A fascade responsible for account creation/deletion, etc.
 * Can be substituted for an altnerative implementation.
 */
class AccountRegister implements AccountRegisterDAO, AccountInfo {

  private $database;
  private $passwordRegister;

  private static $accountsTableName = "accounts";

  public function __construct(\lib\Database $database) {
    $this->database = $database;
    $this->passwordRegister = new TemporaryPasswordRegister($database);
  }

  /**
   * Checks whether an account with the provided username exists.
   * @todo Implement another one of these that checks the Id instead.
   */
  public function isAccountCreated(string $username) : bool {
    try {
      $this->getAccountByUsername($username);
      return true;
    } catch (\Exception $err) {
      return false;
    }
  }

  public function createAccount(\Login\model\Username $username, \Login\model\Password $password) {
    // This design ensures that resources are not spent on asking the database
    // for username availability if the input is incorrect to begin with. All business rules
    // (see Username.php and Password.php) have to be satisfied before any database calls are made.
    if ($this->isAccountCreated($username->getUsername()))
      throw new AccountAlreadyExistsException();
    
      $argsArr = array($username->getUsername(), password_hash($password->getPassword(), PASSWORD_BCRYPT));
      $this->database->query('insert into ' . self::$accountsTableName . ' (username, password) values (?, ?)', $argsArr);
  }

  /**
   * Updates the provided account with the provided updates and returns the updated account.
   * The $updates array is an associative array containing key=>value pairs with new account information.
   * Valid entries: ["password"=>string, "type"=>"admin|user", "username"=>string]
   * If an empty array is passed, only the user's "updatedAt" will be updated.
   * If null is passed, no changes will be made (including "updatedAt").
   */
  public function updateAccount(Account $account, array $updates) : Account {
    // TODO: refactor this, consider using a second account instance that contains the desired changes
    $this->assertAccountExists($account);

    if (!isset($updates))
      return $account;
    else if (count($updates) > 0)
      throw new NotImplementedException();
    else if (count($updates) === 0) {
      $this->database->query('update ' . self::$accountsTableName . ' set updatedat=NOW() where id=?', array($account->getId()));
      return $this->getAccountByUsername($account->getUsername());
    }
  }

  /**
   * Deletes the given account.
   * @todo Implement this.
   * @throws NotImplementedException
   */
  public function deleteAccount(Account $account) {
    throw new NotImplementedException();
  }

  /**
   * Takes the provided credentials and attempts to authenticate an associated account.
   * Returns the account if the operation is successful.
   * 
   * This will verify the provided password and compare it to the account's password
   * as well as the temporary password (if there is one).
   * A temporary password will be created for the user if they wish to be remembered.
   * Lastly, the account will have its "updatedat" variable updated.
   * 
   * @throws InvalidCredentialsException
   */
  public function getAccountByCredentials(AccountCredentials $credentials) : Account {
    // This definitely does more than "one thing". Although that is "by design".
    // Does that work for you, Martin?
    $account = $this->getAccountByUsername($credentials->getUsername());

    if ($account->isPasswordMatch($credentials->getPassword())) {
      $this->doRememberUser($credentials, $account);
      // Refresh the account's "updated at".
      $this->updateAccount($account, array());
      return $account;
    }
    else
      throw new InvalidCredentialsException();
  }

  /**
   * Attempts to fetch and return an account with the provided username.
   * 
   * @throws InternalFailureException
   * @throws AccountDoesNotExistException
   */
  public function getAccountByUsername(string $username) : Account {
    $accountMatches = $this->database->query('select * from ' . self::$accountsTableName . ' where binary username=?', array($username));
    
    if (!isset($accountMatches) || count($accountMatches) > 1)
      throw new InternalFailureException();
    else if (count($accountMatches) < 1)
      throw new AccountDoesNotExistException();

      return $this->getAccountInstance($accountMatches[0]);
  }

  /**
   * Attempts to fetch and return an account with the provided Id.
   * 
   * @throws InternalFailureException
   * @throws AccountDoesNotExistException
   */
  public function getAccountById(string $id) : Account {
    $accountMatches = $this->database->query('select * from ' . self::$accountsTableName . ' where binary id=?', array($id));
    
    if (!isset($accountMatches) || count($accountMatches) > 1)
      throw new InternalFailureException();
    else if (count($accountMatches) < 1)
      throw new AccountDoesNotExistException();

    return $this->getAccountInstance($accountMatches[0]);
  }

  /**
   * Returns a collection of all accounts.
   * @todo Implement this.
   * @throws NotImplementedException
   */
  public function getAccounts() : array {
    throw new NotImplementedException();
  }

  private function assertAccountExists(Account $account) {
    if (!$this->isAccountCreated($account->getUsername())) 
      throw new AccountDoesNotExistException();
  }

  private function getAccountInstance(array $rawAccount) : Account {
    $createdAt = strtotime($rawAccount["createdat"]);
    $updatedAt = strtotime($rawAccount["updatedat"]);
    $id = $rawAccount["id"];
    $type = $rawAccount["type"];
    $tempPassword = null;

    try { $tempPassword = $this->passwordRegister->getTemporaryPassword($id); }
    catch (\Exception $err) {}

    return new Account($rawAccount["username"], $rawAccount["password"], $id, $type, $createdAt, $updatedAt, $tempPassword);
  }

  /**
   * Checks if the user wants to be remembered.
   * Gives the user a temporary password and updates the account instance with the new temporary password.
   */
  private function doRememberUser(AccountCredentials $credentials, Account &$account) {
    // Due to time-constraints I had to rush this temporary password implementation.
    // It's not pretty, but it works. I don't want to use &$ and I hope to refactor it sometime.
    if ($credentials->userWantsToBeRemembered()) {
      $this->passwordRegister->deleteTemporaryPassword($account); // Delete old password, if any
      $this->passwordRegister->createTemporaryPassword($account); // Create new password
      $account = $this->getAccountById($account->getId());
    }
  }
}