<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: includes/ai_trade.php
//
// Description: The function handling AI trading routines.

include_once ("./global_includes.php");
dynamic_loader ($db, "direct_test.php");
direct_test(__FILE__, $_SERVER['PHP_SELF']);

function ai_trade()
{
    //  Setup general variables
    global $playerinfo, $inventory_factor, $ore_price, $ore_delta, $ore_limit, $goods_price;
    global $goods_delta, $goods_limit, $organics_price, $organics_delta, $organics_limit, $ai_isdead;
    global $db;
    
    dynamic_loader ($db, "playerlog.php");

    $ore_price = 11;
    $organics_price = 5;
    $goods_price = 15;

    //  Obtain sector information
    $sectres = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?", array($playerinfo['sector']));
    $sectorinfo = $sectres->fields;

    //  Obtain port information
    $portres = $db->Execute ("SELECT * FROM {$db->prefix}ports WHERE sector_id=?", array($playerinfo['sector']));
    $portinfo = $sectres->fields;

    // Obtain zone information
    $zoneres = $db->Execute ("SELECT zone_id,allow_attack,allow_trade FROM {$db->prefix}zones WHERE " .
                             "zone_id=?", array($sectorinfo['zone_id']));
    $zonerow = $zoneres->fields;

    //  Make sure we can trade here
    if ($zonerow['allow_trade']== "N")
    {
        return;
    }

    //  Ccheck for a port we can use
    if ($portinfo['port_type'] == "none")
    {
        return;
    }

    //  AI do not trade at energy ports since they regen energy
    if ($portinfo['port_type'] == "energy")
    {
        return;
    }

    //  Check for net credit/cargo
    // if ($playerinfo['ship_ore']<0) $playerinfo['ship_ore']= $shipore = 0;
    if ($playerinfo['ship_ore']<0) 
    {
        $playerinfo['ship_ore']= $shipore = 0;
    }

    // if ($playerinfo['ship_organics']<0) $playerinfo['ship_organics']= $shiporganics = 0;
    if ($playerinfo['ship_organics']<0) 
    {
        $playerinfo['ship_organics']= $shiporganics = 0;
    }

    // if ($playerinfo['ship_goods']<0) $playerinfo['ship_goods']= $shipgoods = 0;
    if ($playerinfo['ship_goods']<0) 
    {
        $playerinfo['ship_goods']= $shipgoods = 0;
    }

    // if ($playerinfo['credits']<0) $playerinfo['credits']= $shipcredits=10000;
    if ($playerinfo['credits']<= 0) 
    {
        $playerinfo['credits']= $shipcredits=10000;
    }

    if ($portinfo['port_ore'] <= 0)
    {
        return;
    }

    if ($portinfo['port_organics'] <= 0)
    {
        return;
    }

    if ($portinfo['port_goods'] <= 0)
    {
        return;
    }

    //  Check AI credit/cargo
    if ($playerinfo['ship_ore']>0) $shipore= $playerinfo['ship_ore'];
    if ($playerinfo['ship_organics']>0) $shiporganics= $playerinfo['ship_organics'];
    if ($playerinfo['ship_goods']>0) $shipgoods= $playerinfo['ship_goods'];
    if ($playerinfo['credits']>0) $shipcredits= $playerinfo['credits'];

    //  Make sure we have cargo or credits
    if (!$playerinfo['credits']>0 && !$playerinfo['ship_ore']>0 && !$playerinfo['ship_goods']>0 && !$playerinfo['ship_organics']>0) return;

    //  Make sure cargos are compatible
    if ($portinfo['port_type']== "ore" && $shipore>0) return;
    if ($portinfo['port_type']== "organics" && $shiporganics>0) return;
    if ($portinfo['port_type']== "goods" && $shipgoods>0) return;

    //  Lets trade some cargo
    if ($portinfo['port_type']== "ore")//  PORT ORE 
    {
        // Set the prices
        $ore_price = $ore_price - $ore_delta * $portinfo['port_ore'] / $ore_limit * $inventory_factor;
        $organics_price = $organics_price + $organics_delta * $portinfo['port_organics'] / $organics_limit * $inventory_factor;
        $goods_price = $goods_price + $goods_delta * $portinfo['port_goods'] / $goods_limit * $inventory_factor;

        //  Set cargo buy/sell
        $amount_organics = $playerinfo['ship_organics'];
        $amount_goods = $playerinfo['ship_goods'];

        //  Since we sell all other holds we set amount to be our total hold limit
        $amount_ore = num_holds($playerinfo['hull']);
    
        //  We adjust this to make sure it does not exceed what the port has to sell
        $amount_ore = min($amount_ore, $portinfo['port_ore']);

        //  We adjust this to make sure it does not exceed what we can afford to buy
        $amount_ore = min($amount_ore, floor(($playerinfo['credits'] + $amount_organics * $organics_price + $amount_goods * $goods_price) / $ore_price));

        // Buy / Sell cargo
        $total_cost = round(($amount_ore * $ore_price) - ($amount_organics * $organics_price + $amount_goods * $goods_price));
        $newcredits = max(0,$playerinfo['credits']-$total_cost);
        $newore = $playerinfo['ship_ore']+$amount_ore;
        $neworganics = max(0,$playerinfo['ship_organics']-$amount_organics);
        $newgoods = max(0,$playerinfo['ship_goods']-$amount_goods);
        $trade_result = $db->Execute("UPDATE {$db->prefix}ships SET rating=rating+1, credits=?, " .
                                     "ship_ore=?, ship_organics=?, ship_goods=? WHERE " .
                                     "ship_id=?", array($newcredits, $newore, $neworganics, $newgoods, $playerinfo['ship_id']));
        $trade_result2 = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-?, " .
                                      "port_organics=port_organics+?, port_goods=port_goods+? " .
                                      "WHERE sector_id=?", array($amount_ore, $amount_organics, $amount_goods, $sectorinfo['sector_id']));
    }

    if ($portinfo['port_type']== "organics") //  Port organics
    {
        // Set the prices
        $organics_price = $organics_price - $organics_delta * $portinfo['port_organics'] / $organics_limit * $inventory_factor;
        $ore_price = $ore_price + $ore_delta * $portinfo['port_ore'] / $ore_limit * $inventory_factor;
        $goods_price = $goods_price + $goods_delta * $portinfo['port_goods'] / $goods_limit * $inventory_factor;

        //  Set cargo buy/sell
        $amount_ore = $playerinfo['ship_ore'];
        $amount_goods = $playerinfo['ship_goods'];

        //  Since we sell all other holds we set the amount to be our total hold limit
        $amount_organics = num_holds($playerinfo['hull']);

        //  Make sure we do not exceed what the port has to sell
        $amount_organics = min($amount_organics, $portinfo['port_organics']);

        // Make sure its not more than we can afford 
        $amount_organics = min($amount_organics, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_goods * $goods_price) / $organics_price));

        // Buy/Sell cargo
        $total_cost = round(($amount_organics * $organics_price) - ($amount_ore * $ore_price + $amount_goods * $goods_price));
        $newcredits = max(0,$playerinfo['credits']-$total_cost);
        $newore = max(0,$playerinfo['ship_ore']-$amount_ore);
        $neworganics = $playerinfo['ship_organics']+$amount_organics;
        $newgoods = max(0,$playerinfo['ship_goods']-$amount_goods);
        $trade_result = $db->Execute("UPDATE {$db->prefix}ships SET rating=rating+1, credits=?, " .
                                     "ship_ore=?, ship_organics=?, ship_goods=? WHERE " .
                                     "ship_id=?", array($newcredits, $newore, $neworganics, $newgoods, $playerinfo['ship_id']));
        $trade_result2 = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore+?, " .
                                      "port_organics=port_organics-?, " .
                                      "port_goods=port_goods+? WHERE sector_id=?", array($amount_ore, $amount_organics, $amount_goods, $sectorinfo['sector_id']));
        // playerlog($db,$playerinfo['ship_id'], "LOG_RAW", "kabal Trade Results: Sold $amount_goods Goods Sold $amount_ore Ore Bought $amount_organics Organics Cost $total_cost"); 
    }

    if ($portinfo['port_type']== "goods") // Post goods
    {
        // Set the prices
        $goods_price = $goods_price - $goods_delta * $portinfo['port_goods'] / $goods_limit * $inventory_factor;
        $ore_price = $ore_price + $ore_delta * $portinfo['port_ore'] / $ore_limit * $inventory_factor;
        $organics_price = $organics_price + $organics_delta * $portinfo['port_organics'] / $organics_limit * $inventory_factor;

        //  Set cargo buy/sell
        $amount_ore = $playerinfo['ship_ore'];
        $amount_organics = $playerinfo['ship_organics'];

        //  Since we sell all other holds we set amount to be our total hold limit.
        $amount_goods = num_holds($playerinfo['hull']);

        // Make sure it does not exceed what the port has to sell
        $amount_goods = min($amount_goods, $portinfo['port_goods']);

        // Make sure its not more than we can afford 
        $amount_goods = min($amount_goods, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_organics * $organics_price) / $goods_price));

        // Buy / Sell Cargo
        $total_cost = round(($amount_goods * $goods_price) - ($amount_organics * $organics_price + $amount_ore * $ore_price));
        $newcredits = max(0,$playerinfo['credits']-$total_cost);
        $newore = max(0,$playerinfo['ship_ore']-$amount_ore);
        $neworganics = max(0,$playerinfo['ship_organics']-$amount_organics);
        $newgoods = $playerinfo['ship_goods']+$amount_goods;
        $trade_result = $db->Execute("UPDATE {$db->prefix}ships SET rating=rating+1, credits=?, " .
                                     "ship_ore=?, ship_organics=?, ship_goods=? WHERE " .
                                     "ship_id=?", array($newcredits, $newore, $neworganics, $newgoods, $playerinfo['ship_id']));
        $trade_result2 = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore+?, " .
                                      "port_organics=port_organics+?, " .
                                      "port_goods=port_goods-? WHERE sector_id=?", array($amount_ore, $amount_organics, $amount_goods, $sectorinfo['sector_id']));
        // playerlog($db,$playerinfo['ship_id'], "LOG_RAW", "kabal Trade Results: Sold $amount_ore Ore Sold $amount_organics Organics Bought $amount_goods Goods Cost $total_cost"); 
    }
}
?>