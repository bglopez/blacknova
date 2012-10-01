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
// File: logout.php

include './global_includes.php';

// New database driven language entries
load_languages($db, $lang, array('logout', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'), $langvars);

$title = $l_logout;
$_SESSION['logged_in'] = false;

// Clear the session array, clear the session cookie
$_SESSION = array();
setcookie ("PHPSESSID", "", 0, "/");

// Destroy the session entirely
session_destroy ();

include './header.php';
echo "<h1>" . $title . "</h1>\n";

if (isset ($_SESSION['username']))
{
    $current_score = 0;
    $result = $db->Execute("SELECT ship_id FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
    db_op_result ($db, $result, __LINE__, __FILE__);
    $playerinfo = $result->fields;
    include_once './includes/calc_score.php';
    $current_score = calc_score ($db, $playerinfo['ship_id']);
    player_log ($db, $playerinfo['ship_id'], LOG_LOGOUT, $ip);
    echo $l_logout_score . " " . $current_score . ".<br>";
    $l_logout_text = str_replace ("[name]", $_SESSION['username'], $l_logout_text);
    $l_logout_text = str_replace ("[here]", "<a href='index.php'>" . $l_here . "</a>", $l_logout_text);
    echo $l_logout_text;
}
else
{
    echo str_replace("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);
}

include './footer.php';
?>
