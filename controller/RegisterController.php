<?php

namespace Login\controller;

class RegisterController {

  private $layoutView;
  private $registerView;
  
  private $accountRegister;

  public function __construct(\Login\view\LayoutView $lv,
      \Login\model\IAccountRegisterDAO $register,
      \lib\SessionStorage $session) {
    $this->layoutView = $lv;
    $this->registerView = new \Login\view\RegisterView($session);
    $this->accountRegister = $register;
  }

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