<?php

namespace Login\view;

/**
 * Abstract Parent class for Login-project views.
 * Grants access to Locals, Message Storage, and other helper methods.
 */
abstract class ViewTemplate {

  private $session;

  private static $registerLink = "register";
  private static $forumLink = "forum";

  private static $usernameLocalId = "USERNAME";
  private static $displayMessageLocalId = "MESSAGE";

  public function __construct(\lib\SessionStorage $session) {
    $this->session = $session;
  }

  public function userWantsToRegister() : bool {
    return $this->isRequestGETHeaderPresent(self::$registerLink);
  }
  public function userWantsToViewForum() : bool {
    return $this->isRequestGETHeaderPresent(self::$forumLink);
  }

  public function isGETRequest() : bool {
    return $this->isRequestMethod("GET");
  }
  public function isPOSTRequest() : bool {
    return $this->isRequestMethod("POST");
  }

  // General Helpers
  protected function isRequestGETHeaderPresent(string $header) : bool {
    return array_key_exists($header, $_GET);
  }

  protected function isRequestPOSTHeaderPresent(string $header) : bool {
    return array_key_exists($header, $_POST);
  }

  protected function isValidRequestMethod() : bool {
    return ($this->isRequestMethod("POST") || $this->isRequestMethod("GET"));
  }

  protected function getRequestURI() : string {
    // We don't want to include any query strings here.
    $request = $_SERVER["REQUEST_URI"];
    return substr($request, 0, strrpos($request, "/") + 1);
  }

  protected function getQueryString(bool $prependQuestionmark = true) : string {
    $queryString = $_SERVER["QUERY_STRING"];

    if (isset($queryString))
      return $prependQuestionmark ? "?" . $queryString : $queryString;
    else
      return "";
  }

  protected function hasQueryString(string $key) : bool {
    return preg_match("/&?" . $key . "/", $this->getQueryString(false)) ? true : false;
  }

  protected function hasEmptyQueryString(string $key) : bool {
    return $this->hasQueryString($key) && ($_GET[$key] === "");
  }

  protected function redirect(string $url = null, bool $stripQuery = false) {
    $targetURI = $url ?? $this->getRequestURI();
    header('Location: ' . $targetURI . (!$stripQuery ? $this->getQueryString(true) : ""));
  }

  protected function getRegisterLink() : string {
    return self::$registerLink;
  }
  protected function getForumLink() : string {
    return self::$forumLink;
  }

  // Locals used to carry values over between redirects
  /**
   * This will add a local value with the given id to the currently stored locals for the session.
   * This will overwrite any other item at the same id.
   */
  protected function addLocal(string $id, $value) {
    $arr = $this->getLocals();
    $arr[$id] = $value;
    $this->setlocals($arr);
  }

  protected function getLocal(string $id, bool $alsoClear = true) : string {
    $arr = $this->getLocals();
    $value = isset($arr[$id]) ? $arr[$id] : "";
    if ($alsoClear) $this->deleteLocal($id);
    return $value;
  }

  protected function hasLocal(string $id) : bool {
    $arr = $this->getLocals();
    return isset($arr[$id]);
  }

  protected function deleteLocal(string $id) {
    $arr = $this->getLocals();
    unset($arr[$id]);
    $this->setlocals($arr);
  }

  protected function clearLocals() {
    $this->session->deleteEntry(\Login\ENV::$sessionLocalsId);
  }

  // Persistent Messages
  protected function setDisplayMessage(string $message) {
    $this->session->saveEntry(self::$displayMessageLocalId, $message);
  }

  protected function getDisplayMessage(bool $alsoClear = true) : string {
    $message = $this->session->loadEntry(self::$displayMessageLocalId);
    if ($alsoClear) $this->clearDisplayMessage();
    return isset($message) ? $message : "";
  }

  protected function clearDisplayMessage() {
    $this->session->deleteEntry(self::$displayMessageLocalId);
  }

  // Quick Methods for Persistent Usernames
  protected function setStoredUsername(string $username) {
    $this->addLocal(self::$usernameLocalId, $username);
  }

  protected function getStoredUsername() : string {
    return $this->getLocal(self::$usernameLocalId);
  }

  protected function clearStoredUsername() {
    $this->deleteLocal(self::$usernameLocalId);
  }

  /**
   * This will SET (read: overwrite) the currently stored locals for the session.
   */
  private function setLocals(array $locals) {
    $this->session->saveEntry(\Login\ENV::$sessionLocalsId, $locals);
  }

  private function getLocals() : array {
    $locals = $this->session->loadEntry(\Login\ENV::$sessionLocalsId);
    if (isset($locals)) return $locals;
    
    return array();
  }

  private function getRequestMethod() : string {
    return $_SERVER['REQUEST_METHOD'];
  }

  private function isRequestMethod(string $method) : bool {
    return $this->getRequestMethod() === $method;
  }
}