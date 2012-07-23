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
// File: includes/explode_mines.php

if (preg_match("/explode_mines.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function explode_mines ($db, $sector, $num_mines)
{
    global $db_logging;
    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=? and defence_type ='M' order by quantity ASC;", array($sector));
    db_op_result ($db, $result3, __LINE__, __FILE__, $db_logging);
    echo $db->ErrorMsg();
    // Put the defence information into the array "defenceinfo"
    if ($result3 instanceof ADORecordSet)
    {
        while (!$result3->EOF && $num_mines > 0)
        {
            $row = $result3->fields;
            if ($row['quantity'] > $num_mines)
            {
                $update = $db->Execute("UPDATE {$db->prefix}sector_defence set quantity=quantity - ? where defence_id = ?;", array($num_mines, $row['defence_id']));
                db_op_result ($db, $update, __LINE__, __FILE__, $db_logging);
                $num_mines = 0;
            }
            else
            {
                $update = $db->Execute("DELETE FROM {$db->prefix}sector_defence WHERE defence_id = ?;", array($row['defence_id']));
                db_op_result ($db, $update, __LINE__, __FILE__, $db_logging);
                $num_mines -= $row['quantity'];
             }
             $result3->MoveNext();
         }
     }
}
?>