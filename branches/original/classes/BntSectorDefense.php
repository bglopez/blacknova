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
// File: classes/BntSectorDefense.php

if (strpos ($_SERVER['PHP_SELF'], 'BntSectorDefense.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntSectorDefense
{
    public static function message_defense_owner($db, $sector, $message)
    {
        $res = $db->Execute ("SELECT ship_id FROM {$db->prefix}sector_defence WHERE sector_id = ?;", array ($sector));
        BntDb::logDbErrors ($db, $res, __LINE__, __FILE__);

        if ($res instanceof ADORecordSet)
        {
            while (!$res->EOF)
            {
                player_log ($db, $res->fields['ship_id'], LOG_RAW, $message);
                $res->MoveNext();
            }
        }
    }
}
?>