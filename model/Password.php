<?php

namespace Login\model;

class Password {

  const PASSWORD_MIN_LENGTH = 6;

  private $password;

  public function __construct(string $password) {
    $this->setPassword($password);
  }

  public function getPassword() : string {
    return $this->password;
  }
  public function setPassword(string $password) {
    $this->verifyLength($password);
    $this->password = $password;
  }
  
  private function verifyLength(string $password) {
    if (strlen($password) < self::PASSWORD_MIN_LENGTH)
      throw new PasswordTooShortException();
  }
}