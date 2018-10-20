<?php

namespace Login\view;

require_once 'PostView.php';
require_once 'NewPostView.php';

class ThreadView extends ViewTemplate {

  private $forumLayout;
  private $newPostView;
  private $postView;

  private $threadToDisplay;
  private $account;

  private static $threadEdit = "ThreadView::EditThread";
  private static $threadDelete = "ThreadView::DeleteThread";

  public function __construct(ForumLayout $fl, \lib\SessionStorage $session, \Login\model\Account $account = null) {
    parent::__construct($session);
    $this->forumLayout = $fl;
    $this->newPostView = new NewPostView($session, $this->getThreadLink());
    $this->postview = new PostView($session, $this->getThreadLink());
    $this->account = $account;
  }

  public function displayThread(\Login\model\Thread $thread) {
    $this->threadToDisplay = $thread;
  }
  
  public function userWantsToViewThread() : bool {
    return $this->hasQueryString($this->forumLayout->getThreadLink()) && $this->getThreadRequest() !== "";
  }
  public function userWantsToDeleteThread() : bool {
    return $this->userWantsToViewThread() && $this->isRequestPOSTHeaderPresent(self::$threadDelete);
  }
  public function userWantsToEditThread() : bool {
    return $this->userWantsToViewThread() && $this->isRequestPOSTHeaderPresent(self::$threadEdit);
  }
  public function userWantsToMakeNewPost() : bool {
    return $this->userWantsToViewThread() && $this->newPostView->userWantsToMakeNewPost();
  }

  public function getDesiredThreadId() : int {
    $threadId = intval($this->getThreadRequest());

    if ($threadId <= 0)
      throw new InvalidThreadIdentifierException();
    else
      return $threadId;
  }
  public function getNewPost() : \Login\model\Post {
    return $this->newPostView->getPost();
  }

  public function threadDeletionSuccessful() {
    $this->setDisplayMessage('Thread deleted.');
    $this->redirect('?' . $this->getForumLink(), true);
    die();
  }
  public function postCreationSuccessful() {
    $this->newPostView->postCreationSuccessful();
  }
  public function postCreationUnsuccessful(\Exception $err) {
    $this->newPostView->postCreationUnsuccessful($err);
  }

  public function getHTML() : string {
    return '
    <div class="forumThreadHeader">
      <h2>' . $this->threadToDisplay->getTitle() . '</h2><p> by ' . $this->generateAuthorText() . ' ' . $this->generateToolsMenu(self::$threadEdit, self::$threadDelete) . '</p>
    </div>
    <div class="threadContainer">
      ' . $this->generatePosts() . '
    </div>
    ' . $this->newPostView->getHTML() . '
    ';
  }

  private function getThreadLink() : string {
    return $this->getForumLink() . '&' . $this->forumLayout->getThreadLink() . '=' . $this->getThreadRequest();
  }
  private function getThreadRequest() : string {
    return $_GET[$this->forumLayout->getThreadLink()] ?? '';
  }
  private function generateOpeningPost() : string {
    return $this->threadToDisplay->getTitle();
  }
  private function generateAuthorText() : string {
    return $this->threadToDisplay->getCreatorUsername() . ' on ' . $this->forumLayout->getDateString($this->threadToDisplay->getCreatedAt());
  }
  private function generateToolsMenu(string $editId, string $deleteId) : string {
    if (isset($this->account) && $this->threadToDisplay->canAccountEditThread($this->account)) {
      return '
        <form class="postToolBox" action="?' . $this->getThreadLink() . '" method="post" enctype="multipart/form-data">
          <input type="submit" value="Edit" name="' . $editId .'">
          <input type="submit" value="Delete" name="' . $deleteId . '">
        </form>
      ';
    }
    return "";
  }

  private function generatePosts() : string {
    $postsHTML = "";

    // We need to be able to generate an empty thread, for whatever reason.
    if ($this->threadToDisplay->getPostCount() > 0) {
      $postsHTML .= $this->generateThreadBody($this->threadToDisplay->getBody());

      foreach ($this->threadToDisplay->getPosts() as $post) {
        $postsHTML .= $this->generatePost($post);
      }
    }

    return $postsHTML;
  }

  private function generateThreadBody(\Login\model\Post $post) : string {
    return $this->getPostTemplate('', $post->getBody());
  }

  private function generatePost(\Login\model\Post $post) : string {
    return $this->getPostTemplate($this->generatePostAuthorHTML($post), $post->getBody()); 
  }

  private function getPostTemplate(string $header, string $body) : string {
    // Prevent XSS by encoding special chars such as injected <script>-tags etc.
    $bodyHTMLEncoded = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');
    // Retain user new lines by replacing them with <br>-tags.
    $bodyWithNewlines = str_replace(PHP_EOL, '<br>', $bodyHTMLEncoded);
    
    return '
      <div class="forumPost">
        '. $header . '
        <p>' . $bodyWithNewlines . '</p>
      </div>
    ';
  }

  private function generatePostToolsMenuHTML(\Login\model\Post $post) : string {

  }

  private function generatePostAuthorHTML(\Login\model\Post $post) : string {
    $authorUsernameString = '<h2>' . $post->getCreatorUsername() . '</h2>';
    $postTimestampString = '<h3> on ' . $this->forumLayout->getDateString($post->getCreatedAt()) . '</h3>';

    return $authorUsernameString . $postTimestampString . ' ' . $this->generateToolsMenu(self::$threadEdit, self::$threadDelete);
  }
}