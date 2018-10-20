<?php

namespace view;

class TopMenuView {
  
  /**
   * Render out the Top Menu.
   * Since the assignment tester seems to be written in prehistoric times, we cannot use the <header>-tag.
   * @var stoneAge Whether the HTML should be with a DIV-tag (to support prehistoric browsers) or a header-tag for HTML5.
   */
  public function getHTML(bool $stoneAge = false) : string {
    return '
    ' . ($stoneAge ? '<div class="header">' : '<header>') . 
    '<ul>
      <li><a href="/">Home</a></li>
      <li><a href="/login">Login</a></li>
      <li><a href="/login/?forum">Forum</a></li>
      <li><a href="https://github.com/nidawi/1DV610-login">Github</a></li>
    </ul>'
    . ($stoneAge ? '</div>' : '</header>') . '';
  }
}