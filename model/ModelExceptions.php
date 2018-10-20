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

// General Exceptions
class NotImplementedException extends \Exception {}

// Forum Exceptions
class ThreadTitleTooShortException extends \Exception {}
class ThreadTitleTooLongException extends \Exception {}
class ThreadDoesNotExistException extends \Exception {}
class PostBodyTooShortException extends \Exception {}
class PostBodyTooLongException extends \Exception {}
class PostDoesNotExistException extends \Exception {}
class InvalidPostException extends \Exception {}
class InvalidThreadException extends \Exception {}
class ThreadHasNoPostsException extends \Exception {}