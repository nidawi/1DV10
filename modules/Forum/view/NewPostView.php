<?php

namespace Forum\view;

class NewPostView extends \Login\view\ViewTemplate {

  private static $createPost = "NewPostView::NewPost";
  private static $postBody = "NewPostView::PostBody";
  private static $messageId = "NewPostView::Message";
  private static $formId = "NewPostView::Form";

  private static $postBodyLocal = "post_body";

  private $accountManager;
  private $inheritedURL;

  // TODO: refactor this. really ugly sending a string here.
  public function __construct(\lib\SessionStorage $session,
      \Login\model\AccountManager $accountManager,
      string $location) {
    parent::__construct($session);
    $this->accountManager = $accountManager;
    $this->inheritedURL = $location;
  }

  public function userWantsToCreateNewPost() : bool {
    return $this->isRequestPOSTHeaderPresent(self::$createPost);
  }

  public function getPost() : \Forum\model\Post {
    return new \Forum\model\Post($this->getBody());
  }

  /**
   * Signals that the post creation was successful. This will complete view-related
   * activities, refresh the page, and destroy the call stack with die().
   */
  public function postCreationSuccessful() {
    $this->setDisplayMessage("Posted new message.");
    $this->redirect();
    die();
  }

  /**
   * Signals that the post creation was unsuccessful. This will notify the user
   * of the issues, refresh the page, and destroy the call stack with die().
   */
  public function postCreationUnsuccessful(\Exception $err) {
    $this->addLocal(self::$postBodyLocal, $this->getBody());
    $this->setDisplayMessage($this->interpretException($err));
    $this->redirect();
    die();
  }

  public function getHTML() {
    $postBody = $this->getLocal(self::$postBodyLocal);

    return $this->accountManager->isLoggedIn() ? '
    <div class="newPostContainer">
      <h3>Respond to thread</h3>
      <p id="' . self::$messageId . '">' . $this->getDisplayMessage() . '</p>
      <form action="?' . $this->inheritedURL . '"
        method="post" enctype="multipart/form-data" id="' . self::$formId . '">
        <textarea cols="50" rows="10" size="20" name="' . self::$postBody . '"
          id="' . self::$postBody . '" form="' . self::$formId . '">' . $postBody . '</textarea>
        <input type="submit" value="Post" name="' . self::$createPost .'">
      </form>
    </div>
    ' : '';
  }

  private function getBody() : string {
    return $_POST[self::$postBody];
  }

  private function interpretException(\Exception $err) : string {
    switch (true) {
      case $err instanceof \Forum\model\PostBodyTooLongException:
        return 'Post is too long. Maximum ' . \Forum\model\Post::POST_MAX_LENGTH . ' characters.';
      case $err instanceof \Forum\model\PostBodyTooShortException:
        return 'Post is too short. Minimum ' . \Forum\model\Post::POST_MIN_LENGTH . ' characters.';
      default:
        return "Unknown error";
    }
  }
}