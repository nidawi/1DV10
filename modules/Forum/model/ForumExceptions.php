<?php

namespace Forum\model;

class ThreadTitleTooShortException extends \Exception {}
class ThreadTitleTooLongException extends \Exception {}
class ThreadDoesNotExistException extends \Exception {}
class InvalidThreadException extends \Exception {}
class ThreadHasNoPostsException extends \Exception {}
class PostBodyTooShortException extends \Exception {}
class PostBodyTooLongException extends \Exception {}
class PostDoesNotExistException extends \Exception {}
class InvalidPostException extends \Exception {}