<?php

/*
 * I feel that I need to make a bit of a comment here.
 * I am aware of that you want us to use $_SESSION only inside the model.
 * However, since I use the PRG-pattern to prevent duplicate POST requests, etc.,
 * I needed a way to store display messages and "pre-filled" values for input boxes, etc.
 * Because of this, my views depend on the session (lib/SessionStorage.php) for storing what
 * I call "locals". These "locals" are the same as Node's Express' "locals" which are essentially
 * values that are stored in the session in order to be preserved after a redirect.
 * The use of the session has been abstracted in view/ViewTemplate.php.
 * 
 * This is motivated by the fact that the model, to my understanding, has absolutely nothing
 * to do with the view being able to store values such as this. All "magic indices" are stored
 * in the environments file to prevent hidden dependencies.
 */

// Load Base
require_once __DIR__ . '/ENV.php';
require_once 'config/Settings.php';

require_once 'lib/Database.php';
require_once 'lib/SessionManager.php';
require_once 'lib/Cookie.php';


require_once 'model/IAccountInfo.php';
require_once 'model/IAccountRegisterDAO.php';
require_once 'model/ModelExceptions.php';
require_once 'model/AccountManager.php';
require_once 'model/AccountRegister.php';
require_once 'model/Account.php';
require_once 'model/AccountCredentials.php';
require_once 'model/Username.php';
require_once 'model/Password.php';
require_once 'modules/Forum/model/Forum.php';

require_once 'controller/ApplicationController.php';

// Verify Session integrity
$session = new \lib\SessionManager(); // Start the session manager
$session->verifySessionIntegrity();

//Create object references
$database = new \lib\Database(\Login\ENV::DATABASE_ADDRESS, \Login\ENV::DATABASE_USER, \Login\ENV::DATABASE_PASSWORD, \Login\ENV::DATABASE_DB);
$accountRegister = new Login\model\AccountRegister($database);
$forum = new \Forum\model\Forum($database, $accountRegister);
$sessionStorage = $session->getSession(\Login\ENV::SESSION_ID);
$accountManager = new \Login\model\AccountManager($sessionStorage);

// Controllers
$app = new \Login\controller\ApplicationController($sessionStorage, $accountRegister, $forum, $accountManager);

// Run app
$app->run();

// This will most likely not be called due to the design, but I left it here just in case.
$database->kill();
