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
// File: sched_funcs.php

if (preg_match("/sched_funcs.php/i", $_SERVER['PHP_SELF'])) {
    echo "You can not access this file directly!";
    die();
}

function QUERYOK ($res)
{
    if ($res)
    {
        echo " ok.<br>";
    }
    else
    {
        die (" Failed.");
    }
}

function get_player_name ($userid)
{
    global $db, $db_logging;

    $query = $db->Execute ("select character_name from {$db->prefix}ships where ship_id='$userid'");
    db_op_result ($db, $query, __LINE__, __FILE__, $db_logging);
    $name = $query->fields;

    return $name['character_name'];
}
?>