<?php

namespace Login\view;

class RegisterView extends ViewTemplate {
	private static $register = "RegisterView::Register";
  private static $username = "RegisterView::UserName";
  private static $password = "RegisterView::Password";
  private static $passwordRepeat = "RegisterView::PasswordRepeat";
	private static $messageId = "RegisterView::Message";
	private static $path = "register";

	public function isRegisterRequest() : bool {
		return $this->isRequestGETHeaderPresent(self::$path);
	}
	public function getUsername() : string {
		return isset($_POST[self::$username]) && strlen($_POST[self::$username]) > 0 ? $_POST[self::$username] : "";
	}
	/**
	 * Returns the user's entered username stripped of all "invalid" characters.
	 * This includes PHP's strip_tags as well as anything that is a "non-word" character.
	 * I.e. The only accepted characters for a username are: a-z, 0-9, and _.
	 */
	public function getStrippedUsername() : string {
    return preg_replace("/\W/i", "", strip_tags($this->getUsername()));
  }
	public function getPassword() : string {
		return isset($_POST[self::$password]) && strlen($_POST[self::$password]) > 0 ? $_POST[self::$password] : "";
	}
	public function getRepeatedPassword() : string {
		return isset($_POST[self::$passwordRepeat]) && strlen($_POST[self::$passwordRepeat]) > 0 ? $_POST[self::$passwordRepeat] : "";
	}
	public function hasMatchingPasswordInput() : bool {
		$password = $this->getPassword();
		$passwordRepeat = $this->getRepeatedPassword();

		return ($password !== "" && $passwordRepeat !== "") && ($password === $passwordRepeat);
	}

	public function getHTML(string $message = "", array $locals = array()) {
		return '
		<h2>Register new user</h2>
		<form action="?register" method="post" enctype="multipart/form-data">
			<fieldset>
			<legend>Register a new user - Write username and password</legend>
				<p id="' . self::$messageId . '">' . $message . '</p>
				<label for="' . self::$username . '" >Username :</label>
				<input type="text" size="20" name="' . self::$username . '" id="' . self::$username . '" value="' . $this->getLocalFromArray($locals, "username") . '" />
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
}