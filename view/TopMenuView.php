<?php

namespace view;

class TopMenuView {
  /**
   * Render out the Top Menu.
   * Since the assignment tester seems to be written in prehistoric times, we cannot use the <header>-tag.
   */
  public function show(bool $stoneAge = FALSE) {
    return '
    ' . ($stoneAge ? '<div class="header">' : '<header>') . 
    '<ul>
      <li><a href="/">Home</a></li>
      <li><a href="/login">Login</a></li>
      <li><a href="https://github.com/nidawi/1DV610-login">Github</a></li>
    </ul>'
    . ($stoneAge ? '</div>' : '</header>') . '';
  }
}

/*
    return '
      ' . $stoneAge == TRUE ? "<div class=\"header\">" : "<header>" . '
        <ul>
          <li><a href="/">Home</a></li>
          <li><a href="/login">Login</a></li>
        </ul>
      ' . $stoneAge == TRUE ? "</div>" : "</header>" . '
    ';

*/
