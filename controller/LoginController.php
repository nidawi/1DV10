<?php

namespace Login\controller;

require_once 'view/LoginView.php';

class LoginController {
  
  private $accountManager;
  private $accountRegister;

  private $layoutView;
  private $loginView;

  public function __construct(\Login\view\LayoutView $lv,
      \Login\model\AccountInfo $register,
      \Login\model\AccountManager $accountManager,
      \lib\SessionStorage $sessionStorage) {
    $this->layoutView = $lv;
    $this->accountRegister = $register;
    $this->accountManager = $accountManager;
    $this->loginView = new \Login\view\LoginView($accountManager, $sessionStorage);
  }
  
  /**
   * This will automatically receive requests from the related view
   * and delegate them to the appropriate handler.
   */
  public function doLogin() {
    // Perform a login sequence, taking whatever credentials are provided by the view.
    if ($this->loginView->userWantsToLogin() && !$this->accountManager->isLoggedIn())
      $this->attemptLogin();

    if ($this->loginView->userWantsToLogout() && $this->accountManager->isLoggedIn())
      $this->doLogout();

    $this->layoutView->echoHTML($this->loginView->getHTML());
  }

  private function attemptLogin() {
    try {
      $credentials = $this->loginView->getProvidedCredentials();
      $account = $this->accountRegister->getAccountByCredentials($credentials);

      $this->accountManager->setLoggedInAccount($account);
      $this->loginView->loginSuccessful();
    } catch (\Exception $err) {
      $this->accountManager->unsetLoggedInAccount();
      $this->loginView->loginUnsuccessful($err);
    }
  }

  private function doLogout() {
    $this->accountManager->unsetLoggedInAccount();
    $this->loginView->logoutSuccessful();
  }
}