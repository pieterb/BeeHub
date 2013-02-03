<?php
?>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="/system/">BeeHub<sup><small><strong><em>RC1</em></strong></small></sup></a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li id="navbar-li-profile"><a href="<?= htmlspecialchars(BeeHub_ACL_Provider::inst()->CURRENT_USER_PRINCIPAL, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>">Profile</a></li>
              <li id="navbar-li-groups"><a href="<?= htmlspecialchars(BeeHub::$CONFIG['webdav_namespace']['groups_path'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>">Groups</a></li>
              <li id="navbar-li-sponsors"><a href="<?= htmlspecialchars(BeeHub::$CONFIG['webdav_namespace']['sponsors_path'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>">Sponsors</a></li>
              <li id="navbar-li-files"><a href="/">Files</a></li>
            </ul>
            <ul class="nav pull-right">
              <li><a href="#">Sign in</a></li>
              <li><a href="#">Log in</a></li>
              <li class="beehub-spacer-surfsara-logo visible-desktop"></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="beehub-spacer-navbar-fixed-top visible-desktop"></div>