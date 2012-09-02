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
// File: sched_ports.php

if (strpos ($_SERVER['PHP_SELF'], 'sched_ports.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include 'error.php';
}

// Update Ore in Ports
echo "<strong>PORTS</strong><br><br>";
echo "Adding ore to all commodities ports...";
$resa = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore = port_ore + (? * ? * ? ) WHERE port_type='ore' AND port_ore < ?", array ($ore_rate, $multiplier, $port_regenrate, $ore_limit));
is_query_ok ($resa);

echo "Adding ore to all ore ports...";
$resb = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore = port_ore + (? * ? * ?) WHERE port_type!='special' AND port_type!='none' AND port_ore < ?",  array ($ore_rate, $multiplier, $port_regenrate, $ore_limit));
is_query_ok ($resb);

echo "Ensuring minimum ore levels are 0...";
$resc = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore = 0 WHERE port_ore < 0");
is_query_ok ($resc);
echo "<br>";

// Update Organics in Ports
echo "Adding organics to all commodities ports...";
$resd = $db->Execute ("UPDATE {$db->prefix}universe SET port_organics = port_organics + (? * ? * ?) WHERE port_type='organics' AND port_organics < ?", array ($organics_rate, $multiplier, $port_regenrate, $organics_limit));
is_query_ok ($resd);

echo "Adding organics to all organics ports...";
$rese = $db->Execute ("UPDATE {$db->prefix}universe SET port_organics = port_organics + (? * ? * ?) WHERE port_type!='special' AND port_type!='none' AND port_organics < ?", array ($organics_rate, $multiplier, $port_regenrate, $organics_limit));
is_query_ok ($rese);

echo "Ensuring minimum organics levels are 0...";
$resf = $db->Execute ("UPDATE {$db->prefix}universe SET port_organics = 0 WHERE port_organics < 0");
is_query_ok ($resf);
echo "<br>";

// Update Goods in Ports
echo "Adding goods to all commodities ports...";
$resg = $db->Execute ("UPDATE {$db->prefix}universe SET port_goods = port_goods + (? * ? * ?) WHERE port_type='goods' AND port_goods < ?", array ($goods_rate, $multiplier, $port_regenrate, $goods_limit));
is_query_ok ($resg);

echo "Adding goods to all goods ports...";
$resh = $db->Execute ("UPDATE {$db->prefix}universe SET port_goods = port_goods + (? * ? * ?) WHERE port_type!='special' AND port_type!='none' AND port_goods < ?", array ($goods_rate, $multiplier, $port_regenrate, $goods_limit));
is_query_ok ($resh);

echo "Ensuring minimum goods levels are 0...";
$resi = $db->Execute ("UPDATE {$db->prefix}universe SET port_goods = 0 WHERE port_goods < 0");
is_query_ok ($resi);
echo "<br>";

// Update Energy in Ports
echo "Adding energy to all commodities ports...";
$resj = $db->Execute ("UPDATE {$db->prefix}universe SET port_energy = port_energy + (? * ? * ?) WHERE port_type='energy' AND port_energy < ?", array ($energy_rate, $multiplier, $port_regenrate, $energy_limit));
is_query_ok ($resj);

echo "Adding energy to all energy ports...";
$resk = $db->Execute ("UPDATE {$db->prefix}universe SET port_energy = port_energy + (? * ? * ?) WHERE port_type!='special' AND port_type!='none' AND port_energy < ?", array ($energy_rate, $multiplier, $port_regenrate, $energy_limit));
is_query_ok ($resk);

echo "Ensuring minimum energy levels are 0...";
$resl = $db->Execute ("UPDATE {$db->prefix}universe SET port_energy = 0 WHERE port_energy < 0");
is_query_ok ($resl);
echo "<br>";

// Now check to see if any ports are over max, if so rectify.
echo "Checking Energy Port Cap...";
$resm = $db->Execute ("UPDATE {$db->prefix}universe SET port_energy = ? WHERE port_energy > ?", array ($energy_limit, $energy_limit));
is_query_ok ($resm);

echo "Checking Goods Port Cap...";
$resn = $db->Execute ("UPDATE {$db->prefix}universe SET port_goods = ? WHERE port_goods > ?", array ($goods_limit, $goods_limit));
is_query_ok ($resn);

echo "Checking Organics Port Cap...";
$reso = $db->Execute ("UPDATE {$db->prefix}universe SET port_organics = ? WHERE port_organics > ?", array ($organics_limit, $organics_limit));
is_query_ok ($reso);

echo "Checking Ore Port Cap...";
$resp = $db->Execute ("UPDATE {$db->prefix}universe SET port_ore = ? WHERE port_ore > ?", array ($ore_limit, $ore_limit));
is_query_ok ($resp);
$multiplier = 0;
?>
