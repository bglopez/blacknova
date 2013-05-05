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
// File: classes/LogMove.php

if (strpos ($_SERVER['PHP_SELF'], 'LogMove.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class LogMove
{
    static function writeLog ($db, $ship_id, $sector_id)
    {
        $res = $db->Execute("INSERT INTO {$db->prefix}movement_log (ship_id, sector_id, time) VALUES (?, ?, NOW())", array ($ship_id, $sector_id));
        DbOp::dbResult ($db, $res, __LINE__, __FILE__);
    }
}
?>