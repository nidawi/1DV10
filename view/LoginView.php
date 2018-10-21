<?php

namespace Login\view;

class LoginView extends ViewTemplate {

	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	private static $LOCAL_KEEPLOGGEDIN_ID = "KEEPLOGGEDIN";

	private $accountManager;
	
	public function __construct(\Login\model\AccountManager $accountManager, \lib\SessionStorage $session) {
		parent::__construct($session);
		$this->accountManager = $accountManager;
	}

	public function userWantsToLogin() : bool {
		return $this->hasProvidedCredentials() || $this->isLoginRequest();
	}
	public function userWantsToLogout() : bool {
		return $this->isLogoutRequest();
	}

	public function getProvidedCredentials() : \Login\model\AccountCredentials {
		return new \Login\model\AccountCredentials($this->getUsername(), $this->getPassword(), $this->userWantsToBeRemembered());
	}

	/**
	 * Signals that the login was successful. This will complete view-related
	 * activities, refresh the page, and destroy the call stack with die().
	 */
	public function loginSuccessful() {
		$this->rememberUser();
		$this->clearState();
		$this->displayLoginMessage();
		$this->end();
	}

	/**
	 * Signals that the login was unsuccessful. This will notify the user
	 * of the issues, refresh the page, and destroy the call stack with die().
	 */
	public function loginUnsuccessful(\Exception $err) {
		$this->saveState();
		$this->clearCookies();
		$this->displayError($err);
		$this->end();
	}
	
	/**
	 * Signals that the logout was unsuccessful. This will clean up any
	 * remnants of the user, refresh the page, and destroy the call stack with die().
	 */
	public function logoutSuccessful() {
		$this->clearCookies();
    $this->displayLogoutMessage();
    $this->end();
	}

	public function getHTML() : string {
		return $this->accountManager->isLoggedIn() ? $this->generateLogoutButtonHTML() : $this->generateLoginFormHTML();
	}

	private function rememberUser() {
		if ($this->userWantsToBeRemembered())
			$this->setCookies();
	}

	private function end() {
		// TODO: find a better solution for this.
		// This is essentially an attempt at abstracting the redirection required for this user interface.
		$this->redirect(null, $this->isLogoutRequest() ? true : false);
		die(); // This destroys the call stack.
	}

	private function displayLoginMessage() {
		if ($this->areCookiesSet())
		  $this->setDisplayMessage("Welcome back with cookie");
		else
		  $this->setDisplayMessage("Welcome" . ($this->isToRemainLoggedIn() ? " and you will be remembered" : ""));
	}
	private function displayLogoutMessage() {
		$this->setDisplayMessage("Bye bye!");
	}
	private function displayError(\Exception $err) {
		$this->setDisplayMessage($this->interpretException($err));
	}

	private function userWantsToBeRemembered() : bool {
		return $this->isToRemainLoggedIn() || $this->areCookiesSet();
	}
	private function hasProvidedCredentials() : bool {
		return $this->areCookiesSet() || $this->areCredentialsSet();
	}
	private function isLoginRequest() : bool {
		return $this->isRequestPOSTHeaderPresent(self::$login);
	}
	private function isLogoutRequest() : bool {
		return $this->isRequestPOSTHeaderPresent(self::$logout);
	}
	private function isToRemainLoggedIn() : bool {
		return isset($_POST[self::$keep]) ? ($_POST[self::$keep] === "on") : false;
	}
	private function areCredentialsSet() : bool {
		return isset($_POST[self::$name]) && strlen($_POST[self::$name]) > 0 && isset($_POST[self::$password]) && strlen($_POST[self::$password]) > 0;
	}

	private function saveState() {
		$this->saveUsername();
		$this->saveKeepLoggedIn();
	}
	private function clearState() {
		$this->clearStoredUsername();
		$this->clearKeepLoggedIn();
	}
	private function clearKeepLoggedIn() {
		$this->deleteLocal(self::$LOCAL_KEEPLOGGEDIN_ID);
	}

	private function saveUsername() {
		$this->setStoredUsername($this->getUsername());
	}
	private function saveKeepLoggedIn() {
		if ($this->isToRemainLoggedIn())
			$this->addLocal(self::$LOCAL_KEEPLOGGEDIN_ID, "");
	}

	private function getKeepLoggedIn() : bool {
		$keepChecked = $this->hasLocal(self::$LOCAL_KEEPLOGGEDIN_ID);
		return $keepChecked;
	}
	private function getUsername() : string {
		if ($this->areCookiesSet())
			return $this->getUsernameCookie()->getContent();
		else
			return $_POST[self::$name] ?? "";
	}
	private function getPassword() : string {
		if ($this->areCookiesSet())
			return $this->getPasswordCookie()->getContent();
		else
			return $_POST[self::$password] ?? "";
	}

	// Cookie Methods - these have a dependency to \lib\Cookie.php
	// These have been wrapped into this View to reduce coupling
	private function areCookiesSet() : bool {
		return (\lib\Cookie::isCookieSet(self::$cookieName) && \lib\Cookie::isCookieSet(self::$cookiePassword));
	}
	private function getUsernameCookie() {
		try {
			return \lib\Cookie::loadCookie(self::$cookieName);
		} catch (\Exception $err) {
			return new \lib\Cookie(self::$cookieName, '');
		}
	}
	private function getPasswordCookie() {
		try {
			$passwordCookie = \lib\Cookie::loadCookie(self::$cookiePassword);
			// $passwordCookie->decrypt(\Login\ENV::$cookieEncryptionKey);
			return $passwordCookie;
		} catch (\Exception $err) {
			return new \lib\Cookie(self::$cookiePassword, '');
		}
	}
	private function setCookies() {
		$usernameCookie = new \lib\Cookie(self::$cookieName, $this->accountManager->getLoggedInAccount()->getUsername());
		$passwordCookie = new \lib\Cookie(self::$cookiePassword, $this->accountManager->getLoggedInAccount()->getTemporaryPassword());

		$usernameCookie->set();
		// $passwordCookie->encrypt(\Login\ENV::$cookieEncryptionKey);
		$passwordCookie->set();
	}
	private function clearCookies() {
		$usernameCookie = $this->getUsernameCookie();
		$passwordCookie = $this->getPasswordCookie();

		$usernameCookie->delete();
		$passwordCookie->delete();
	}

	private function interpretException(\Exception $err) : string {
		// Translates exception type to string
		// If cookies are set, the issue will always be related to them.
		if ($this->areCookiesSet())
			return "Wrong information in cookies";

		switch (true) {
			case $err instanceof \Login\model\PasswordMissingException:
				return "Password is missing";
			case $err instanceof \Login\model\UsernameMissingException:
				return "Username is missing";
			case $err instanceof \Login\model\InvalidCredentialsException:
			case $err instanceof \Login\model\AccountDoesNotExistException:
				return "Wrong name or password";
			default:
			  return "Unknown error";
		}
	}

	private function generateLogoutButtonHTML() : string {
		return '
			<form  method="POST">
				<p id="' . self::$messageId . '">' . $this->getDisplayMessage() .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}

	private function generateLoginFormHTML() : string {
		return '
			<form method="POST" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $this->getDisplayMessage() . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getStoredUsername() . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '"' . ($this->getKeepLoggedIn() ? " checked" : "") . ' />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}
}