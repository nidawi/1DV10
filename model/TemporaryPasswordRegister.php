<?php

namespace Login\model;

require_once 'TemporaryPassword.php';

class TemporaryPasswordRegister {

  private $database;

  private static $temporaryPasswordsTableName = "temp_password";

  public function __construct(\lib\Database $database) {
    $this->database = $database;
  }

  public function createTemporaryPassword(Account $account) {
    $generatedPassword = TemporaryPassword::generateTemporaryPassword();
    $this->database->query('insert into ' . self::$temporaryPasswordsTableName . ' (password, owner) values (?, ?)', array($generatedPassword, $account->getId()));
  }

  public function deleteTemporaryPassword(Account $account) {
    $this->database->query('delete from ' . self::$temporaryPasswordsTableName . ' where owner=?', array($account->getId()));
  }

  public function getTemporaryPassword(int $id) : TemporaryPassword {
    $fetchedPassword = $this->database->query('select * from ' . self::$temporaryPasswordsTableName . ' where owner=?', array($id));

    if (!isset($fetchedPassword) || count($fetchedPassword) !== 1)
      throw new TemporaryPasswordDoesNotExistException();
    else
      return $this->getTemporaryPasswordInstance($fetchedPassword[0]);
  }

  private function getTemporaryPasswordInstance(array $rawPassword) : TemporaryPassword {
    $createdAt = strtotime($rawPassword["createdat"]);
    return new TemporaryPassword($rawPassword["password"], $createdAt);
  }
}