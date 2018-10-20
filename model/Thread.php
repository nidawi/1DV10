<?php

namespace Login\model;

class Thread {

  const TITLE_MIN_LENGTH = 2;
  const TITLE_MAX_LENGTH = 100;
  const POSTS_MAX_COUNT = 100;

  private $title;
  private $id;

  private $creator;
  private $posts;

  private $createdAt;
  private $updatedAt;

  public function __construct(string $title,
      int $id = null,
      string $createdAt = null,
      string $updatedAt = null) {
    $this->setTitle($title);
    $this->id = $id;
    $this->posts = array();
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  public function setTitle(string $title) {
    $length = strlen($title);

    if ($length < self::TITLE_MIN_LENGTH)
      throw new ThreadTitleTooShortException();
    else if ($length > self::TITLE_MAX_LENGTH)
      throw new ThreadTitleTooLongException();

    $this->title = $title;
  }
  public function setCreator(Account $creator) {
    $this->creator = $creator;
  }
  public function setPosts(array $posts) {
    $this->posts = $posts;
  }

  public function getTitle() : string {
    return $this->title;
  }
  public function getId() : int {
    return $this->id;
  }
  public function getCreatorUsername() : string {
    // Law-of-demeter-respecting alternative that works due to us not really needing much creator info.
    return $this->creator->getUsername();
  }
  public function getCreatorId() : int {
    return $this->creator->getId();
  }
  public function getBody() : Post {
    if ($this->getPostCount() > 0)
      return $this->posts[0];
    throw new ThreadHasNoPostsException(); // Somehow the thread has no posts.
  }
  public function getPosts() : array {
    return array_slice($this->posts, 1);
  }
  public function getPostCount() : int {
    return count($this->posts);
  }
  public function getCreatedAt() : int {
    return strtotime($this->createdAt);
  }
  public function getUpdatedAt() : int {
    return strtotime($this->updatedAt);
  }

  public function isAccountThreadCreator(Account $account) : bool {
    return $account->getId() === $this->getCreatorId();
  }
  public function canAccountEditThread(Account $account) : bool {
    return $account->isAdmin() || $this->isAccountThreadCreator($account);
  }

  public function canPost() : bool {
    return $this->id === null && $this->createdAt === null && $this->updatedAt === null;
  }
  public function canUpdate() : bool {
    return !$this->canPost();
  }
  public function canDelete() : bool {
    return $this->canUpdate();
  }
}