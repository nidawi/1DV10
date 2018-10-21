<?php

namespace Forum\controller;

require_once __DIR__ . '/../view/ViewExceptions.php';
require_once __DIR__ . '/../view/ForumLayout.php';
require_once __DIR__ . '/../view/ForumView.php';
require_once __DIR__ . '/../view/PostView.php';
require_once __DIR__ . '/../view/ThreadView.php';
require_once __DIR__ . '/../view/NewThreadView.php';

class ForumController {

  private $forum;
  private $accountManager;

  private $layoutView;
  private $forumLayoutView;
  private $forumView;
  private $threadView;
  private $postView;
  private $newThreadView;

  public function __construct(\Login\view\LayoutView $lv,
      \lib\SessionStorage $session,
      \Forum\model\ForumDAO $forum,
      \Login\model\AccountManager $accountManager) {
    $this->forum = $forum;
    $this->accountManager = $accountManager;

    $this->layoutView = $lv;
    $this->forumLayoutView = new \Forum\view\ForumLayout($session, $accountManager);
    $this->forumView = new \Forum\view\ForumView($this->forumLayoutView, $session);
    $this->threadView = new \Forum\view\ThreadView($this->forumLayoutView, $session, $accountManager);
    $this->postView = new \Forum\view\PostView($this->forumLayoutView, $session, $accountManager);
    $this->newThreadView = new \Forum\view\NewThreadView($this->forumLayoutView, $session);
  }

  /**
   * Interacts with the forum.
   * This method will automatically delegate the request to the appropriate handler.
   */
  public function doForumInteractions() {
    if ($this->newThreadView->userWantsToMakeNewThread())
      $this->doCreateNewThread();
    else if ($this->threadView->userWantsToViewThread())
      $this->doDisplayThread();
    else if ($this->postView->userWantsToViewPost())
      $this->doDisplayPost();
    else
      $this->doDisplayThreads();
  }

  private function doCreateNewThread() {
    $this->assertUserLoggedIn();

    if ($this->newThreadView->isCreateNewThreadRequest())
      $this->attemptThreadCreation();

    $this->layoutView->echoHTML($this->forumLayoutView->getHTML($this->newThreadView->getHTML()));
  }
  
  private function attemptThreadCreation() {
    try {
      $thread = $this->newThreadView->getThread();
      $post = $this->newThreadView->getThreadBodyPost();
      $this->forum->createThread($thread, $post, $this->accountManager->getLoggedInAccount());
      
      $this->newThreadView->threadCreationSuccessful();
    } catch (\Exception $err) {
      $this->newThreadView->threadCreationFailed($err);
    }
  }
  
  private function doDisplayThread() {
    try {
      $threadId = $this->threadView->getDesiredThreadId();
      $thread = $this->forum->getThread($threadId);

      $this->threadView->setThreadToDisplay($thread);
      if ($this->threadView->userWantsToDeleteThread())
        $this->doDeleteThread($thread);
      else if ($this->threadView->userWantsToEditThread())
        $this->doEditThread($thread);
      else if ($this->threadView->userWantsToMakeNewPost())
        $this->doCreateNewPost($thread);
  
      $this->layoutView->echoHTML($this->forumLayoutView->getHTML($this->threadView->getHTML()));
    } catch (\Forum\model\ThreadDoesNotExistException $err) {
      $this->layoutView->userRequestsInexistantResource();
    } catch (\Forum\view\InvalidThreadIdentifierException $err) {
      $this->layoutView->userMadeBadRequest();
    } catch (\Exception $err) {
      $this->layoutView->serverFailure();
    }
  }

  private function doDeleteThread(\Forum\model\Thread $thread) {
    $this->assertUserThreadPrivilege($thread);
    $this->forum->deleteThread($thread);
    $this->threadView->threadDeletionSuccessful();
  }

  private function doEditThread(\Forum\model\Thread $thread) {
    // TODO: implement this
    $this->layoutView->userMadeBadRequest();
  }

  private function doCreateNewPost(\Forum\model\Thread $thread) {
    try {
      $this->assertUserLoggedIn();
      $post = $this->threadView->getNewPost();

      $this->forum->createPost($post, $thread, $this->accountManager->getLoggedInAccount());
      $this->threadView->postCreationSuccessful();
    } catch (\Forum\model\PostBodyTooLongException $err) {
      $this->threadView->postCreationUnsuccessful($err);
    } catch (\Forum\model\PostBodyTooShortException $err) {
      $this->threadView->postCreationUnsuccessful($err);
    }
  }

  private function doDisplayPost() {
    try {
      $postId = $this->postView->getDesiredPostId();
      $post = $this->forum->getPost($postId);
      $thread = $this->forum->getThread($post->getThreadId());

      $this->postView->setPostAndThreadToDisplay($post, $thread);
      if ($this->postView->userWantsToDeletePost())
        $this->doDeletePost($post);
      else if ($this->postView->userWantsToEditPost())
        $this->doEditPost($post);

      $this->layoutView->echoHTML($this->forumLayoutView->getHTML($this->postView->getHTML()));
    } catch (\Forum\model\PostDoesNotExistException $err) {
      $this->layoutView->userRequestsInexistantResource();
    } catch (\Forum\view\InvalidPostIdentifierException $err) {
      $this->layoutView->userMadeBadRequest();
    } catch (\Exception $err) {
      $this->layoutView->serverFailure();
    }
  }

  private function doEditPost(\Forum\model\Post $post) {
    // TODO: implement this
    $this->layoutView->userMadeBadRequest();
  }

  private function doDeletePost(\Forum\model\Post $post) {
    $this->assertUserPostPrivilege($post);
    $this->forum->deletePost($post);
    $this->postView->postDeletionSuccessful();
  }

  private function doDisplayThreads() {
    $threads = $this->forum->getThreads();
    $this->forumView->setThreadsToDisplay($threads);
    $this->layoutView->echoHTML($this->forumLayoutView->getHTML($this->forumView->getHTML()));
  }

  private function assertUserLoggedIn() {
    if (!$this->accountManager->isLoggedIn())
      $this->layoutView->userIsUnauthorized();
  }
  private function assertUserThreadPrivilege(\Forum\model\Thread $thread) {
    $this->assertUserLoggedIn();
    if (!$thread->canAccountEditThread($this->accountManager->getLoggedInAccount()))
      $this->layoutView->userAccessForbidden();
  }
  private function assertUserPostPrivilege(\Forum\model\Post $post) {
    $this->assertUserLoggedIn();
    if (!$post->canAccountEditPost($this->accountManager->getLoggedInAccount()))
      $this->layoutView->userAccessForbidden();
  }
}