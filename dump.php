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
// File: dump.php

include './global_includes.php';

if (check_login ($db, $lang, $langvars)) // Checks player login, sets playerinfo
{
    die ();
}

$title = $l_dump_title;
include './header.php';

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('dump', 'main', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'));

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
$playerinfo = $result->fields;

$result2 = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id = ?;", array ($playerinfo['sector']));
$sectorinfo = $result2->fields;
echo "<h1>" . $title . "</h1>\n";

if ($playerinfo['turns'] < 1)
{
    echo $l_dump_turn  . "<br><br>";
    BntText::gotoMain ($db, $lang, $langvars);
    include './footer.php';
    die ();
}

if ($playerinfo['ship_colonists'] == 0)
{
    echo $l_dump_nocol . "<br><br>";
}
elseif ($sectorinfo['port_type'] == "special")
{
    $update = $db->Execute ("UPDATE {$db->prefix}ships SET ship_colonists = 0, turns = turns - 1, turns_used = turns_used + 1 WHERE ship_id = ?;", array ($playerinfo['ship_id']));
    echo $l_dump_dumped . "<br><br>";
}
else
{
    echo $l_dump_nono . "<br><br>";
}

BntText::gotoMain ($db, $lang, $langvars);
include './footer.php';
?>
