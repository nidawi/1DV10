<?php

date_default_timezone_set(\Login\ENV::$defaultTimezone);

if (\Login\ENV::$envApplicationStatus === "development") {
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  ini_set("error_log", "/var/log/php-errors.log");
}