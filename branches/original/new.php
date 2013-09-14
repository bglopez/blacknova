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
// File: new.php

include './global_includes.php';

if (!isset ($_GET['lang']))
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

// Database driven language entries
$langvars = null;
$langvars = BntTranslate::load ($db, $lang, array ('new', 'login', 'common', 'global_includes', 'global_funcs', 'footer', 'news', 'index'));

$variables = null;
$variables['lang'] = $lang;
$variables['link'] = $link;
$variables['link_forums'] = $bntreg->get("link_forums");
$variables['admin_mail'] = $admin_mail;
$variables['body_class'] = 'new';

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvars";

// Pull in footer variables from footer_t.php
include './footer_t.php';
$template->AddVariables ('langvars', $langvars);
$template->AddVariables ('variables', $variables);
$template->Display ("new.tpl");


/*
include './header.php';
// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('new', 'login', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));
echo "<h1>" . $title . "</h1>\n";

echo "<form action='new2.php" . $link . "' method='post'>\n";
echo "    <dl class='twocolumn-form'>\n";
echo "        <dt style='padding:3px'><label for='username'>" . $langvars['l_login_email'] . ":</label></dt>\n";
echo "        <dd style='padding:3px'><input type='email' id='username' name='username' size='20' maxlength='40' value='' placeholder='someone@example.com' style='width:200px'></dd>\n";
echo "        <dt style='padding:3px'><label for='shipname'>" . $langvars['l_new_shipname'] . ":</label></dt>\n";
echo "        <dd style='padding:3px'><input type='text' id='shipname' name='shipname' size='20' maxlength='20' value='' style='width:200px'></dd>\n";
echo "        <dt style='padding:3px'><label for='character'>" . $langvars['l_new_pname'] . ":</label></dt>\n";
echo "        <dd style='padding:3px'><input type='text' id='character' name='character' size='20' maxlength='20' value='' style='width:200px'></dd>\n";
echo "    </dl>\n";
echo "    <br style='clear:both;'><br>";
echo "    <div style='text-align:center'><input type='submit' value='" . $langvars['l_submit'] . "'>&nbsp;<input type='reset' value='" . $langvars['l_reset'] . "'><br><br>\n";
echo "        " . $langvars['l_new_info'] . "<br></div>\n";
echo "</form>";

include './footer.php';
*/
?>
