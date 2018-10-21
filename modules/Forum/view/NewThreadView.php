<?php

namespace Forum\view;

class NewThreadView extends \Login\view\ViewTemplate {

  private $forumLayoutView;

  private static $newThread = "NewThreadView::NewThread";
  private static $threadTitle = "NewThreadView::Title";
  private static $threadBody = "NewThreadView::Body";
  private static $messageId = "NewThreadView::Message";
  private static $formId = "NewThreadView::FormId";

  private static $threadTitleLocal = "thread_title";
  private static $threadBodyLocal = "thread_body";

  public function __construct(ForumLayout $flv, \lib\SessionStorage $session) {
    parent::__construct($session);
    $this->forumLayoutView = $flv;
  }

  /**
   * Returns true if the user would like to view the Thread Creation page.
   */
  public function userWantsToViewThreadCreation() : bool {
    return $this->hasEmptyQueryString($this->forumLayoutView->getThreadLink());
  }

  /**
   * Returns true if the user has submitted a thread creation request.
   */
  public function userWantsToCreateNewThread() : bool {
    return $this->isRequestPOSTHeaderPresent(self::$newThread);
  }

  public function getThread() : \Forum\model\Thread {
    return new \Forum\model\Thread($this->getTitleString());
  }
  public function getThreadBodyPost() : \Forum\model\Post {
    return new \Forum\model\Post($this->getBodyString());
  }

  /**
   * Signals that the thread creation was successful. This will notify the user and redirect the client.
   * WARNING: This will also kill the call stack by calling die().
   */
  public function threadCreationSuccessful() {
    $this->setDisplayMessage('Thread posted.');
    $this->redirect('?' . $this->getForumLink(), true);
    die();
  }

  /**
   * Signals that the thread creation was unsuccessful. This will notify the user and refresh the page.
   * WARNING: This will also kill the call stack by calling die().
   */
  public function threadCreationFailed(\Exception $err) {
    $this->addLocal(self::$threadTitleLocal, $this->getTitleString());
    $this->addLocal(self::$threadBodyLocal, $this->getBodyString());
    $this->setDisplayMessage($this->interpretExceptionToString($err));
    $this->redirect();
    die();
  }

  public function getHTML() : string {
    return '
    <h2>Create a new thread</h2>
    ' . $this->generateForm() . '
    ';
  }
  private function getTitleString() : string {
    return $_POST[self::$threadTitle];
  }
  private function getBodyString() : string {
    return $_POST[self::$threadBody];
  }

  private function interpretExceptionToString(\Exception $err) : string {
    switch (true) {
      case $err instanceof \Forum\model\ThreadTitleTooLongException:
        return 'Thread title is too long. Maximum ' . \Forum\model\Thread::TITLE_MAX_LENGTH . ' characters.';
      case $err instanceof \Forum\model\ThreadTitleTooShortException:
        return 'Thread title is too short. Minimum ' . \Forum\model\Thread::TITLE_MIN_LENGTH . ' characters.';
      case $err instanceof \Forum\model\PostBodyTooLongException:
        return 'Thread body is too long. Maximum ' . \Forum\model\Post::POST_MAX_LENGTH . ' characters.';
      case $err instanceof \Forum\model\PostBodyTooShortException:
        return 'Thread body is too short. Minimum ' . \Forum\model\Post::POST_MIN_LENGTH . ' characters.';
      default:
        return "Unknown error";
    }
  }

  private function generateForm() : string {
    $threadTitle = $this->getLocal(self::$threadTitleLocal);
    $threadBody = $this->getLocal(self::$threadBodyLocal);

    return '
    <form action="?' . $this->forumLayoutView->getThreadPath() . '" method="POST" enctype="multipart/form-data" id="' . self::$formId . '">
      <fieldset>
      <p id="' . self::$messageId . '">' . $this->getDisplayMessage() . '</p>
      <label for="' . self::$threadTitle . '" >Title :</label>
      <input type="text" size="20" name="' . self::$threadTitle . '"
        id="' . self::$threadTitle . '" value="' . $threadTitle . '" />
      <br/>
      <label for="' . self::$threadBody . '" >Body :</label>
      <textarea cols="50" rows="10" size="20" name="' . self::$threadBody . '"
        id="' . self::$threadBody . '" form="' . self::$formId . '">' . $threadBody . '</textarea>
      <br/>
      <input id="submit" type="submit" name="' . self::$newThread . '"  value="Create" />
      </fieldset>
    </form>
    ';
  }
}