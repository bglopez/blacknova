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
// File: navcomp.php

include("config.php");
updatecookie();
include("languages/$lang");
$title=$l_nav_title;
include("header.php");

if (checklogin())
{
    die();
}

bigtitle();

if (!$allow_navcomp)
{
    echo "$l_nav_nocomp<BR><BR>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
}

if (!isset($_GET['state']))
{
    $_GET['state'] = '';
}
$state = $_GET['state'];

unset($stop_sector);

$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo=$result->fields;
$current_sector = $playerinfo['sector'];
$computer_tech  = $playerinfo['computer'];

$result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$current_sector'");
$sectorinfo=$result2->fields;

// Gets the stop_sector POST Variable.
// Validats the post variable as a number.
// Typecast variable into an integer.

if (isset($_POST['stop_sector']))
{
    $stop_sector = $_POST['stop_sector'];
    if (!is_numeric($stop_sector))
    {
        adminlog(902, "{$playerinfo['ship_id']}|Tried to insert a hardcoded NavComp Info, to show planets|{$stop_sector}.");
        echo "<div style='color:#fff; font-size: 12px;'><span style='color:#fff;'>Detected Invalid NavComputer Information (<span style='color:#f00;'>Possible Hack!</span>)</span></div>\n<br>\n";

        TEXT_GOTOMAIN();
        include("footer.php");
        die();
    }
    $stop_sector = (int)$stop_sector;
}
else
{
    $stop_sector = '';
}

if ($state == 0)
{
    echo "<FORM ACTION=\"navcomp.php\" METHOD=POST>";
    echo "$l_nav_query <INPUT NAME=\"stop_sector\">&nbsp;<INPUT TYPE=SUBMIT VALUE=$l_submit><BR>\n";
    echo "<INPUT NAME=\"state\" VALUE=1 TYPE=HIDDEN>";
    echo "</FORM>\n";
}
elseif ($state == 1)
{
    if ($computer_tech < 5)
    {
        $max_search_depth = 2;
    }
    elseif ($computer_tech < 10)
    {
        $max_search_depth = 3;
    }
    elseif ($computer_tech < 15)
    {
        $max_search_depth = 4;
    }
    elseif ($computer_tech < 20)
    {
        $max_search_depth = 5;
    }
    else
    {
        $max_search_depth = 6;
    }

    for ($search_depth = 1; $search_depth <= $max_search_depth; $search_depth++)
    {
        $search_query = "SELECT    distinct\n    a1.link_start\n    ,a1.link_dest \n";
        for ($i = 2; $i<=$search_depth;$i++)
        {
            $search_query = $search_query . "    ,a". $i . ".link_dest \n";
        }

        $search_query = $search_query . "FROM\n     $dbtables[links] AS a1 \n";

        for ($i = 2; $i<=$search_depth;$i++)
        {
            $search_query = $search_query . "    ,$dbtables[links] AS a". $i . " \n";
        }

        $search_query = $search_query . "WHERE \n        a1.link_start = $current_sector \n";

        for ($i = 2; $i<=$search_depth; $i++)
        {
            $k = $i-1;
            $search_query = $search_query . "    AND a" . $k . ".link_dest = a" . $i . ".link_start \n";
        }

        $search_query = $search_query . "    AND a" . $search_depth . ".link_dest = $stop_sector \n";
        $search_query = $search_query . "    AND a1.link_dest != a1.link_start \n";

        for ($i=2; $i<=$search_depth;$i++)
        {
            $search_query = $search_query . "    AND a" . $i . ".link_dest not in (a1.link_dest, a1.link_start ";

            for ($j=2; $j<$i;$j++)
            {
                $search_query = $search_query . ",a".$j.".link_dest ";
            }
            $search_query = $search_query . ")\n";
        }

        $search_query = $search_query . "ORDER BY a1.link_start, a1.link_dest ";
        for ($i=2;$i<=$search_depth;$i++)
        {
            $search_query = $search_query . ", a" . $i . ".link_dest";
        }

        $search_query = $search_query . " \nLIMIT 1";
        //echo "$search_query\n\n";
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        $search_result = $db->Execute ($search_query) or die ("Invalid Query");
        $found = $search_result->RecordCount();
        if ($found > 0)
        {
            break;
        }
    }

    if ($found > 0)
    {
        echo "<H3>$l_nav_pathfnd</H3>\n";
        $links=$search_result->fields;
        echo $links[0];
        for ($i=1;$i<$search_depth+1;$i++)
        {
            echo " >> " . $links[$i];
        }
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        echo "<BR><BR>";
        echo "$l_nav_answ1 $search_depth $l_nav_answ2<BR><BR>";
    }
    else
    {
        echo "$l_nav_proper<BR><BR>";
    }
}

TEXT_GOTOMAIN();
include("footer.php");
?>
