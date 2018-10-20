<?php

namespace Login\view;

class ForumView extends ViewTemplate {

  private $forumLayout;
  private $threadsToDisplay;

  private static $messageId = "ForumView::Message";

  public function __construct(ForumLayout $fl, \lib\SessionStorage $session) {
    parent::__construct($session);
    $this->forumLayout = $fl;
    $this->threadsToDisplay = array();
  }

  public function displayThreads(array $threads) {
    $this->threadsToDisplay = $threads;
  }

  public function getHTML(string $body = null) : string {
    return $this->generateForum();
  }

  private function generateForum() : string {
    return '
    <div class="forumContainer">
      <p id="' . self::$messageId . '">' . $this->getDisplayMessage() . '</p>
      <table>
        <col width="40%">
        <col width="10%">
        <col width="20%">
        <col width="30%">
        <tr>
          <th>Title</th>
          <th>Posts</th>
          <th>Poster</th>
          <th>Posted at</th>
        </tr>
        ' . $this->generateThreads() . '
      </table>
    </div>
    ';
  }

  private function generateThreads() : string {
    $threadsHTML = "";

    foreach ($this->threadsToDisplay as $thread) {
      $threadsHTML .= $this->generateThread($thread);
    }

    return $threadsHTML;
  }

  private function generateThread(\Login\model\Thread $thread) : string {
    return '
      <tr>
        <td>' . $this->getTitleString($thread) .'</td>
        <td>' . count($thread->getPosts()) . '</td>
        <td>' . $thread->getCreatorUsername() . '</td>
        <td>' . $this->getDateString($thread->getCreatedAt()) . '</td>
      </tr>
    ';
  }

  private function getTitleString(\Login\model\Thread $thread) : string {
    $threadTitle = htmlspecialchars($thread->getTitle(), ENT_QUOTES, 'UTF-8');
    return '<a href="?' . $this->getForumLink() . '&' . $this->forumLayout->getThreadLink() . '=' . $thread->getId() . '">' . $threadTitle . '</a>';
  }

  private function getDateString(int $time) : string {
    return date("F jS, Y, g:i a", $time);
  }
}