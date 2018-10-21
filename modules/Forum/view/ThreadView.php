<?php

namespace Forum\view;

require_once 'PostView.php';
require_once 'NewPostView.php';

class ThreadView extends \Login\view\ViewTemplate {

  private $forumLayout;
  private $newPostView;

  private $threadToDisplay;
  private $session;
  private $accountManager;

  private static $threadEdit = "ThreadView::EditThread";
  private static $threadDelete = "ThreadView::DeleteThread";

  public function __construct(ForumLayout $fl,
      \lib\SessionStorage $session,
      \Login\model\AccountManager $accountManager) {
    parent::__construct($session);
    $this->forumLayout = $fl;
    $this->session = $session;
    $this->newPostView = new NewPostView($session, $accountManager, $this->getThreadLink());
    $this->accountManager = $accountManager;
  }

  public function setThreadToDisplay(\Forum\model\Thread $thread) {
    $this->threadToDisplay = $thread;
  }
  
  /**
   * Returns true if the user has specified a thread that they wish to view.
   */
  public function userWantsToViewThread() : bool {
    return $this->hasQueryString($this->forumLayout->getThreadLink()) && $this->getThreadId() !== "";
  }
  /**
   * Returns true if the user has requested thread deletion.
   */
  public function userWantsToDeleteThread() : bool {
    return $this->userWantsToViewThread() && $this->isRequestPOSTHeaderPresent(self::$threadDelete);
  }
  public function userWantsToEditThread() : bool {
    return $this->userWantsToViewThread() && $this->isRequestPOSTHeaderPresent(self::$threadEdit);
  }
  /**
   * Returns true if the user has requested a post creation.
   */
  public function userWantsToCreateNewPost() : bool {
    return $this->userWantsToViewThread() && $this->newPostView->userWantsToCreateNewPost();
  }

  public function getDesiredThreadId() : int {
    $threadId = intval($this->getThreadId());

    if ($threadId <= 0)
      throw new InvalidThreadIdentifierException();
    else
      return $threadId;
  }

  public function getNewPost() : \Forum\model\Post {
    return $this->newPostView->getPost();
  }

  /**
   * Signals that the thread deletion was successful. This will notify the user and redirect the client.
   * WARNING: This will also kill the call stack by calling die().
   */
  public function threadDeletionSuccessful() {
    $this->setDisplayMessage('Thread deleted.');
    $this->redirect('?' . $this->getForumLink(), true);
    die();
  }

  /**
   * Signals that the post creation was successful. This will notify the user and redirect the client.
   * WARNING: This will also kill the call stack by calling die().
   */
  public function postCreationSuccessful() {
    $this->newPostView->postCreationSuccessful();
  }

  /**
   * Signals that the post creation was unsuccessful. This will notify the user and refresh the page.
   * WARNING: This will also kill the call stack by calling die().
   */
  public function postCreationUnsuccessful(\Exception $err) {
    $this->newPostView->postCreationUnsuccessful($err);
  }

  public function getHTML() : string {
    return '
    <div class="forumThreadHeader">
      <h2>' . $this->threadToDisplay->getTitle() . '</h2>
      <p> by ' . $this->generateAuthorText() . ' ' . $this->generateThreadToolsMenuHTML() . '</p>
    </div>
    <div class="threadContainer">
      ' . $this->generatePosts() . '
    </div>
    ' . $this->newPostView->getHTML() . '
    ';
  }

  private function getThreadLink() : string {
    return $this->forumLayout->getSpecificThreadLink($this->getThreadId());
  }

  private function getThreadId() : string {
    return $_GET[$this->forumLayout->getThreadLink()] ?? '';
  }

  private function generateAuthorText() : string {
    $createdAt = $this->forumLayout->getDateString($this->threadToDisplay->getCreatedAt());
    return $this->threadToDisplay->getCreatorUsername() . ' on ' . $createdAt;
  }

  private function generateThreadToolsMenuHTML() : string {
    $loggedIn = $this->accountManager->isLoggedIn();
    if ($loggedIn && $this->threadToDisplay->canAccountEditThread($this->accountManager->getLoggedInAccount())) {
      return '
        <form class="postToolBox" action="?' . $this->getThreadLink() . '" method="post" enctype="multipart/form-data">
          <input type="submit" value="Edit" name="' . self::$threadEdit .'">
          <input type="submit" value="Delete" name="' . self::$threadDelete . '">
        </form>
      ';
    }
    return "";
  }

  private function generatePosts() : string {
    $postsHTML = "";

    // We need to be able to generate an empty thread, for whatever reason.
    if ($this->threadToDisplay->getPostCount() > 0) {
      $postsHTML .= $this->generatePostBodyHTML($this->threadToDisplay->getBody());

      foreach ($this->threadToDisplay->getPosts() as $post) {
        $postsHTML .= $this->generatePostBodyHTML($post);
      }
    }

    return $postsHTML;
  }

  private function generatePostBodyHTML(\Forum\model\Post $post) : string {
    $bodyPost = new PostView($this->forumLayout, $this->session, $this->accountManager);
    $bodyPost->setPostAndThreadToDisplay($post, $this->threadToDisplay);
    return $bodyPost->getHTML();
  }
}