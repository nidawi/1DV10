<?php

namespace Login\view;

class ForumLayout extends ViewTemplate {

  private $account;
  
  private static $threadLink = "thread";
  private static $postLink = "post";

  public function __construct(\lib\SessionStorage $session, \Login\model\Account $account = null) {
    parent::__construct($session);
    $this->account = $account;
  }

  public function getThreadLink() : string {
    return self::$threadLink;
  }
  public function getPostLink() : string {
    return self::$postLink;
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
    return $this->account !== null ? 'Logged in as ' . $this->account->getUsername() . ' (' . $this->getAccountType() . ')'
      : 'Not logged in.';
  }

  private function getAccountType() : string {
    return $this->account->isAdmin() ? "admin" : "user";
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
    return $this->account !== null ? '<li><a href="?' . $this->getPath(self::$threadLink) . '">New Thread</a></li>' : '';
  }

  private function getPath(string $link) : string {
    return $this->getForumLink() . '&' . $link;
  }
}