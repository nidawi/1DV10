<?php

namespace Login\controller;

class RegisterController extends ControllerTemplate {
  private $layoutView;
  private $registerView;
  private $accountRegister;

  public function __construct(\Login\view\LayoutView $lv, \Login\model\AccountRegister $register, \lib\SessionStorage $session) {
    parent::__construct($session, $register);

    $this->layoutView = $lv;
    $this->registerView = new \Login\view\RegisterView();
    $this->accountRegister = $register;
  }

  private function doGET() {
    $this->layoutView->echoHTML(false, $this->registerView->getHTML($this->getAndClearFlashMessage(), $this->getLocals()));
    $this->clearLocals();
  }
  private function doPOST() {
    try {
      $this->accountRegister->createAccount($this->registerView->getUsername(), $this->registerView->getPassword(), $this->registerView->getRepeatedPassword());

      // Validators passed. Create account.
      $this->addLocal("username", $this->registerView->getUsername());
      $this->setFlashMessage("Registered new user.");
      $this->layoutView->redirect("/login/", true);
    } catch (\Exception $err) {
      $this->addLocal("username", $this->registerView->getStrippedUsername());
      $this->setFlashMessage(str_replace(PHP_EOL, "<br>", $err->getMessage())); // Assign the error message to the session.
      $this->layoutView->redirect("/login/");
    }
  }

  public function doRegister() {
    // Deals with GET
    if ($this->layoutView->isGETRequest()) {
      $this->doGET();
      return;
    }
    $this->doPOST();
  }
}