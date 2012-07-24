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
// File: includes/spy_ship_destroyed.php

function spy_ship_destroyed($db,$ship_id, $attacker_player_id = 0)
{
    dynamic_loader ($db, "playerlog.php");

    global $shipinfo;
  
    if ($attacker_player_id)
    {    
        $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET active ='N', job_id = '0', spy_percent = '0.0', ship_id=?, planet_id='0' WHERE ship_id=? AND owner_id=?", array($shipinfo['ship_id'], $ship_id, $attacker_player_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    $res = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}players.character_name, {$db->prefix}ships.name AS ship_name FROM {$db->prefix}ships INNER JOIN {$db->prefix}players ON {$db->prefix}ships.player_id = {$db->prefix}players.player_id INNER JOIN {$db->prefix}spies ON {$db->prefix}spies.ship_id = {$db->prefix}ships.ship_id  WHERE {$db->prefix}spies.ship_id=?", array($ship_id)); 
    while (!$res->EOF)
    {
        $owners = $res->fields;
        playerlog($db,$owners['owner_id'], "LOG_SHIPSPY_CATACLYSM", "$owners[spy_id]|$owners[character_name]|$owners[ship_name]");
        $res->MoveNext();
    }
  
    $debug_query = $db->Execute("DELETE FROM {$db->prefix}spies WHERE ship_id=?", array($ship_id)); 
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}
?>