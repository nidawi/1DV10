<?php

namespace Login\model;

class Username {
  
  const USERNAME_MIN_LENGTH = 3;

  private static $usernameValidityRegexp = "/^\w+$/i";

  private $username;

  public function __construct(string $username) {
    $this->setUsername($username);
  }

  public function getUsername() : string {
    return $this->username;
  }
  public function setUsername(string $username) {
    $this->verifyLength($username);    
    $this->verifyCharacters($username);
    $this->username = $username;
  }
  
  private function verifyLength(string $username) {
    if (strlen($username) < self::USERNAME_MIN_LENGTH)
      throw new UsernameTooShortException();
  }
  private function verifyCharacters(string $username) {
    if (!preg_match(self::$usernameValidityRegexp, $username))
      throw new UsernameContainsInvalidCharactersException();
  }
}