<?php

namespace Login\view;

/**
 * Abstract Parent class for Login-project views.
 */
abstract class ViewTemplate {

  /**
   * A helper method to retrieve a value from an array at a certain index/identifier. Returns an empty string if the value is not set for the array.
   */
  protected function getLocalFromArray(array $arr, string $id) {
    return isset($arr[$id]) ? $arr[$id] : "";
  }

  /**
   * A helper method to check if the specified request (GET) header is set.
   */
  protected function isRequestGETHeaderPresent(string $header) : bool {
    return array_key_exists($header, $_GET);
  }

  protected function getRequestMethod() : string {
    return $_SERVER['REQUEST_METHOD'];
  }
  protected function isRequestMethod(string $method) : bool {
    return $this->getRequestMethod() === $method;
  }
  protected function getQueryString(bool $prependQuestionmark = true) : string {
    if (isset($_SERVER["QUERY_STRING"]))
      return $prependQuestionmark ? "?" . $_SERVER["QUERY_STRING"] : $_SERVER["QUERY_STRING"];
    else
      return "";
  }
  protected function hasQueryString(string $key) : bool {
    return preg_match("/&?" . $key . "/", $this->getQueryString(false)) ? true : false;
  }


  /**
   * A helper method to check if the specified request (POST) header is set.
   */
  protected function isRequestPOSTHeaderPresent(string $header) : bool {
    return array_key_exists($header, $_POST);
  }


  public function redirect(string $url, bool $stripQuery = false) {
    header('Location: ' . $url . (!$stripQuery ? $this->getQueryString(true) : ""));
    die();
  }
}