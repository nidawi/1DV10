<?php

namespace Forum\view;

class PostView extends \Login\view\ViewTemplate {

  private $forumLayout;

  private $postToDisplay;
  private $threadToDisplay;
  private $accountManager;

  private static $postEdit = "PostView::EditPost";
  private static $postDelete = "PostView::DeletePost";
  private static $postBody = "PostView::PostBody";
  private static $formId = "PostView::Form";

  public function __construct(ForumLayout $fl,
      \lib\SessionStorage $session,
      \Login\model\AccountManager $accountManager) {
    parent::__construct($session);
    $this->forumLayout = $fl;
    $this->accountManager = $accountManager;
  }

  public function setPostAndThreadToDisplay(\Forum\model\Post $post, \Forum\model\Thread $thread) {
    $this->postToDisplay = $post;
    $this->threadToDisplay = $thread;
  }

  /**
   * Returns true if the user has specified a post that they wish to view.
   */
  public function userWantsToViewPost() : bool {
    return $this->hasQueryString($this->forumLayout->getPostLink()) && $this->getRequestPostId() !== "";
  }
  /**
   * Returns true if the user has requested post deletion.
   */
  public function userWantsToDeletePost() : bool {
    return $this->userWantsToViewPost() && $this->isRequestPOSTHeaderPresent(self::$postDelete);
  }

  public function userWantsToEditPost() : bool {
    return $this->userWantsToViewPost() && $this->isRequestPOSTHeaderPresent(self::$postEdit);
  }

  public function getDesiredPostId() : int {
    $postId = intval($this->getRequestPostId());

    if ($postId <= 0)
      throw new InvalidPostIdentifierException();
    else
      return $postId;
  }

  /**
   * Signals that the post deletion was successful. This will notify the user and redirect the client.
   * WARNING: This will also kill the call stack by calling die().
   */
  public function postDeletionSuccessful() {
    $this->setDisplayMessage("Post deleted.");
    $this->redirect('?' . $this->forumLayout->getSpecificThreadLink($this->threadToDisplay->getId()), true);
    die();
  }
  
  public function getHTML() : string {
    $body = $this->userWantsToEditPost() ? $this->generateEditPostHTML() : $this->generateViewPostHTML();

    return '
    <div class="forumPost">
      ' . $this->generatePostHeaderHTML() . '
      <p>' . $body . '</p>
    </div>
    ';
  }

  private function getPostLink() : string {
    return $this->forumLayout->getSpecificPostLink($this->getRequestPostId());
  }

  private function getRequestPostId() : string {
    return $_GET[$this->forumLayout->getPostLink()] ?? $this->postToDisplay->getId();
  }

  private function generatePostHeaderHTML() : string {
    return $this->threadToDisplay->isPostIdThreadBody($this->postToDisplay->getId())
      ? ""
      : $this->generatePostAuthorHTML() . ' ' . $this->generatePostToolsMenuHTML();
  }

  private function generateViewPostHTML() : string {
    // Prevent XSS by encoding special chars such as injected <script>-tags etc.
    $bodyHTMLEncoded = htmlspecialchars($this->postToDisplay->getbody(), ENT_QUOTES, 'UTF-8');
    // Retain user new lines by replacing them with <br>-tags.
    $bodyWithNewlines = str_replace(PHP_EOL, '<br>', $bodyHTMLEncoded);

    return $bodyWithNewlines;
  }

  private function generateEditPostHTML() : string {
    return '
    <form action="?' . $this->getPostLink() . '" method="post" enctype="multipart/form-data" id="' . self::$formId . '">
      <textarea cols="50" rows="10" size="20" name="' . self::$postBody . '"
        id="' . self::$postBody . '" form="' . self::$formId . '">' . $this->postToDisplay->getBody() . '</textarea>
      <input type="submit" value="Save" name="' . self::$postEdit .'">
    </form>
    ';
  }

  private function generatePostAuthorHTML() : string {
    $authorUsernameString = '<h2>' . $this->postToDisplay->getCreatorUsername() . '</h2>';
    $postTimestampString = '<h3> on ' . $this->forumLayout->getDateString($this->postToDisplay->getCreatedAt()) . '</h3>';

    return $authorUsernameString . $postTimestampString;
  }

  private function generatePostToolsMenuHTML() : string {
    if ($this->accountManager->isLoggedIn() && $this->postToDisplay->canAccountEditPost($this->accountManager->getLoggedInAccount())) {
      return '
        <form class="postToolBox" action="?' . $this->getPostLink() . '" method="post" enctype="multipart/form-data">
          <input type="submit" value="Edit" name="' . self::$postEdit .'">
          <input type="submit" value="Delete" name="' . self::$postDelete . '">
        </form>
      ';
    }
    return "";
  }
}