<?php

namespace Login\view;

class PostView extends ViewTemplate {

  // TODO: finish this
  private $postToDisplay;
  private $inheritedURL;

  // TODO: find a better solution for determining current location
  public function __construct(\lib\SessionStorage $session, string $location) {
    parent::__construct($session);
    $this->inheritedURL = $location;
  }

  public function getHTML() : string {
    return '
    
    ';
  }
}