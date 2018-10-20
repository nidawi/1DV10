<?php

date_default_timezone_set(\Login\ENV::DEFAULT_TIME_ZONE);

if (\Login\ENV::APPLICATION_STATUS === "development") {
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  ini_set("error_log", "/var/log/php-errors.log");
}