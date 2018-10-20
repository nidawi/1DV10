<?php

namespace Login\controller;

require_once __DIR__ . '/../view/ForumLayout.php';
require_once __DIR__ . '/../view/ForumView.php';
require_once __DIR__ . '/../view/ThreadView.php';
require_once __DIR__ . '/../view/NewThreadView.php';

class ForumController {

  private $forum;
  private $account;

  private $layoutView;
  private $forumLayoutView;
  private $forumView;
  private $threadView;
  private $newPostView;
  private $newThreadView;

  public function __construct(\Login\view\LayoutView $lv, \lib\SessionStorage $session, \Login\model\IForumDAO $forum, \Login\model\Account $currentAccount = null) {
    
    $this->forum = $forum;
    $this->account = $currentAccount;

    $this->layoutView = $lv;
    $this->forumLayoutView = new \Login\view\ForumLayout($session, $currentAccount);

    $this->threadView = new \Login\view\ThreadView($this->forumLayoutView, $session, $currentAccount);
    $this->forumView = new \Login\view\ForumView($this->forumLayoutView, $session);
    $this->newThreadView = new \Login\view\NewThreadView($this->forumLayoutView, $session);
  }

  public function doForumInteractions() {
    if ($this->newThreadView->userWantsToMakeNewThread())
      $this->doCreateNewThread();
    else if ($this->threadView->userWantsToViewThread())
      $this->doDisplayThread();
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
      $this->forum->createThread($thread, $post, $this->account);
      
      $this->newThreadView->threadCreationSuccessful();
    } catch (\Exception $err) {
      $this->newThreadView->threadCreationFailed($err);
    }
  }
  
  private function doDisplayThread() {
    try {
      $threadId = $this->threadView->getDesiredThreadId();
      $thread = $this->forum->getThread($threadId);
  
      if ($this->threadView->userWantsToDeleteThread())
        $this->doDeleteThread($thread);
      else if ($this->threadView->userWantsToEditThread())
        $this->doEditThread($thread);
      else if ($this->threadView->userWantsToMakeNewPost())
        $this->doCreateNewPost($thread);
  
      $this->threadView->displayThread($thread);
      $this->layoutView->echoHTML($this->forumLayoutView->getHTML($this->threadView->getHTML()));
    } catch (\Login\model\ThreadDoesNotExistException $err) {
      $this->layoutView->userRequestsInexistantResource();
    } catch (\Login\view\InvalidThreadIdentifierException $err) {
      $this->layoutView->userMadeBadRequest();
    } catch (\Exception $err) {
      $this->layoutView->serverFailure();
    }
  }

  private function doDeleteThread(\Login\model\Thread $thread) {
    $this->assertUserThreadPrivilege($thread);
    $this->forum->deleteThread($thread);
    $this->threadView->threadDeletionSuccessful();
  }

  private function doEditThread(\Login\model\Thread $thread) {
    // TODO: implement this
    $this->layoutView->userMadeBadRequest();
  }

  private function doCreateNewPost(\Login\model\Thread $thread) {
    try {
      $post = $this->threadView->getNewPost();
      $this->forum->createPost($post, $thread, $this->account);
      $this->threadView->postCreationSuccessful();
    } catch (\Login\model\PostBodyTooLongException $err) {
      $this->threadView->postCreationUnsuccessful($err);
    } catch (\Login\model\PostBodyTooShortException $err) {
      $this->threadView->postCreationUnsuccessful($err);
    }
  }

  private function doDisplayThreads() {
    $threads = $this->forum->getThreads();
    $this->forumView->displayThreads($threads);
    $this->layoutView->echoHTML($this->forumLayoutView->getHTML($this->forumView->getHTML()));
  }

  private function assertUserThreadPrivilege(\Login\model\Thread $thread) {
    $this->assertUserLoggedIn();
    if (!$thread->canAccountEditThread($this->account))
      $this->layoutView->userAccessForbidden();
  }
  private function assertUserPostPrivilege(\Login\model\Post $post) {
    $this->assertUserLoggedIn();
    // todo: implement (may have to skip post post editing due to time)
  }
  private function assertUserLoggedIn() {
    if (!$this->isLoggedIn())
      $this->layoutView->userIsUnauthorized();
  }

  private function isLoggedIn() : bool {
    return $this->account !== null;
  }  
}