<?php

namespace Login\view;

class LayoutView extends ViewTemplate {
  // Create display-related object-references that we need.
  private $topMenu;

  public function __construct() {
    $this->topMenu = new \view\TopMenuView();
    if (!$this->isValidRequestMethod())
      throw new \Exception("Invalid method");
  }

  private function isValidRequestMethod() : bool {
    return ($this->isRequestMethod("POST") || $this->isRequestMethod("GET"));
  }
  private function generateTitle() : string {
    return 'Assignment 2 - ' . (($this->userWantsToRegister()) ? 'Register' : 'Home');
  }
  private function generateRegisterLink(bool $isLoggedIn) : string {
    return ($this->userWantsToRegister()) ? '<a href="?">Back to login</a>' : ($isLoggedIn ? '' : '<a href="?register">Register a new user</a>');
  }
  private function generateLoginStatus(bool $isLoggedIn) : string {
    return $isLoggedIn ? '<h2>Logged in</h2>' : '<h2>Not logged in</h2>';
  }
  private function appendFooter() : string {
    return '
    <hr>
    ' . '<p>' . date('l, \t\h\e jS \o\f F o, \T\h\e \t\i\m\e \i\s H:i:s', time()) . '</p>' . '
    ';
  }

  public function userWantsToRegister() : bool {
    return $this->isRequestGETHeaderPresent("register");
  }
  public function isGETRequest() : bool {
    return $this->isRequestMethod("GET");
  }
  public function isPOSTRequest() : bool {
    return $this->isRequestMethod("POST");
  }

  private function generateErrorHTML(string $errorMessage, int $statusCode = 500) : string {
    return '
      <h2>Error: ' . $statusCode . '</h2>
      <p>' . $errorMessage .'</p>
    ';
  }
  private function getHTML(bool $isLoggedIn = false, string $body) : string {
    return '<!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="/public/css/main.css">
        <title>' . $this->generateTitle() . '</title>
      </head>
      <body>
        ' . $this->topMenu->show(TRUE) . '
        <div class="content">
          <h1>Assignment 2</h1>
          ' . $this->generateRegisterLink($isLoggedIn) . '
          ' . $this->generateLoginStatus($isLoggedIn) . '
          
          <div class="container">
            ' . $body . '
          </div>
          ' . $this->appendFooter() . '
        </div>
       </body>
    </html>
  ';
  }
  public function echoHTML(bool $isLoggedIn = false, string $body) {
    echo $this->getHTML($isLoggedIn, $body);
  }
  public function echoErrorHTML(string $errorMessage, int $statusCode = 500) {
    http_response_code($statusCode);
    echo $this->getHTML(false, $this->generateErrorHTML($errorMessage, $statusCode));
    die();
  }
}
