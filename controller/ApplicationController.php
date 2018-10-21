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
require_once __DIR__ . '/../modules/Forum/controller/ForumController.php';

class ApplicationController {

  private $layoutView;

  private $loginController;
  private $registerController;
  private $forumController;

  public function __construct(\lib\SessionStorage $sessionStorage,
      \Login\model\IAccountRegisterDAO $register,
      \Forum\model\IForumDAO $forum,
      \Login\model\AccountManager $accountManager) {
    $this->createViews($sessionStorage, $accountManager);
    $this->createControllers($sessionStorage, $register, $forum, $accountManager);
  }

  /**
   * Runs the application. This will automatically receive requests and delegate them to the appropriate controller.
   */
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

  private function createViews(\lib\SessionStorage $session, \Login\model\AccountManager $accountManager) {
    $this->layoutView = new \Login\view\LayoutView($session, $accountManager);
  }

  private function createControllers(\lib\SessionStorage $storage,
      \Login\model\IAccountRegisterDAO $register,
      \Forum\model\IForumDAO $forum,
      \Login\model\AccountManager $accountManager) {
    $this->loginController = new \Login\controller\LoginController($this->layoutView, $register, $accountManager, $storage);
    $this->registerController = new \Login\controller\RegisterController($this->layoutView, $register, $storage);
    $this->forumController = new \Forum\controller\ForumController($this->layoutView, $storage, $forum, $accountManager);
  }
}
