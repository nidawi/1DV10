<?php

namespace Login\model;

/**
 * Account Model
 */
class Account {

  const ACCOUNT_TYPE_NORMAL = 0;
  const ACCOUNT_TYPE_MODERATOR = 1;
  const ACCOUNT_TYPE_ADMIN = 2;

  private $id;
  private $username;
  private $password;
  private $tempPassword;
  private $type;
  private $createdAt;
  private $updatedAt;

  public function __construct(
      string $username,
      string $password,
      int $id,
      int $type,
      int $createdAt,
      int $updatedAt,
      TemporaryPassword $tempPassword = null) {
    $this->id = $id;
    $this->username = $username;
    $this->password = $password;
    $this->type = $type;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->tempPassword = $tempPassword;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getUsername() : string {
    return $this->username;
  }

  public function getTemporaryPassword() : string {
    return $this->hasTemporaryPassword()
      ? $this->tempPassword->getPassword()
      : '';
  }

  public function isAdmin() : bool {
    return $this->type === self::ACCOUNT_TYPE_ADMIN;
  }

  public function isModerator() : bool {
    return $this->type === self::ACCOUNT_TYPE_MODERATOR;
  }

  public function isNormalUser() : bool {
    return !$this->isAdmin() && !$this->isModerator();
  }

  public function isPasswordMatch(string $passwordToCompare) : bool {
    // I figured that information expert and law of demeter liked letting the account be responsible.
    if ($this->hasTemporaryPassword() && $this->tempPassword->isPasswordMatch($passwordToCompare)) {
      $this->tempPassword->assertValidity();
      return true;
    }

    return password_verify($passwordToCompare, $this->password);
  }

  public function getCreatedAt() : int {
    return $this->createdAt;
  }

  public function getUpdatedAt() : int {
    return $this->updatedAt;
  }

  private function hasTemporaryPassword() : bool {
    return isset($this->tempPassword);
  }
}