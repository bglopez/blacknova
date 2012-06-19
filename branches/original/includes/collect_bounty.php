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
// File: includes/collect_bounty.php

if (preg_match("/collect_bounty.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function collect_bounty ($attacker, $bounty_on)
{
    global $db, $dbtables, $l_by_thefeds;
    $res = $db->Execute("SELECT * FROM $dbtables[bounty],$dbtables[ships] WHERE bounty_on = $bounty_on AND bounty_on = ship_id and placed_by <> 0");
    if ($res)
    {
        while (!$res->EOF)
        {
            $bountydetails = $res->fields;
            if ($res->fields['placed_by'] == 0)
            {
                $placed = $l_by_thefeds;
            }
            else
            {
                $res2 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id = $bountydetails[placed_by]");
                $placed = $res2->fields['character_name'];
            }

            $update = $db->Execute("UPDATE $dbtables[ships] SET credits = credits + $bountydetails[amount] WHERE ship_id = $attacker");
            $delete = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");

            playerlog ($attacker, LOG_BOUNTY_CLAIMED, "$bountydetails[amount]|$bountydetails[character_name]|$placed");
            playerlog ($bountydetails['placed_by'], LOG_BOUNTY_PAID, "$bountydetails[amount]|$bountydetails[character_name]");
            $res->MoveNext();
        }
   }
   $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_on = $bounty_on");
}
?>
