<?php

// Load Base
require_once __DIR__ . '/ENV.php';
require_once 'config/Settings.php';

require_once 'lib/Database.php';
require_once 'lib/SessionManager.php';
require_once 'lib/Cookie.php';

require_once 'model/AccountRegister.php';
require_once 'model/Account.php';

require_once 'controller/ApplicationController.php';

// Verify Session integrity
$session = new \lib\SessionManager(); // Start the session manager
$session->verifySessionIntegrity();

//Create object references
$database = new \lib\Database(\Login\ENV::$databaseAddress, \Login\ENV::$databaseUser, \Login\ENV::$databasePassword, \Login\ENV::$databaseTarget);
$accountRegister = new Login\model\AccountRegister($database);
$storage = $session->getSession("ASSIGNMENT2_PHP_SESSION");

// Controllers
$app = new \Login\controller\ApplicationController($storage, $accountRegister);

// Run app
$app->run();

$database->kill();
