<?php

// Load Base
require_once __DIR__ . '/ENV.php';
require_once 'config/Settings.php';

require_once 'lib/Database.php';
require_once 'lib/SessionManager.php';
require_once 'lib/Cookie.php';

require_once 'model/IForumDAO.php';
require_once 'model/IAccountInfo.php';
require_once 'model/IAccountRegisterDAO.php';
require_once 'model/ModelExceptions.php';
require_once 'model/AccountRegister.php';
require_once 'model/Account.php';
require_once 'model/AccountCredentials.php';
require_once 'model/Username.php';
require_once 'model/Password.php';
require_once 'model/Forum.php';

require_once 'controller/ApplicationController.php';

// Verify Session integrity
$session = new \lib\SessionManager(); // Start the session manager
$session->verifySessionIntegrity();

//Create object references
$database = new \lib\Database(\Login\ENV::$databaseAddress, \Login\ENV::$databaseUser, \Login\ENV::$databasePassword, \Login\ENV::$databaseTarget);
$accountRegister = new Login\model\AccountRegister($database);
$forum = new Login\model\Forum($database, $accountRegister);
$storage = $session->getSession("ASSIGNMENT2_PHP_SESSION");

// Controllers
$app = new \Login\controller\ApplicationController($storage, $accountRegister, $forum);

// Run app
$app->run();

$database->kill();
