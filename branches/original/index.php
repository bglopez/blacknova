<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: index.php

$index_page = true;
include 'config/config.php';

if (!isset($_GET['lang']))
{
    $_GET['lang'] = null;
    $lang = $default_lang;
    $link = '';
}
else
{
    $lang = $_GET['lang'];
    $link = "?lang=" . $lang;
}

// Check to see if the language database has been installed yet.
$result = $db->Execute("SELECT name, value FROM {$db->prefix}languages WHERE category=? AND language=?;", array('common', $lang));
if (!$result)
{
    // If not, redirect to create_universe.
    header("Location: create_universe.php");
    die ();
}

// New database driven language entries
load_languages($db, $lang, array('main', 'login', 'logout', 'index'), $langvars, $db_logging);

$title = $l_welcome_bnt;
$body_class = 'index';

include 'header.php';
?>

<div class="index-header"><img class="index" src="images/header1.png" style="width:994px; height:150px" alt="Blacknova Traders"></div>
<div class="index-flags">
<a href="index.php?lang=french"><img style="width: 24px; height:16px" src="images/flags/France.png" alt="French"></a>
<a href="index.php?lang=german"><img style="width: 24px; height:16px" src="images/flags/Germany.png" alt="German"></a>
<a href="index.php?lang=spanish"><img style="width: 24px; height:16px" src="images/flags/Mexico.png" alt="Spanish"></a>
<a href="index.php?lang=english"><img style="width: 24px; height:16px" src="images/flags/United_States_of_America.png" alt="American English"></a></div>
<div class="index-header-text">Blacknova Traders</div>
<br>
<h2 style="display:none">Navigation</h2>
<div class="navigation" role="navigation">
<ul class="navigation">
<li class="navigation"><a href="new.php<?php echo $link; ?>"><span class="button blue"><span class="shine"></span><?php echo $l_new_player; ?></span></a></li>
<li class="navigation"><a href="mailto:<?php echo $admin_mail; ?>"><span class="button gray"><span class="shine"></span><?php echo $l_login_emailus; ?></span></a></li>
<li class="navigation"><a href="ranking.php<?php echo $link; ?>"><span class="button purple"><span class="shine"></span><?php echo $l_rankings; ?></span></a></li>
<li class="navigation"><a href="faq.php<?php echo $link; ?>"><span class="button brown"><span class="shine"></span><?php echo $l_faq; ?></span></a></li>
<li class="navigation"><a href="settings.php<?php echo $link; ?>"><span class="button red"><span class="shine"></span><?php echo $l_settings; ?></span></a></li>
<li class="navigation"><a href="<?php echo $link_forums; ?>" target="_blank"><span class="button orange"><span class="shine"></span><?php echo $l_forums; ?></span></a></li>
</ul></div><br style="clear:both">
<div><p></p></div>
<div class="index-welcome">
<h1 class="index-h1"><?php echo $l_welcome_bnt; ?></h1>
<p><?php echo $l_bnt_description; ?><br></p>
<form action="login2.php<?php echo $link; ?>" method="post">
<dl class="twocolumn-form">
<dt><label for="email"><?php echo $l_login_email; ?></label></dt>
<dd><input type="email" id="email" name="email" size="20" maxlength="40"></dd>
<dt><label for="pass"><?php echo $l_login_pw; ?></label></dt>
<dd><input type="password" id="pass" name="pass" size="20" maxlength="20"></dd>
</dl>
<br style="clear:both">
<div style="text-align:center"><?php echo $l_login_forgotpw; ?></div><br>
<div style="text-align:center">
<input class="button green" type="submit" value="<?php echo $l_login_title; ?>">
</div>
</form>
<br>
<p class="cookie-warning"><?php echo $l_cookie_warning; ?></p></div>
<br>
<?php include "footer.php"; ?>
