<?php

namespace Login\model;

// Account Exceptions
class AccountDoesNotExistException extends \Exception {}
class AccountAlreadyExistsException extends \Exception {}
class UsernameTooShortException extends \Exception {}
class UsernameContainsInvalidCharactersException extends \Exception {}
class PasswordTooShortException extends \Exception {}
class UsernameMissingException extends \Exception {}
class PasswordMissingException extends \Exception {}
class InvalidCredentialsException extends \Exception {}
class TemporaryPasswordExpiredException extends \Exception {}
class TemporaryPasswordDoesNotExistException extends \Exception {}
class NoAccountLoggedInException extends \Exception {}

// General Exceptions
class NotImplementedException extends \Exception {}