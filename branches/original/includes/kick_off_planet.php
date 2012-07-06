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
// File: includes/kick_off_planet.php

if (preg_match("/kick_off_planet.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function kick_off_planet ($db, $ship_id, $whichteam)
{
    global $db_logging;
    $result1 = $db->Execute("SELECT * from {$db->prefix}planets where owner = '$ship_id' ");
    db_op_result ($db, $result1, __LINE__, __FILE__, $db_logging);

    if ($result1 > 0)
    {
        while (!$result1->EOF)
        {
            $row = $result1->fields;
            $result2 = $db->Execute("SELECT * from {$db->prefix}ships where on_planet = 'Y' and planet_id = '$row[planet_id]' and ship_id <> '$ship_id' ");
            db_op_result ($db, $result2, __LINE__, __FILE__, $db_logging);
            if ($result2 > 0)
            {
                while (!$result2->EOF )
                {
                    $cur = $result2->fields;
                    $resa = $db->Execute("UPDATE {$db->prefix}ships SET on_planet = 'N',planet_id = '0' WHERE ship_id='$cur[ship_id]'");
                    db_op_result ($db, $resa, __LINE__, __FILE__, $db_logging);
                    playerlog ($db, $cur['ship_id'], LOG_PLANET_EJECT, "$cur[sector]|$row[character_name]");
                    $result2->MoveNext();
                }
            }

            $result1->MoveNext();
        }
   }
}
?>
