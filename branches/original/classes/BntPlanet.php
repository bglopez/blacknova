<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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
// File: classes/BntPlanet.php

class BntPlanet
{
    public function getOwner($db = null, $planet_id = null, &$owner_info = null)
    {
        $owner_info = null;
        if (!is_null ($planet_id) && is_numeric ($planet_id) && $planet_id > 0)
        {
            $sql  = "SELECT ship_id, character_name, team FROM {$db->prefix}planets ";
            $sql .= "LEFT JOIN {$db->prefix}ships ON {$db->prefix}ships.ship_id = {$db->prefix}planets.owner ";
            $sql .= "WHERE {$db->prefix}planets.planet_id=?;";
            $res = $db->Execute ($sql, array ($planet_id));
            BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);
            if ($res->RecordCount() > 0 )
            {
                $owner_info = (array) $res->fields;

                return true;
            }
        }

        return false;
    }
}
?>
