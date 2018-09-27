<?php

namespace Login\controller;

require_once 'view/ViewTemplate.php';
require_once 'view/TopMenuView.php';
require_once 'view/LoginView.php';
require_once 'view/RegisterView.php';
require_once 'view/LayoutView.php';

require_once 'controller/ControllerTemplate.php';
require_once 'controller/LoginController.php';
require_once 'controller/RegisterController.php';

/**
 * This is a happy little application controller controlling controllers as it controls best.
 */
class ApplicationController {
  // Application object references
  private $storage;
  private $register;
  private $account;
  // Application views
  private $layoutView;
  // Application controllers
  private $loginController;
  private $registerController;

  private function createControllers() {
    $this->loginController = new \Login\controller\LoginController($this->layoutView, $this->register, $this->account, $this->storage);
    $this->registerController = new \Login\controller\RegisterController($this->layoutView, $this->register, $this->storage);
  }
  private function createViews() {
    $this->layoutView = new \Login\view\LayoutView();
  }

  public function __construct(\lib\SessionStorage $storage, \Login\model\AccountRegister $register) {
    $this->storage = $storage;
    $this->register = $register;
    $this->account = $storage->loadEntry(\Login\ENV::$sessionCurrentUserId);
    $this->createViews();
    $this->createControllers();
  }
  public function run() {
    try {
      if ($this->layoutView->userWantsToRegister()) 
        $this->registerController->doRegister();
      else
        $this->loginController->doLogin();
    
    } catch (Exception $err) {
      $this->layoutView->echoErrorHTML($err->getMessage());
    }
  }
}
