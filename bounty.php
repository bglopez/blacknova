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
// File: bounty.php

include "config.php";
updatecookie ();
include "languages/$lang";
$title = $l_by_title;
include "header.php";

if (checklogin())
{
    die();
}

if (!isset($_GET['response']))
{
    $_GET['response'] = '';
}
$response = $_GET['response'];

$res = $db->Execute("select * FROM $dbtables[ships] WHERE email='$username'");
db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
$playerinfo = $res->fields;

switch ($response) {
    case "display":
        bigtitle ();
        $res5 = $db->Execute("select * FROM $dbtables[ships],$dbtables[bounty] WHERE bounty_on = ship_id AND bounty_on = $bounty_on");
        db_op_result ($db, $res5, __LINE__, __FILE__, $db_logging);
        $j = 0;
        if ($res5)
        {
            while (!$res5->EOF)
            {
                $bounty_details[$j] = $res5->fields;
                $j++;
                $res5->MoveNext();
            }
        }

        $num_details = $j;
        if ($num_details < 1)
        {
            echo $l_by_nobounties . "<br>";
        }
        else
        {
            echo $l_by_bountyon. " " . $bounty_details[0]['character_name'];
            echo '<table border=1 cellspacing=1 cellpadding=2 width="50%" align=center>';
            echo "<tr bgcolor=\"$color_header\">";
            echo "<td><b>" . $l_amount . "</td>";
            echo "<td><b>" . $l_by_placedby . "</td>";
            echo "<td><b>" . $l_by_action . "</td>";
            echo "</tr>";
            $color = $color_line1;
            for ($j = 0; $j < $num_details; $j++)
            {
                $someres = $db->execute("select character_name FROM $dbtables[ships] WHERE ship_id = " . $bounty_details[$j][placed_by]);
                db_op_result ($db, $someres, __LINE__, __FILE__, $db_logging);
                $details = $someres->fields;
                echo "<tr bgcolor=\"$color\">";
                echo "<td>" . $bounty_details[$j]['amount'] . "</td>";
                if ($bounty_details[$j]['placed_by'] == 0)
                {
                    echo "<td>$l_by_thefeds</td>";
                }
                else
                {
                    echo "<td>" . $details['character_name'] . "</td>";
                }
                if ($bounty_details[$j][placed_by] == $playerinfo[ship_id])
                {
                    echo "<td><a href=bounty.php?bid=" . $bounty_details[$j][bounty_id] . "&response=cancel>$l_by_cancel</A></td>";
                }
                else
                {
                    echo "<td>n/a</td>";
                }

                echo "</tr>";

                if ($color == $color_line1)
                {
                    $color = $color_line2;
                }
                else
                {
                    $color = $color_line1;
                }
            }
            echo "</table>";
        }
        break;
    case "cancel":
        $bid = (int)$_GET['bid'];

        bigtitle();
        if ($playerinfo[turns]<1 )
        {
            echo "$l_by_noturn<br><br>";
            TEXT_GOTOMAIN();
            include "footer.php";
            die();
        }

        $res = $db->Execute("select * from $dbtables[bounty] WHERE bounty_id = $bid");
        db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
        if (!res || $res->RowCount() ==0)
        {
            echo "$l_by_nobounty<br><br>";
            TEXT_GOTOMAIN();
            include "footer.php";
            die();
        }

        $bty = $res->fields;
        if ($bty[placed_by] <> $playerinfo[ship_id])
        {
            echo "$l_by_notyours<br><br>";
            TEXT_GOTOMAIN();
            include "footer.php";
            die();
        }

        $del = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bid");
        db_op_result ($db, $del, __LINE__, __FILE__, $db_logging);
        $stamp = date("Y-m-d H-i-s");
        $refund = $bty['amount'];
        $resx = $db->Execute("UPDATE $dbtables[ships] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, credits=credits+$refund where ship_id=$playerinfo[ship_id]");
        db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);
        echo "$l_by_canceled<br>";
        TEXT_GOTOMAIN();
        die();
        break;
    case "place":
        bigtitle();
        $bounty_on = stripnum($bounty_on);
        $ex = $db->Execute("select * from $dbtables[ships] WHERE ship_id = $bounty_on");
        db_op_result ($db, $ex, __LINE__, __FILE__, $db_logging);
        if (!$ex)
        {
            echo "$l_by_notexists<br><br>";
            TEXT_GOTOMAIN();
            include "footer.php";
            die();
        }

        $bty = $ex->fields;
        if ($bty[ship_destroyed] == "Y")
        {
            echo "$l_by_destroyed<br><br>";
            TEXT_GOTOMAIN();
            include "footer.php";
            die();
        }

        if ($playerinfo[turns]<1 )
        {
            echo "$l_by_noturn<br><br>";
            TEXT_GOTOMAIN();
            include "footer.php";
            die();
        }

        $amount = stripnum($amount);
        if ($amount < 0)
        {
            echo "$l_by_zeroamount<br><br>";
            TEXT_GOTOMAIN();
            include "footer.php";
            die();
        }

        if ($bounty_on == $playerinfo['ship_id'])
        {
            echo "$l_by_yourself<br><br>";
            TEXT_GOTOMAIN();
            include "footer.php";
            die();
        }

        if ($amount > $playerinfo['credits'])
        {
            echo "$l_by_notenough<br><br>";
            TEXT_GOTOMAIN();
            include "footer.php";
            die();
        }

        if ($bounty_maxvalue != 0)
        {
            $percent = $bounty_maxvalue * 100;
            $score = gen_score($playerinfo[ship_id]);
            $maxtrans = $score * $score * $bounty_maxvalue;
            $previous_bounty = 0;
            $pb = $db->Execute("select SUM(amount) AS totalbounty FROM $dbtables[ships] WHERE bounty_on = $bounty_on AND placed_by = $playerinfo[ship_id]");
            db_op_result ($db, $pb, __LINE__, __FILE__, $db_logging);
            if ($pb)
            {
                $prev = $pb->fields;
                $previous_bounty = $prev[totalbounty];
            }

            if ($amount + previous_bounty > $maxtrans)
            {
                $l_by_toomuch=str_replace("[percent]",$percent,$l_by_toomuch);
                echo "$l_by_toomuch<br><br>";
                TEXT_GOTOMAIN();
                include "footer.php";
                die();
            }
        }

        $insert = $db->Execute("INSERT INTO $dbtables[bounty] (bounty_on,placed_by,amount) values ($bounty_on, $playerinfo[ship_id] ,$amount)");
        db_op_result ($db, $insert, __LINE__, __FILE__, $db_logging);
        $stamp = date("Y-m-d H-i-s");
        $resx = $db->Execute("UPDATE $dbtables[ships] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, credits=credits-$amount where ship_id=$playerinfo[ship_id]");
        db_op_result ($db, $resx, __LINE__, __FILE__, $db_logging);
        echo "$l_by_placed<br>";
        TEXT_GOTOMAIN();
        die();
        break;
    default:
        bigtitle();
        $res = $db->Execute("select * FROM $dbtables[ships] WHERE ship_destroyed = 'N' AND ship_id <> $playerinfo[ship_id] ORDER BY character_name ASC");
        db_op_result ($db, $res, __LINE__, __FILE__, $db_logging);
        echo "<form action=bounty.php method=post>";
        echo "<table>";
        echo "<tr><td>$l_by_bountyon</td><td><select NAME=bounty_on>";
        while (!$res->EOF)
        {
            if (isset($bounty_on) && $bounty_on == $res->fields[ship_id])
            {
                $selected = "selected";
            }
            else
            {
                $selected = "";
            }

            $charname = $res->fields[character_name];
            $ship_id = $res->fields[ship_id];
            echo "<OPTION VALUE=$ship_id $selected>$charname</OPTION>";
            $res->MoveNext();
        }

        echo "</select></td></tr>";
        echo "<tr><td>$l_by_amount:</td><td><input type=text name=amount size=20 maxlength=20></td></tr>";
        echo "<tr><td></td><td><input type=submit value=$l_by_place><input type=reset value=Clear></td>";
        echo "</table>";
        echo "<input type=hidden name=response value=place>";
        echo "</form>";

        $result3 = $db->Execute ("select bounty_on, SUM(amount) as total_bounty FROM $dbtables[bounty] GROUP BY bounty_on");
        db_op_result ($db, $result3, __LINE__, __FILE__, $db_logging);

        $i = 0;
        if ($result3)
        {
            while (!$result3->EOF)
            {
                $bounties[$i] = $result3->fields;
                $i++;
                $result3->MoveNext();
            }
        }

        $num_bounties = $i;
        if ($num_bounties < 1)
        {
            echo "$l_by_nobounties<br>";
        }
        else
        {
            echo $l_by_moredetails . "<br><br>";
            echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
            echo "<tr bgcolor=\"$color_header\">";
            echo "<td><b>$l_by_bountyon</b></td>";
            echo "<td><b>$l_amount</td>";
            echo "</tr>";
            $color = $color_line1;
            for ($i = 0; $i < $num_bounties; $i++)
            {
                $someres = $db->execute("select character_name FROM $dbtables[ships] WHERE ship_id = " . $bounties[$i][bounty_on]);
                db_op_result ($db, $someres, __LINE__, __FILE__, $db_logging);
                $details = $someres->fields;
                echo "<tr bgcolor=\"$color\">";
                echo "<td><a href=bounty.php?bounty_on=" . $bounties[$i][bounty_on] . "&response=display>". $details[character_name] ."</A></td>";
                echo "<td>" . $bounties[$i][total_bounty] . "</td>";
                echo "</tr>";

                if ($color == $color_line1)
                {
                    $color = $color_line2;
                }
                else
                {
                    $color = $color_line1;
                }
            }
            echo "</table>";
        }
        echo "<br><br>";
        break;
}

TEXT_GOTOMAIN();
include "footer.php";
?>
