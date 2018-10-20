<?php

namespace Login\model;

require_once 'Thread.php';
require_once 'Post.php';

// This is a forum facade controller
// This facade controller can be substituted for another implementation.
class Forum implements IForumDAO {

  private $database;
  private $register;

  private static $threadsTableName = "threads";
  private static $postsTableName = "posts";

  public function __construct(\lib\Database $db, IAccountInfo $accountRegister) {
    $this->database = $db;
    $this->register = $accountRegister;
  }

  public function createThread(Thread $thread, Post $body, Account $poster) {
    // todo: prevent duplicate posting

    if (!$thread->canPost())
      throw new InvalidThreadException();
    if (!$body->canCreate())
      throw new InvalidPostException();

    $this->database->query('insert into ' . self::$threadsTableName . ' (title, creator) values (?, ?)', array($thread->getTitle(), $poster->getId()));
    $createdThread = $this->getThread($this->getLastInsertId());

    $this->createPost($body, $createdThread, $poster);
  }
  public function createPost(Post $post, Thread $thread, Account $poster) {
    if (!$post->canCreate())
      throw new InvalidPostException();
    if (!$thread->canUpdate())
      throw new InvalidThreadException();

    $this->database->query('insert into ' . self::$postsTableName . ' (thread, creator, body) values (?, ?, ?)', array($thread->getId(), $poster->getId(), $post->getBody()));
  }

  public function getThreads() : array {
    $threads = array();

    $dbThreads = $this->database->query('select * from ' . self::$threadsTableName, array());
    foreach ($dbThreads as $thread) {
      $threads[] = $this->createThreadInstance($thread);
    }

    return $threads;
  }
  public function getThread(string $id) : Thread {
    $fetchedThread = $this->database->query('select * from ' . self::$threadsTableName . ' where id=?', array($id));
    if (!isset($fetchedThread) || count($fetchedThread) !== 1)
      throw new ThreadDoesNotExistException();

    return $this->createThreadInstance($fetchedThread[0]);
  }
  public function getThreadPosts(Thread $thread) : array {
    $posts = array();

    $fetchedPosts = $this->database->query('select * from ' . self::$postsTableName . ' where thread=?', array($thread->getId()));
    foreach ($fetchedPosts as $post) {
      $posts[] = $this->createPostInstance($post);
    }

    return $posts;
  }
  public function getPost(string $id) : Post {
    throw new NotImplementedException();
  }

  public function updateThread(Thread $oldThread, Thread $newThread) {
    throw new NotImplementedException();
  }
  public function updatePost(Post $oldPost, Post $newPost) {
    throw new NotImplementedException();
  }

  public function deleteThread(Thread $thread) {
    if (!$thread->canDelete())
      throw new InvalidThreadException();
    if (!$this->threadExists($thread))
      throw new ThreadDoesNotExistException();

    $this->database->query('delete from ' . self::$threadsTableName . ' where id=?', array($thread->getId()));
  }
  public function deletePost(Post $post) {
    throw new NotImplementedException();
  }

  private function threadExists(Thread $thread) : bool {
    $fetchedThread = $this->database->query('select * from ' . self::$threadsTableName . ' where id=?', array($thread->getId()));
    return count($fetchedThread) > 0;
  }

  private function getLastInsertId() : string {
    $fetchedid = $this->database->query('select LAST_INSERT_ID()', array());
    return strval($fetchedid[0]["LAST_INSERT_ID()"]);
  }

  private function createThreadInstance(array $rawThread) : Thread {
    $newThread = new Thread($rawThread["title"], $rawThread["id"], $rawThread["createdat"], $rawThread["updatedat"]);
    $newThread->setCreator($this->register->getAccountById($rawThread["creator"]));
    $newThread->setPosts($this->getThreadPosts($newThread));
    return $newThread;
  }

  private function createPostInstance(array $rawPost) : Post {
    $newPost = new Post($rawPost["body"], $rawPost["id"], $rawPost["createdat"], $rawPost["updatedat"], $rawPost["parent"]);
    $newPost->setCreator($this->register->getAccountById($rawPost["creator"]));
    return $newPost;
  }
}