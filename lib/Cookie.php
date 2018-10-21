<?php

namespace lib;

require_once 'CookieExceptions.php';

/**
 * Cookie Wrapper v1.2
 */
class Cookie {

  const COOKIE_EXPIRATION_TIME = (60 * 60 * 24 * 3); //Default cookie lifespan is 3 days.
  private static $defaultPath = "/";

  private $name;
  private $content;
  private $expireIn;

  public function __construct(string $name, string $content, int $expireIn = NULL) {
    $this->setName($name);
    $this->setContent($content);
    $this->setExpireIn($expireIn ?? self::COOKIE_EXPIRATION_TIME);
  }

  /**
   * Checks whether a cookie with the given name is currently set.
   */
  public static function isCookieSet(string $cookieName) : bool {
    return (isset($_COOKIE[$cookieName]));
  }
  /**
   * Loads a cookie with the given name. Throws if it isn't set.
   * As mentioned by C Martin on page 25, static factory methods are okay.
   * @throws CookieNotSetException
   */
  public static function loadCookie(string $cookieName) : Cookie {
    if (self::isCookieSet($cookieName))
      return new Cookie($cookieName, $_COOKIE[$cookieName]);
    else
      throw new CookieNotSetException();
  }

  /**
   * Sets the name of this cookie.
   */
  public function setName(string $name) {
    if (strlen($name) < 1)
      throw new CookieNameTooShortException();
  
    $this->name = $name;
  }
  /**
   * Sets the content of this cookie.
   */
  public function setContent(string $content) {
    $this->content = $content;
  }
  /**
   * Sets the expiration time of this cookie.
   */
  public function setExpireIn(int $expireIn) {
    if ($expireIn < 1)
      throw new InvalidCookieExpirationException();

    $this->expireIn = $expireIn;
  }

  /**
   * Returns the content of this cookie.
   */
  public function getContent() : string {
    return $this->content;
  }

  /**
   * Checks whether this cookie is currently set.
   */
  public function isSet() : bool {
    return self::isCookieSet($this->name);
  }

  /**
   * Sets this cookie (saves it to the client).
   */
  public function set(string $cookiePath = NULL) {
    // HTTPOnly is currently turned off for the purpose of the assignment tests.
    setcookie($this->name, $this->content, $this->getExpirationTime(), $cookiePath ?? self::$defaultPath, "", TRUE);
  }

  /**
   * Deletes the cookie from the client.
   */
  public function delete($cookiePath = NULL) {
    if ($this->isSet())
      setcookie($this->name, "", time() - 3600, $cookiePath ?? self::$defaultPath);
  }

  /**
   * Unsets the cookie from the system.
   */
  public function unset() {
    if ($this->isSet())
      unset($_COOKIE[$this->name]);
  }

  private function getExpirationTime() : int {
    return (time() + $this->expireIn);
  }
}