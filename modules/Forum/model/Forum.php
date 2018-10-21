<?php

namespace Forum\model;

require_once 'ForumExceptions.php';
require_once 'ForumDAO.php';
require_once 'Thread.php';
require_once 'Post.php';

// This is a forum facade controller
// This facade controller can be substituted for another implementation.
class Forum implements ForumDAO {

  // I feel most of these methods here are pretty self-explanatory.
  private $database;
  private $register;

  private static $threadsTableName = "threads";
  private static $postsTableName = "posts";

  public function __construct(\lib\Database $db, \Login\model\AccountInfo $accountRegister) {
    $this->database = $db;
    $this->register = $accountRegister;
  }

  public function createThread(Thread $thread, Post $body, \Login\model\Account $poster) {
    // todo: prevent duplicate posting
    if (!$thread->canPost())
      throw new InvalidThreadException();
    if (!$body->canCreate())
      throw new InvalidPostException();

    $argsArr = array($thread->getTitle(), $poster->getId());
    $this->database->query('insert into ' . self::$threadsTableName . ' (title, creator) values (?, ?)', $argsArr);
    // todo: consider refactor here
    $createdThread = $this->getThread($this->getLastInsertId());

    $this->createPost($body, $createdThread, $poster);
  }

  public function createPost(Post $post, Thread $thread, \Login\model\Account $poster) {
    if (!$post->canCreate())
      throw new InvalidPostException();
    if (!$thread->canUpdate())
      throw new InvalidThreadException();

    $argsArr = array($thread->getId(), $poster->getId(), $post->getBody());
    $this->database->query('insert into ' . self::$postsTableName . ' (thread, creator, body) values (?, ?, ?)', $argsArr);
  }

  /**
   * Fetches and returns an array containing all threads being stored, as Thread objects.
   * @todo Paging should be implemented for future scalability.
   */
  public function getThreads() : array {
    $threads = array();

    $fetchedThreads = $this->database->query('select * from ' . self::$threadsTableName, array());
    foreach ($fetchedThreads as $thread) {
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
    // Todo: we need some kind of paging for when threads get loads of posts and this will be too resource inefficient
    $posts = array();

    $fetchedPosts = $this->database->query('select * from ' . self::$postsTableName . ' where thread=?', array($thread->getId()));
    foreach ($fetchedPosts as $post) {
      $posts[] = $this->createPostInstance($post);
    }

    return $posts;
  }

  public function getPost(string $id) : Post {
    $fetchedPost = $this->database->query('select * from ' . self::$postsTableName . ' where id=?', array($id));
    if (!isset($fetchedPost) || count($fetchedPost) !== 1)
      throw new PostDoesNotExistException();
    
    return $this->createPostInstance($fetchedPost[0]);
  }

  public function updateThread(Thread $oldThread, Thread $newThread) {
    // todo: implement this
    throw new NotImplementedException();
  }
  
  public function updatePost(Post $oldPost, Post $newPost) {
    // todo: implement this
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
    if (!$post->canDelete())
      throw new InvalidPostException();
    if (!$this->postExists($post))
      throw new PostDoesNotExistException();

    $this->database->query('delete from ' . self::$postsTableName . ' where id=?', array($post->getId()));
  }

  private function threadExists(Thread $thread) : bool {
    $fetchedThread = $this->database->query('select * from ' . self::$threadsTableName . ' where id=?', array($thread->getId()));
    return count($fetchedThread) > 0;
  }

  private function postExists(Post $post) : bool {
    $fetchedPost = $this->database->query('select * from ' . self::$postsTableName . ' where id=?', array($post->getId()));
    return count($fetchedPost) > 0;
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
    $newPost = new Post($rawPost["body"], $rawPost["id"], $rawPost["createdat"],
      $rawPost["updatedat"], $rawPost["thread"], $rawPost["parent"]);
    $newPost->setCreator($this->register->getAccountById($rawPost["creator"]));
    return $newPost;
  }
}