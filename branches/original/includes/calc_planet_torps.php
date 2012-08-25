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
// File: includes/calc_planet_torps.php

function calc_planet_torps ()
{
    global $db, $ownerinfo, $base_defense, $planetinfo;

    $base_factor = ($planetinfo['base'] == 'Y') ? $base_defense : 0;

    $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
    db_op_result ($db, $res, __LINE__, __FILE__);
    $torp_launchers = round (pow ($level_factor, ($ownerinfo['torp_launchers']) + $base_factor)) * 10;
    $torps = $planetinfo['torps'];
    if ($res)
    {
       while (!$res->EOF)
       {
           $ship_torps =  round (pow ($level_factor, $res->fields['torp_launchers'])) * 10;
           $torp_launchers = $torp_launchers + $ship_torps;
           $res->MoveNext();
       }
    }
    if ($torp_launchers > $torps)
    {
        $planettorps = $torps;
    }
    else
    {
        $planettorps = $torp_launchers;
    }

    $planetinfo['torps'] -= $planettorps;

    return $planettorps;
}
?>