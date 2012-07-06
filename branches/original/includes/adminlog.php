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
// File: includes/adminlog.php

if (preg_match("/adminlog.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function adminlog ($db, $dbtype, $log_type, $data = "")
{
    global $db_logging;
    // Write log_entry to the admin log
    $ret = (boolean) false;
    $data = addslashes ($data);
    if (!empty($log_type))
    {
        $ret = $db->Execute("INSERT INTO {$db->prefix}logs VALUES(NULL, 0, $log_type, NOW(), '$data')");
        db_op_result ($db, $ret, __LINE__, __FILE__, $db_logging);
    }

    if (!$ret)
    {
        return (boolean) false;
    }
    else
    {
        return (boolean) true;
    }
}
?>
