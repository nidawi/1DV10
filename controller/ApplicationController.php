<?php

namespace Login\controller;

// TODO: make all views prettier. If there's time. That is.
require_once 'view/ViewTemplate.php';
require_once 'view/TopMenuView.php';
require_once 'view/LoginView.php';
require_once 'view/RegisterView.php';
require_once 'view/LayoutView.php';

require_once 'view/ViewExceptions.php';

require_once 'controller/LoginController.php';
require_once 'controller/RegisterController.php';
require_once 'controller/ForumController.php';

class ApplicationController {

  private $layoutView;

  private $loginController;
  private $registerController;
  private $forumController;

  public function __construct(\lib\SessionStorage $sessionStorage,
      \Login\model\IAccountRegisterDAO $register,
      \Login\model\IForumDAO $forum) {
    $account = $sessionStorage->loadEntry(\Login\ENV::SESSION_CURRENT_USER_ID);
    $this->createViews($sessionStorage, $account);
    $this->createControllers($sessionStorage, $register, $forum, $account);
  }

  public function run() {
    try {
      if ($this->layoutView->userWantsToRegister())
        $this->registerController->doRegister();
      else if ($this->layoutView->userWantsToViewForum())
        $this->forumController->doForumInteractions();
      else
        $this->loginController->doLogin();
    
    } catch (Exception $err) {
      $this->layoutView->serverFailure();
    }
  }

  private function createViews(\lib\SessionStorage $session, \Login\model\Account $account = null) {
    $this->layoutView = new \Login\view\LayoutView($session, $account);
  }

  private function createControllers(\lib\SessionStorage $storage,
      \Login\model\IAccountRegisterDAO $register,
      \Login\model\IForumDAO $forum,
      \Login\model\Account $account = null) {
    $this->loginController = new \Login\controller\LoginController($this->layoutView, $register, $account, $storage);
    $this->registerController = new \Login\controller\RegisterController($this->layoutView, $register, $storage);
    $this->forumController = new \Login\controller\ForumController($this->layoutView, $storage, $forum, $account);
  }
}
