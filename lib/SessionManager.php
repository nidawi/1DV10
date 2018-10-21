<?php

namespace lib;

require_once 'Cookie.php'; // This service requires cookies.
require_once 'SessionStorage.php';

/**
 * A simplistic session manager. PHP-specific.
 */
class SessionManager {

  private static $userAgentKey = "SESSION_USER_AGENT";
  private static $remoteAddressKey = "SESSION_REMOTE_ADDRESS";

  public function __construct() {
    $this->start();
  }

  /**
   * Gets the session that's associated to the current client.
   */
  public function getSession(string $key) : SessionStorage {
    return new \lib\SessionStorage($key);
  }

  /**
   * Verifies the integrity of the current session.
   * Will replace the session with a valid one if the check fails.
   */
  public function verifySessionIntegrity() {
    if (!$this->isValidSession()) {
      $this->end();
      $this->restart();
    }
  }

  private function end() {
    // Ends session without destroying session data
    if ($this->isSessionStarted()) {
      session_commit();
      $this->clear();
    }
  }

  private function restart() {
    // TODO: there should be a cleaner way to do this.
    session_start();
    session_regenerate_id();
    session_destroy();
    session_commit();
    session_start();
    $this->setUserAgent();
    $this->setRemoteAddress();
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

  private function isValidSession() : bool {
    return ($this->getUserAgent() === $this->getStoredUserAgent()) && ($this->getRemoteAddress() === $this->getStoredRemoteAddress());
  }
  private function isSessionStarted() : bool {
    return (session_status() === PHP_SESSION_ACTIVE);
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
    return isset($_SESSION[self::$userAgentKey]) ? $_SESSION[self::$userAgentKey] : "";
  }
  private function getStoredRemoteAddress() : string {
    return isset($_SESSION[self::$remoteAddressKey]) ? $_SESSION[self::$remoteAddressKey] : "";
  }

  private function setUserAgent() {
     $_SESSION[self::$userAgentKey] = $this->getUserAgent();
  }
  private function setRemoteAddress() {
    $_SESSION[self::$remoteAddressKey] = $this->getRemoteAddress();
  }
}