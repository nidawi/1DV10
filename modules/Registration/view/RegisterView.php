<?php

namespace Registration\view;

require_once 'PasswordsDoNotMatchException.php';

class RegisterView extends \Login\view\ViewTemplate {

  private static $register = "RegisterView::Register";
  private static $username = "RegisterView::UserName";
  private static $password = "RegisterView::Password";
  private static $passwordRepeat = "RegisterView::PasswordRepeat";
  private static $messageId = "RegisterView::Message";

  public function __construct(\lib\SessionStorage $session) {
    parent::__construct($session);
  }

  public function userWantsToRegisterNewAccount() : bool {
    return $this->isRequestPOSTHeaderPresent(self::$register) && $this->areRegistrationFieldsSet();
  }

  public function getUsername() : \Login\model\Username {
    return new \Login\model\Username($_POST[self::$username] ?? "");
  }
  public function getPassword() : \Login\model\Password {
    $this->assertMatchingPasswordInputs();
    return new \Login\model\Password($_POST[self::$password] ?? "");
  }

  /**
   * Signals that the registration was successful. This will notify the user,
   * refresh the page, and destroy the call stack with die().
   */
  public function registrationSuccessful() {
    $this->saveUsername();
    $this->displayRegistrationSuccessful();
    $this->end(true);
  }

	/**
   * Signals that the registration was unsuccessful. This will notify the user of the issues,
   * refresh the page, and destroy the call stack with die().
   */
  public function registrationUnsuccessful(\Exception $err) {
    $this->saveUsername();
    $this->displayErrors($err);
    $this->end(false);
  }

  public function getHTML() {
    return '
    <h2>Register new user</h2>
    <form action="?' . $this->getRegisterLink() . '" method="post" enctype="multipart/form-data">
      <fieldset>
        <legend>Register a new user - Write username and password</legend>
        <p id="' . self::$messageId . '">' . $this->getDisplayMessage() . '</p>
        <label for="' . self::$username . '" >Username :</label>
        <input type="text" size="20" name="' . self::$username . '" id="' . self::$username . '" value="' . $this->getStoredUsername() . '" />
        <br/>
        <label for="' . self::$password . '" >Password  :</label>
        <input type="password" size="20" name="' . self::$password . '" id="' . self::$password . '" value="" />
        <br/>
        <label for="' . self::$passwordRepeat . '" >Repeat password  :</label>
        <input type="password" size="20" name="' . self::$passwordRepeat . '" id="' . self::$passwordRepeat . '" value="" />
        <br/>
        <input id="submit" type="submit" name="' . self::$register . '"  value="Register" />
        <br/>
      </fieldset>
    </form>
    ';
  }

  private function areRegistrationFieldsSet() : bool {
    return isset($_POST[self::$username]) && isset($_POST[self::$password]) && isset($_POST[self::$passwordRepeat]);
  }

  private function assertMatchingPasswordInputs() : bool {
    if (!$this->hasMatchingPasswordInput())
      throw new PasswordsDoNotMatchException();
    else
      return true;
  }

  private function hasMatchingPasswordInput() : bool {
    $password = $_POST[self::$password];
    $passwordRepeat = $_POST[self::$passwordRepeat];

    return $password === $passwordRepeat;
  }

  private function saveUsername() {
    // todo: we kind of want to inherit this regexp from the model
    $username = preg_replace("/\W/i", "", strip_tags($_POST[self::$username] ?? ""));
    $this->setStoredUsername($username);
  }

  private function displayRegistrationSuccessful() {
    $this->setDisplayMessage("Registered new user.");
  }

  private function displayErrors(\Exception $error = null) {
    $errors = $this->getProblems();
    // Add additional error
    if (isset($error)) $errors[] = $error;

    // Present Errors
    $messageToDisplay = $this->getProblemsString($errors);

    $this->setDisplayMessage($messageToDisplay);
  }

  private function end(bool $registrationSuccessful) {
    $this->redirect(null, $registrationSuccessful);
    die(); // This destroys the call stack.
  }

  private function getProblems() : array {
    // Todo: consider alternatives such as non-throwing boolean-based checks
    $errors = array();

    try { $this->getUsername(); }
    catch (\Exception $err) { $errors[] = $err; }
    try { $this->getPassword(); }
    catch (\Exception $err) { $errors[] = $err; }

    return $errors;
  }

  private function getProblemsString(array $problems) : string {
    return array_reduce($this->getErrorStringList($problems), function ($a, $b) { return $a . $b; }, "");
  }

  private function getErrorStringList(array $errList) : array {
    $errorStringArray = array();
    foreach ($errList as $err) {
      $errorStringArray[] = $this->interpretException($err);
    }

    return array_unique($errorStringArray);
  }

  private function interpretException(\Exception $err) : string {
    $usernameMinLength = \Login\model\Username::USERNAME_MIN_LENGTH;
    $passwordMinLength = \Login\model\Password::PASSWORD_MIN_LENGTH;

    switch (true) {
      case $err instanceof \Login\model\AccountAlreadyExistsException:
        return "User exists, pick another username.<br>";
      case $err instanceof \Login\model\UsernameTooShortException:
        return "Username has too few characters, at least " . $usernameMinLength . " characters.<br>";
      case $err instanceof \Login\model\UsernameContainsInvalidCharactersException:
        return "Username contains invalid characters.<br>";
      case $err instanceof \Login\model\PasswordTooShortException:
        return "Password has too few characters, at least " . $passwordMinLength . " characters.<br>";
      case $err instanceof \Registration\view\PasswordsDoNotMatchException:
        return "Passwords do not match.<br>";
      default:
        return "Unknown error.<br>";
    }
  }
}