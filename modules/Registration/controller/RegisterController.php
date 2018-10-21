<?php

namespace Registration\controller;

require_once 'modules/Registration/view/RegisterView.php';

class RegisterController {

  private $layoutView;
  private $registerView;
  
  private $accountRegister;

  public function __construct(\Login\view\LayoutView $lv,
      \Login\model\AccountRegisterDAO $register,
      \lib\SessionStorage $session) {
    $this->layoutView = $lv;
    $this->registerView = new \Registration\view\RegisterView($session);
    $this->accountRegister = $register;
  }

  /**
   * Deals with registration requests and delegates work to the appropriate handlers.
   */
  public function doRegister() {
    if ($this->registerView->userWantsToRegisterNewAccount())
      $this->attemptRegistration();

    $this->layoutView->echoHTML($this->registerView->getHTML());
  }

  private function attemptRegistration() {
    try {
      // TODO: if there's time, do this better
      $username = $this->registerView->getUsername();
      $password = $this->registerView->getPassword();

      $this->accountRegister->createAccount($username, $password);

      $this->registerView->registrationSuccessful();
    } catch (\Exception $err) {
      $this->registerView->registrationUnsuccessful($err);
    }
  }
}