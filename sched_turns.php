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
// File: sched_turns.php

if (preg_match("/sched_turns.php/i", $_SERVER['PHP_SELF']))
{
    echo "You can not access this file directly!";
    die();
}

echo "<b>TURNS</b><br><br>";
echo "Adding turns...";
global $db_logging;
QUERYOK($db->Execute("UPDATE $dbtables[ships] SET turns = turns + ($turns_per_tick * $multiplier) WHERE turns < $max_turns"));

echo "Ensuring maximum turns are $max_turns...";
QUERYOK($db->Execute("UPDATE $dbtables[ships] SET turns = $max_turns WHERE turns > $max_turns;"));
echo "<br>";
$multiplier = 0;
?>
