<?php

namespace Login\model;

/**
 * Manages logged-in accounts.
 */
class AccountManager {

  private $sessionStorage;
  private $loggedInAccount;

  public function __construct(\lib\SessionStorage $sessionStorage) {
    $this->sessionStorage = $sessionStorage;
    $this->loggedInAccount = $sessionStorage->loadEntry(\Login\ENV::SESSION_CURRENT_USER_ID);
  }

  /**
   * Checks whether there is currently a logged-in account.
   */
  public function isLoggedIn() : bool {
    return isset($this->loggedInAccount);
  }

  /**
   * Returns the currently logged-in account.
   * @throws NoAccountLoggedInException
   */
  public function getLoggedInAccount() : Account {
    if (!$this->isLoggedIn())
      throw new NoAccountLoggedInException();

    return $this->loggedInAccount;
  }

  /**
   * Sets the currently logged-in account.
   */
  public function setLoggedInAccount(\Login\Model\Account $account) {
    $this->loggedInAccount = $account;
    $this->sessionStorage->saveEntry(\Login\ENV::SESSION_CURRENT_USER_ID, $account);
  }

  /**
   * Unsets the currently logged-in account.
   */
  public function unsetLoggedInAccount() {
    unset($this->loggedInAccount);
    $this->sessionStorage->deleteEntry(\Login\ENV::SESSION_CURRENT_USER_ID);
  }
}