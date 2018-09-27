<?php

namespace Login\controller;

class LoginController extends ControllerTemplate {
  private $layoutView;
  private $loginView;
  private $currentAccount;
  private $accountRegister;

  public function __construct(\Login\view\LayoutView $lv, \Login\model\AccountRegister $register, $account, \lib\SessionStorage $session) {
    parent::__construct($session, $register);

    $this->layoutView = $lv;
    $this->loginView = new \Login\view\LoginView($account);
    $this->accountRegister = $register;
    $this->currentAccount = $account;    
  }

  private function doLogout() {
    if (isset($this->currentAccount)) {
      // If someone is online, log them out.

      // We need to clear their cookies when they log out.
      if ($this->loginView->areCookiesSet()) {
        $usernameCookie = $this->loginView->getUsernameCookie();
        $passwordCookie = $this->loginView->getPasswordCookie();
        $usernameCookie->delete();
        $passwordCookie->delete();
      }

      $this->unsetLoggedInAccount();
      $this->currentAccount = null;
      $this->setFlashMessage("Bye bye!");
      $this->layoutView->redirect("/login/", true);
      return;
    } else {
      // If no one is online, do nothing.
      $this->setFlashMessage("");
      $this->layoutView->redirect("/login/", true);
      return;
    }
  }
  private function doRefresh() {
    $this->setFlashMessage("");
    $this->layoutView->redirect("/login/", true);
    return;
  }
  private function setRemainLoggedInCookies() {
    // Set cookies
    $usernameCookie = $this->loginView->getUsernameCookie();
    $passwordCookie = $this->loginView->getPasswordCookie();
    $passwordCookie->encrypt(\Login\ENV::$cookieEncryptionKey); // Encrypt the password
    // Consider a different key. Might be a good idea.

    $usernameCookie->set();
    $passwordCookie->set();
  }

  private function runValidators(string $overrideUsername = NULL, string $overridePassword = NULL) {
    $username = $overrideUsername ?? $this->loginView->getUsername();
    $password = $overridePassword ?? $this->loginView->getPassword();
    $providedCredentials = ($overrideUsername !== NULL && $overridePassword !== NULL) ? TRUE : $this->loginView->hasProvidedCredentials();

    if ($username !== "" && $password === "")
      throw new \Exception("Password is missing");
    else if (!$providedCredentials || ($username === "" && $password !== ""))
      throw new \Exception("Username is missing");
    
    try {
      $this->currentAccount = $this->accountRegister->getAccountByUsername($username); // Attempt loading the user if info has been provided. This throws exceptions.
    } catch (\Exception $err) {
      throw new \Exception("Wrong name or password");
    }
    
    if (!$this->currentAccount->isPasswordMatch($password))
      throw new \Exception("Wrong name or password");
  }

  private function doCookieLogin() {
    if ($this->loginView->areCookiesSet()) {
      $usernameCookie = $this->loginView->getUsernameCookie();
      $passwordCookie = $this->loginView->getPasswordCookie();
      $passwordCookie->decrypt(\Login\ENV::$cookieEncryptionKey);

      try {
        $this->runValidators($usernameCookie->getContent(), $passwordCookie->getContent());

        // Check cookie lifetime
        $cookieMaximumLifespan = ($this->currentAccount->getUpdatedAt() + \lib\Cookie::$defaultExpirationTime); // This is the longest a cookie could possibly live.
        // It is based on the last time that the user logged in and had its "updatedat" updated and its cookies refreshed.
        if (time() - $cookieMaximumLifespan > 0) {
          // If the time now minus the lifespan is a positive number, that means that the maximum lifespan has ended and the cookie is invalid.
          throw new \Exception("Cookie maximum lifespan exceeded");
        }

        // Update cookies
        $usernameCookie->set();
        $passwordCookie->encrypt(\Login\ENV::$cookieEncryptionKey);
        $passwordCookie->set();

        $this->setFlashMessage("Welcome back with cookie");
        $this->setLoggedInAccount($this->currentAccount);
        $this->layoutView->redirect("/login/");
      } catch (\Exception $err) {
        $usernameCookie->delete();
        $passwordCookie->delete();
        $this->setFlashMessage("Wrong information in cookies");
        $this->layoutView->redirect("/login/");
      }
    }
  }

  public function doLogin() {
    // Deals with GET
    if ($this->layoutView->isGETRequest()) {
      if ($this->loginView->areCookiesSet() && $this->currentAccount === null) {
        $this->doCookieLogin();
        return;
      }
  
      $this->layoutView->echoHTML($this->currentAccount !== null, $this->loginView->getHTML($this->getAndClearFlashMessage(), $this->getLocals()));
      $this->clearLocals();
      return;
    }

    // First, we deal with logout requests which trigger when 1) you're logged in and 2) LoginView:Logout is present in the POST body.
    if ($this->loginView->isLogoutRequest()) {
      $this->doLogout();
    }
    // Then, nothing should happen if you do this while already logged in.
    if ($this->loginView->isLoginRequest() && isset($this->currentAccount)) {
      $this->doRefresh();
    }

    try {
      $this->runValidators();

      if ($this->loginView->isToRemainLoggedIn()) {
        $this->setRemainLoggedInCookies();
      }

      $this->setFlashMessage("Welcome" . ($this->loginView->isToRemainLoggedIn() ? " and you will be remembered" : ""));
      $this->setLoggedInAccount($this->currentAccount);
      $this->layoutView->redirect("/login/");
    } catch(\Exception $err) {
      $this->addLocal("username", $this->loginView->getUsername());
      if ($this->loginView->isToRemainLoggedIn()) $this->addLocal("keep", "");
      $this->currentAccount = null; // Clear the account if any.
      $this->setFlashMessage($err->getMessage()); // Assign the error message to the session.
      $this->layoutView->redirect("/login/");
    }
  }
}