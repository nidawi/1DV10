<?php

namespace Login\model;

class AccountCredentials {

  private $username;
  private $password;
  private $remember;

  private static $minUsernameLength = 1;
  private static $minPasswordLength = 1;

  public function __construct(string $username, string $password, bool $remember = false) {
    $this->setUsername($username);
    $this->setPassword($password);
    $this->remember = $remember;
  }

  public function userWantsToBeRemembered() : bool {
    return $this->remember;
  }

  public function getUsername() : string {
    return $this->username;
  }
  public function getPassword() : string {
    return $this->password;
  }

  private function setUsername(string $username) {
    // I could have used model classes for verification
    // but that would make the system report "input missing"
    // when the input is too short, which would be a lie.
    if (strlen($username) < self::$minUsernameLength)
      throw new UsernameMissingException();
    $this->username = $username;
  }
  private function setPassword(string $password) {
    if (strlen($password) < self::$minPasswordLength)
      throw new PasswordMissingException();
    $this->password = $password;
  }
}