<?php

namespace Login\view;

class NewThreadView extends ViewTemplate {

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

  public function userWantsToMakeNewThread() : bool {
    return $this->hasEmptyQueryString($this->forumLayoutView->getThreadLink());
  }

  public function isCreateNewThreadRequest() : bool {
    return $this->isRequestPOSTHeaderPresent(self::$newThread);
  }

  public function getThread() : \Login\model\Thread {
    return new \Login\model\Thread($this->getTitleString());
  }
  public function getThreadBodyPost() : \Login\model\Post {
    return new \Login\model\Post($this->getBodyString());
  }

  public function threadCreationSuccessful() {
    $this->setDisplayMessage('Thread posted.');
    $this->redirect('?' . $this->getForumLink(), true);
    die();
  }

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
      case $err instanceof \Login\model\ThreadTitleTooLongException: return 'Thread title is too long. Maximum ' . \Login\model\Thread::TITLE_MAX_LENGTH . ' characters.';
      case $err instanceof \Login\model\ThreadTitleTooShortException: return 'Thread title is too short. Minimum ' . \Login\model\Thread::TITLE_MIN_LENGTH . ' characters.';
      case $err instanceof \Login\model\PostBodyTooLongException: return 'Thread body is too long. Maximum ' . \Login\model\Post::POST_MAX_LENGTH . ' characters.';
      case $err instanceof \Login\model\PostBodyTooShortException: return 'Thread body is too short. Minimum ' . \Login\model\Post::POST_MIN_LENGTH . ' characters.';
      default: "Unknown error";
    }
  }

  private function generateForm() : string {
    return '
    <form action="?' . $this->getPath() . '" method="POST" enctype="multipart/form-data" id="' . self::$formId . '">
      <fieldset>
				<p id="' . self::$messageId . '">' . $this->getDisplayMessage() . '</p>
				<label for="' . self::$threadTitle . '" >Title :</label>
				<input type="text" size="20" name="' . self::$threadTitle . '" id="' . self::$threadTitle . '" value="' . $this->getLocal(self::$threadTitleLocal) . '" />
        <br/>
        <label for="' . self::$threadBody . '" >Body :</label>
        <textarea cols="50" rows="10" size="20" name="' . self::$threadBody . '" id="' . self::$threadBody . '" form="' . self::$formId . '">' . $this->getLocal(self::$threadBodyLocal) . '</textarea>
				<br/>
				<input id="submit" type="submit" name="' . self::$newThread . '"  value="Create" />
      </fieldset>
    </form>
    ';
  }

  private function getPath() : string {
    return $this->getForumLink() . '&' . $this->forumLayoutView->getThreadLink();
  }
}