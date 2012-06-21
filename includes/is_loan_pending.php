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
// File: includes/isLoanPending.php

if (preg_match("/isLoanPending.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function isLoanPending ($ship_id)
{
    global $db, $dbtables;
    global $IGB_lrate;

    $res = $db->Execute("SELECT loan, UNIX_TIMESTAMP(loantime) AS time FROM $dbtables[ibank_accounts] WHERE ship_id=$ship_id");
    if ($res)
    {
        $account = $res->fields;

        if ($account['loan'] == 0)
        {
            return false;
        }

        $curtime = time();
        $difftime = ($curtime - $account['time']) / 60;
        if ($difftime > $IGB_lrate)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}
?>
