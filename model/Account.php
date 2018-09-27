<?php

namespace Login\model;

/**
 * Account Model
 */
class Account {
  private $rawAccountData;

  public function __construct($accountData = null) {
    if ($accountData !== null)
      $this->setData($accountData);
  }

  public function setData($accountData) {
    $this->rawAccountData = $accountData;
  }
  public function isLoaded() : bool {
    return isset($this->rawAccountData);
  }
  public function getId() : string {
    return $this->isLoaded() ? $this->rawAccountData["id"] : "";
  }
  public function getUsername() : string {
    return $this->isLoaded() ? $this->rawAccountData["username"] : "";
  }
  public function getType() : string {
    return $this->isLoaded() ? $this->rawAccountData["type"] : "";
  }
  public function getCreatedAt() : int {
    return $this->isLoaded() ? strtotime($this->rawAccountData["createdat"]) : -1;
  }
  public function getUpdatedAt() : int {
    return $this->isLoaded() ? strtotime($this->rawAccountData["updatedat"]) : -1;
  }
  public function isPasswordMatch(string $passwordToCompare) : bool {
    return $this->isLoaded() ? (password_verify($passwordToCompare, $this->rawAccountData["password"])) : false;
  }
}