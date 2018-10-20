<?php

namespace lib;

require_once 'Cookie.php'; // This service requires cookies.

class SessionStorage {
  
  private $sessionKey = "PHP_SESSION";

  public function __construct(string $appId = null)
  {
    if (isset($appId))
      $this->sessionKey = $appId;
  }

  /**
   * Checks whether the provided entry with the given id exists in the application's session storage.
   */
  public function exists(string $id) : bool {
    return isset($_SESSION[$id . $this->getSessionKey($id)]);
  }

  /**
   * Attempts to load an entry with the given identifier from the application's session storage.
   * If no entry was found, null will be returned.
   */
  public function loadEntry(string $id) {
    if ($this->exists($id)) {
      return $_SESSION[$id . $this->getSessionKey($id)];
    } else return NULL;
  }

  /**
   * Saves the provided entry with the given id in the application's session storage.
   */
  public function saveEntry(string $id, $entry) {
    $_SESSION[$id . $this->getSessionKey($id)] = $entry;
  }

  /**
   * Removes the provided entry with the given id from the application's session storage.
   */
  public function deleteEntry(string $id) {
    unset($_SESSION[$id . $this->getSessionKey($id)]);
  }

  /**
   * Prepends the given ID with this application's session key.
   */
  private function getSessionKey(string $id) : string {
    return $this->sessionKey . '_' . $id;
  }
}