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

	private $currentAccount;
	
	public function __construct($account) {
		$this->currentAccount = $account;
	}

	private function generateLogoutButtonHTML(string $message = "") : string {
		return '
			<form  method="POST">
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}
	private function generateLoginFormHTML(string $message = "", array $locals = array()) : string {
		return '
			<form method="POST" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getLocalFromArray($locals, "username") . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '"' . (isset($locals["keep"]) ? " checked" : "") . ' />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}

	public function isLoginRequest() : bool {
		return $this->isRequestPOSTHeaderPresent(self::$login);
	}
	public function isLogoutRequest() : bool {
		return $this->isRequestPOSTHeaderPresent(self::$logout);
	}
	public function areCookiesSet() : bool {
		return (\lib\Cookie::isCookieSet(self::$cookieName) && \lib\Cookie::isCookieSet(self::$cookiePassword));
	}
	public function getUsernameCookie() {
		try {
			return \lib\Cookie::loadCookie(self::$cookieName);
		} catch (\Exception $err) {
			return new \lib\Cookie(self::$cookieName, $this->getUsername());
		}
	}
	public function getPasswordCookie() {
		try {
			return \lib\Cookie::loadCookie(self::$cookiePassword);
		} catch (\Exception $err) {
			return new \lib\Cookie(self::$cookiePassword, $this->getPassword());
		}
	}
	public function hasProvidedCredentials() : bool {
		return isset($_POST[self::$name]) && strlen($_POST[self::$name]) > 0 && isset($_POST[self::$password]) && strlen($_POST[self::$password]) > 0;
	}
	public function getUsername() : string {
		return isset($_POST[self::$name]) && strlen($_POST[self::$name]) > 0 ? $_POST[self::$name] : "";
	}
	public function getPassword() : string {	
		return isset($_POST[self::$password]) && strlen($_POST[self::$password]) > 0 ? $_POST[self::$password] : "";
	}
	public function isToRemainLoggedIn() : bool {
		return isset($_POST[self::$keep]) ? ($_POST[self::$keep] === "on") : false;
	}

	/**
	 * Returns the HTML representation of this view.
	 */
	public function getHTML(string $message = "", array $locals = array()) : string {
		return ($this->currentAccount !== null) ? $this->generateLogoutButtonHTML($message) : $this->generateLoginFormHTML($message, $locals);
	}
}