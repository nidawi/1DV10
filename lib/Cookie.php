<?php

namespace lib;

/**
 * Cookie Wrapper v1
 */
class Cookie {
  public static $defaultExpirationTime = (60 * 60 * 24 * 3); //Default cookie lifespan is 3 days.
  private static $defaultEncryptionMethod = "AES-128-CBC";
  private static $defaultCookiePath = "/";

  private $name;
  private $content;
  private $expireIn;
  private $encryptionMethod = "AES-128-CBC";

  private function getExpirationTime() : int {
    return (time() + $this->expireIn);
  }
  private function generateIv() {
    $ivLength = openssl_cipher_iv_length($this->encryptionMethod);
    return openssl_random_pseudo_bytes($ivLength);
  }

  public function getContent() {
    return $this->content;
  }

  public function setName(string $name) {
    if (strlen($name) < 1) throw new \Exception("Cookie name is too short (<1)");
    $this->name = $name;
  }
  public function setContent($content) {
    if (!isset($content)) throw new \Exception("No cookie content was provided");
    $this->content = $content;
  }
  public function setExpireIn(int $expireIn) {
    if ($expireIn < 1) throw new \Exception("Cannot set a cookie in the past");
    $this->expireIn = $expireIn;
  }
  public function setEncryptionMethod(string $sslCipherMethod) {
    if (in_array($sslCipherMethod, openssl_get_cipher_methods()))
      $this->encryptionMethod = $sslCipherMethod;
    else
      throw new \Exception("{$sslCipherMethod} is not a valid ssl cipher method");
  }

  public function isSet() : bool {
    return (isset($_COOKIE[$this->name]));
  }

  public function __construct(string $name, $content, int $expireIn = NULL) {
    $this->setName($name);
    $this->setContent($content);
    $this->setExpireIn($expireIn ?? self::$defaultExpirationTime);
  }

  public function encrypt(string $key) {
    // We do not really need any more security than this for the time being.
    // This can be extended at a later date to include various other safety features such as sha256 hashes and what have you.
    $iv = $this->generateIv();
    $encrypted = openssl_encrypt($this->content, $this->encryptionMethod, $key, $options=OPENSSL_RAW_DATA, $iv);
    $this->content = base64_encode($encrypted . $iv);
  }
  public function decrypt(string $key) {
    $decoded = base64_decode($this->content);
    $iv = substr($decoded, - strlen($this->generateIv()));
    $encrypted = substr($decoded, 0, strlen($iv));
    $decrypted = openssl_decrypt($encrypted, $this->encryptionMethod, $key, $options=OPENSSL_RAW_DATA, $iv);
    $this->content = $decrypted;
  }
  public function set(string $cookiePath = NULL) {
    // HTTPOnly is currently turned off for the purpose of the assignment tests.
    setcookie($this->name, $this->content, $this->getExpirationTime(), $cookiePath ?? self::$defaultCookiePath, "", TRUE);
  }
  public function delete($cookiePath = NULL) {
    if ($this->isSet())
      setcookie($this->name, "", time() - 3600, $cookiePath ?? self::$defaultCookiePath);
  }
  public function unset() {
    if ($this->isSet())
      unset($_COOKIE[$this->name]);
  }

  public static function loadCookie(string $cookieName) {
    if (self::isCookieSet($cookieName)) {
      // Since we cannot retrieve cookie expiration time we will simply use the default expiration in a sense of "refreshing"
      return new Cookie($cookieName, $_COOKIE[$cookieName]);
    } else {
      throw new \Exception("Cookie {$cookieName} isn't set");
    }
  }
  public static function isCookieSet(string $cookieName) : bool {
    return (isset($_COOKIE[$cookieName]));
  }
}