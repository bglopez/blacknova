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
// File: inclues/ini_to_db.php
//
// Function for importing values from an INI file into the database.

if (strpos ($_SERVER['PHP_SELF'], 'ini_to_db.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function ini_to_db ($db, $ini_file, $ini_table, $section)
{
    // This is a loop, that reads a ini file, of the type variable = value.
    // It will loop thru the list of the ini variables, and push them into the db.
    $ini_keys = parse_ini_file ($ini_file, true);

    $status = true; // This variable allows us to track the inserts into the databse. If one fails, the whole process is considered failed.
    $i = 0;
    $resa = $db->StartTrans (); // We enclose the inserts in a transaction as it is roughly 30 times faster
    \bnt\dbop::dbresult ($db, $resa, __LINE__, __FILE__);

    foreach ($ini_keys as $config_category => $config_line)
    {
        foreach ($config_line as $config_key => $config_value)
        {
            // We have to ensure that the language string (config_value) is utf8 encoded before sending to the database
            $config_value = utf8_encode ($config_value);
            $debug_query = $db->Execute ("INSERT into {$db->prefix}$ini_table (name, category, value, section) VALUES (?,?,?,?)", array ($config_key, $config_category, $config_value, $section));
            \bnt\dbop::dbresult ($db, $debug_query, __LINE__, __FILE__);
            if (!$debug_query)
            {
                $status[$i] = $debug_query;
                $i++;
            }
        }
    }

    $trans_status = $db->CompleteTrans(); // Complete the transaction
    \bnt\dbop::dbresult ($db, $trans_status, __LINE__, __FILE__);

    if ($trans_status !== true)
    {
        $status[$i] = $debug_query;
        $i++;
    }

    return $status;
}
?>
