<?php

namespace Forum\view;

class ForumLayout extends \Login\view\ViewTemplate {

  private $accountManager;
  
  private static $threadLink = "thread";
  private static $postLink = "post";

  public function __construct(\lib\SessionStorage $session, \Login\model\AccountManager $accountManager) {
    parent::__construct($session);
    $this->accountManager = $accountManager;
  }

  public function getThreadLink() : string {
    return self::$threadLink;
  }
  public function getPostLink() : string {
    return self::$postLink;
  }

  public function getSpecificThreadLink(string $id) : string {
    return $this->getForumLink() . '&' . $this->getThreadLink() . '=' . $id;
  }
  public function getSpecificPostLink(string $id) : string {
    return $this->getForumLink() . '&' . $this->getPostLink() . '=' . $id;
  }

  public function getDateString(int $time) : string {
    // Uniform forum date format that can be changed in one place.
    return date("F jS, Y, g:i a", $time);
  }

  public function getHTML(string $body) : string {
    return '
    ' . $this->generateForumHeader() . '
    ' . $this->generateMenu() . '
    ' . $body . '
    ';
  }

  private function generateForumHeader() : string {
    return '
    <h1><a href="?' . $this->getForumLink() . '">Forum</a></h1>
    <p>' . $this->generateLoggedInAsHTML() . '</p>
    ';
  }

  private function generateLoggedInAsHTML() : string {
    return $this->accountManager->isLoggedIn() ? 'Logged in as ' . $this->accountManager->getLoggedInAccount()->getUsername() . ' (' . $this->getAccountType() . ')'
      : 'Not logged in.';
  }

  private function getAccountType() : string {
    return $this->accountManager->getLoggedInAccount()->isAdmin() ? "admin" : "user";
  }

  private function generateMenu() : string {
    return '
    <div class="forumMenuContainer">
      <ul>
        <li><a href="?' . $this->getForumLink() . '">Home</a></li>
        ' . $this->getNewThreadLink() . '
      </ul>
    </div>
    ';
  }

  private function getNewThreadLink() : string {
    return $this->accountManager->isLoggedIn() ? '<li><a href="?' . $this->getPath(self::$threadLink) . '">New Thread</a></li>' : '';
  }

  private function getPath(string $link) : string {
    return $this->getForumLink() . '&' . $link;
  }
}