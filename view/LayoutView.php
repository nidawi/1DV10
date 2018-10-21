<?php

namespace Login\view;

/**
 * This is a navigation view, essentially.
 */
class LayoutView extends ViewTemplate {

  private $topMenu;
  private $accountManager;

  public function __construct(\lib\SessionStorage $session, \Login\model\AccountManager $accountManager) {
    parent::__construct($session);

    $this->accountManager = $accountManager;
    $this->topMenu = new \view\TopMenuView();

    if (!$this->isValidRequestMethod())
      $this->userAttemptedUnallowedMethod();
  }

  public function userMadeBadRequest() {
    $this->errorOccured(400);
  }
  public function userIsUnauthorized() {
    $this->errorOccured(401);
  }
  public function userAccessForbidden() {
    $this->errorOccured(403);
  }
  public function userRequestsInexistantResource() {
    $this->errorOccured(404);
  }
  public function userAttemptedUnallowedMethod() {
    $this->errorOccured(405);
  }
  public function serverFailure() {
    $this->errorOccured(500);
  }

  public function echoHTML(string $body) {
    echo $this->getHTML($body);
  }
  
  /**
   * This will destroy the call stack by calling die().
   * This is because showing the error to the user
   * is generally the last thing you do.
   */
  private function errorOccured(int $statusCode) : string {
    http_response_code($statusCode);
    $errorMessage = $this->getStatusCodeMessage($statusCode);
    $errorHTML = '
      <h2>Error: ' . $statusCode . '</h2>
      <p>' . $errorMessage .'</p>
      ';
    
    echo $this->getHTML($errorHTML);
    die();
  }

  private function getStatusCodeMessage(int $statusCode) : string {
    switch ($statusCode) {
      case 400: return "Bad Request";
      case 401: return "Unauthorized";
      case 403: return "Forbidden";
      case 404: return "Not Found";
      case 405: return "Method not allowed";
      case 500: default: return "Internal Server Error";
    }
  }

  private function getHTML(string $body) : string {
    return '<!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="/login/public/css/main.css">
        <title>' . $this->generateTitle() . '</title>
      </head>
      <body>
        ' . $this->topMenu->getHTML(true) . '
        <div class="content">
          <h1>Assignment 2</h1>
            <ul>
              ' . $this->generateRegisterLink() . '
              ' . $this->generateForumLink() . '
            </ul>
              ' . $this->generateLoginStatus() . '
          
          <div class="container">
            ' . $body . '
          </div>
          ' . $this->appendFooter() . '
        </div>
       </body>
    </html>
  ';
  }

  private function generateTitle() : string {
    return 'Assignment 2 - ' . (($this->userWantsToRegister()) ? 'Register' : 'Home');
  }

  private function generateRegisterLink() : string {
    $registerLink = ($this->userWantsToRegister()) ? '<a href="?">Back to login</a>' : ($this->accountManager->isLoggedIn() ? '' : '<a href="?' . $this->getRegisterLink() . '">Register a new user</a>');
    return strlen($registerLink) > 0 ? $this->addListTags($registerLink) : '';
  }

  private function generateForumLink() : string {
    $forumLink = $this->userWantsToViewForum() ? '<a href="?">Go back</a>' : '<a href="?' . $this->getForumLink() . '">Go to Forum</a>';
    return $this->addListTags($forumLink);
  }

  private function generateLoginStatus() : string {
    return $this->accountManager->isLoggedIn() ? '<h2>Logged in</h2>' : '<h2>Not logged in</h2>';
  }

  private function appendFooter() : string {
    return '
    <hr>
    ' . '<p>' . date('l, \t\h\e jS \o\f F o, \T\h\e \t\i\m\e \i\s H:i:s', time()) . '</p>' . '
    ';
  }

  private function addListTags(string $element) : string {
    return '<li>' . $element . '</li>';
  }
}