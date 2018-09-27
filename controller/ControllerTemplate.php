<?php

namespace Login\controller;

/**
 * This is an abstract class representing a controller template.
 * It contains certain mutual functions between all controllers of the application,
 * as well as dictating the layout of them. 
 */
abstract class ControllerTemplate {
  private $session;
  private $accountRegister;

  public function __construct(\lib\SessionStorage $session, \Login\model\AccountRegister $register) {
    $this->session = $session;
    $this->accountRegister = $register;
  }
  /**
   * This will SET (read: overwrite) the currently stored locals for the session.
   */
  protected function setLocals(array $locals) {
    $this->session->saveEntry(\Login\ENV::$sessionLocalsId, $locals);
  }
  /**
   * This will add a local value with the given id to the currently stored locals for the session.
   * This will overwrite any other item at the same id.
   */
  protected function addLocal(string $id, $value) {
    $arr = $this->getLocals();
    $arr[$id] = $value;
    $this->setlocals($arr);
  }
  /**
   * Returns all currently stored locals as an associative array (id => value);
   */
  protected function getLocals() : array {
    $locals = $this->session->loadEntry(\Login\ENV::$sessionLocalsId);
    if (isset($locals)) return $locals;
    
    return array();
  }
  protected function clearLocals() {
    $this->session->deleteEntry(\Login\ENV::$sessionLocalsId);
  }
  protected function setFlashMessage(string $message) {
    $this->session->saveEntry(\Login\ENV::$sessionFlashMessageId, $message);
  }
  /**
   * Returns the current flash message, if any.
   * Returns an empty string is no message is set.
   */
  protected function getFlashMessage() : string {
    return $this->session->loadEntry(\Login\ENV::$sessionFlashMessageId) ?? "";
  }
  protected function clearFlashMessage() {
    $this->session->deleteEntry(\Login\ENV::$sessionFlashMessageId);
  }
  protected function getAndClearFlashMessage() : string {
    $flashMsg = $this->getFlashMessage();
    if (isset($flashMsg)) {
      $this->clearFlashMessage();
      return $flashMsg;
    }

    return "";
  }
  protected function setLoggedInAccount(\Login\Model\Account $account) {
    // Save entry to session.
    $this->session->saveEntry(\Login\ENV::$sessionCurrentUserId, $account);
    // Update the account's Last Logged In date
    $this->accountRegister->updateAccount($account, array());
  }
  protected function unsetLoggedInAccount() {
    $this->session->deleteEntry(\Login\ENV::$sessionCurrentUserId);
  }
}
