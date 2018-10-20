<?php

namespace Login\controller;

class LoginController {
  
  private $layoutView;
  private $loginView;
  private $currentAccount;
  private $accountRegister;
  private $session;

  public function __construct(\Login\view\LayoutView $lv,
      \Login\model\IAccountInfo $register,
      \Login\model\Account $account = null,
      \lib\SessionStorage $session) {
    $this->layoutView = $lv;
    $this->session = $session;
    $this->loginView = new \Login\view\LoginView($account, $session);
    $this->accountRegister = $register;
    $this->currentAccount = $account;    
  }

  public function doLogin() {
    // Perform a login sequence, taking whatever credentials are provided by the view.
    if ($this->loginView->userWantsToLogin() && !isset($this->currentAccount))
      $this->attemptLogin();

    if ($this->loginView->userWantsToLogout() && isset($this->currentAccount))
      $this->doLogout();

    $this->layoutView->echoHTML($this->loginView->getHTML());
  }

  private function attemptLogin() {
    try {
      $this->attemptLoadingAccount();
      $this->setLoggedInAccount($this->currentAccount);
      $this->loginView->loginSuccessful($this->currentAccount);
    } catch (\Exception $err) {
      $this->unsetLoggedInAccount();
      $this->loginView->loginUnsuccessful($err);
    }
  }

  private function doLogout() {
    $this->unsetLoggedInAccount();
    $this->loginView->logoutSuccessful();
  }

  private function attemptLoadingAccount() {
    $credentials = $this->loginView->getProvidedCredentials();
    $this->currentAccount = $this->accountRegister->getAccountByCredentials($credentials);
  }

  private function setLoggedInAccount(\Login\Model\Account $account) {
    $this->session->saveEntry(\Login\ENV::$sessionCurrentUserId, $account);
  }

  private function unsetLoggedInAccount() {
    unset($this->currentAccount);
    $this->session->deleteEntry(\Login\ENV::$sessionCurrentUserId);
  }
}