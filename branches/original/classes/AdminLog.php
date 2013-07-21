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
// File: classes/AdminLog.php
// Todo: Recode adminlog to be smart about whether there is a db, and if not, log to a file that will be slurped into the db when there is.

if (strpos ($_SERVER['PHP_SELF'], 'AdminLog.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class AdminLog
{
    static function writeLog ($db, $log_type, $data = "")
    {
        // Write log_entry to the admin log
        $ret = false;
        $data = addslashes ($data);
        if (is_int ($log_type))
        {
            $ret = $db->Execute ("INSERT INTO {$db->prefix}logs VALUES (NULL, 0, ?, NOW(), ?)", array ($log_type, $data));
            DbOp::dbResult ($db, $ret, __LINE__, __FILE__);
        }

        return $ret;
    }
}
?>