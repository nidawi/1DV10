<?php

namespace lib;

require_once 'Cookie.php'; // This service requires cookies.
require_once 'SessionStorage.php';

/**
 * A simplistic session manager.
 */
class SessionManager {
  private static $USER_AGENT_KEY = "SESSION_USER_AGENT";
  private static $REMOTE_ADDRESS_KEY = "SESSION_REMOTE_ADDRESS";

  public function __construct() {
    $this->start();
  }

  private function getRequestHeader(string $key) : string {
    return $_SERVER[$key] ?? "";
  }
  private function getUserAgent() : string {
    return $this->getRequestHeader("HTTP_USER_AGENT");
  }
  private function getRemoteAddress() : string {
    return $this->getRequestHeader("REMOTE_ADDR");
  }
  private function getStoredUserAgent() : string {
    return isset($_SESSION[self::$USER_AGENT_KEY]) ? $_SESSION[self::$USER_AGENT_KEY] : "";
  }
  private function getStoredRemoteAddress() : string {
    return isset($_SESSION[self::$REMOTE_ADDRESS_KEY]) ? $_SESSION[self::$REMOTE_ADDRESS_KEY] : "";
  }
  private function setUserAgent() {
     $_SESSION[self::$USER_AGENT_KEY] = $this->getUserAgent();
  }
  private function setRemoteAddress() {
    $_SESSION[self::$REMOTE_ADDRESS_KEY] = $this->getRemoteAddress();
  }

  private function start() {
    if (!$this->isSessionStarted())
      session_start([
        "cookie_secure" => true
      ]);

    if ($this->getStoredUserAgent() === "")
      $this->setUserAgent();
    if ($this->getStoredRemoteAddress() === "")
      $this->setRemoteAddress();
  }
  private function clear() {
    // Unset the whole session
    $_SESSION = array();

    // Destroy the session cookie and cookie header
    $sessionCookie = Cookie::loadCookie(session_name());
    $sessionCookie->unset();
    $sessionCookie->delete();

    // Clear out other possible values
    if (isset($_GET[session_name()]))
      unset($_GET[session_name()]);

    if (isset($_POST[session_name()]))
      unset($_POST[session_name()]);
  }

  /**
   * Gets a session with the given key.
   */
  public function getSession(string $key) : SessionStorage {
    return new \lib\SessionStorage($key);
  }

  public function verifySessionIntegrity() {
    if (!$this->isValidSession()) {
      $this->end();
      $this->restart();
    }
  }
  public function isValidSession() : bool {
    return ($this->getUserAgent() === $this->getStoredUserAgent()) && ($this->getRemoteAddress() === $this->getStoredRemoteAddress());
  }
  public function isSessionStarted() : bool {
    return (session_status() === PHP_SESSION_ACTIVE);
  }

  public function restart() {
    session_start();
    session_regenerate_id();
    session_destroy();
    session_commit();
    session_start();
    $this->setUserAgent();
    $this->setRemoteAddress();
  }

  public function end() {
    // Ends session without destroying session data
    if ($this->isSessionStarted()) {
      session_commit(); // Abort session and discard any changes.
      $this->clear(); // Clean up
    }
  }

  private function destroy() {
    if ($this->isSessionStarted()) {
      $this->clear();
      session_destroy();
    }
  }
}