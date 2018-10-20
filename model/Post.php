<?php

namespace Login\model;

class Post {

  const POST_MIN_LENGTH = 2;
  const POST_MAX_LENGTH = 2000;

  private $id;
  private $thread;
  private $creator;
  private $body;
  private $createdAt;
  private $updatedAt;
  private $parent;

  public function __construct(string $body,
      int $id = null,
      string $createdAt = null,
      string $updatedAt = null,
      int $parent = null) {
    $this->setBody($body);
    $this->id = $id;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
    $this->parent = $parent;
  }

  public function setBody(string $body) {
    $length = strlen($body);

    if ($length < self::POST_MIN_LENGTH)
      throw new PostBodyTooShortException();
    else if ($length > self::POST_MAX_LENGTH)
      throw new PostBodyTooLongException();

    $this->body = $body;
  }
  public function setCreator(Account $creator) {
    $this->creator = $creator;
  }
  public function setThread(Thread $thread) {
    $this->thread = $thread;
  }

  public function getBody() : string {
    return $this->body;
  }
  public function getCreatorUsername() : string {
    // Law-of-demeter-respecting alternative that works due to us not really needing much creator info.
    return $this->creator->getUsername();
  }
  public function getCreatorId() : int {
    return $this->creator->getId();
  }
  public function getCreatedAt() : int {
    return strtotime($this->createdAt);
  }

  public function isAccountPostCreator(Account $account) : bool {
    return $account->getId() === $this->getCreatorId();
  }
  public function canAccountEditPost(Account $account) : bool {
    return $account->isAdmin() || $this->isAccountPostCreator($account);
  }

  public function canCreate() : bool {
    return $this->id === null && $this->createdAt === null && $this->updatedAt === null;
  }
  public function canUpdate() : bool {
    return !$this->canCreate();
  }
}