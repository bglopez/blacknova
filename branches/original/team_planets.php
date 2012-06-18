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
// File: team_planets.php

include "config.php";
updatecookie();
include "languages/$lang";
$title = $l_teamplanet_title;
include "header.php";

if (checklogin())
{
    die();
}


$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

if ($playerinfo[team]==0)
{
    echo "<br>$l_teamplanet_notally";
    echo "<br><br>";
    TEXT_GOTOMAIN();

    include "footer.php";
    return;
}

$query = "SELECT * FROM $dbtables[planets] WHERE corp=$playerinfo[team]";
if (!empty($sort))
{
    $query .= " ORDER BY";
    if ($sort == "name")
    {
        $query .= " $sort ASC";
    }
    elseif ($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" ||
    $sort == "colonists" || $sort == "credits" || $sort == "fighters")
    {
        $query .= " $sort DESC";
    }
    elseif ($sort == "torp")
    {
        $query .= " torps DESC";
    }
    else
    {
        $query .= " sector_id ASC";
    }
}

$res = $db->Execute($query);
bigtitle();

echo "<br>";
echo "<b><A HREF=planet_report.php>$l_teamplanet_personal</a></b>";
echo "<br>";
echo "<br>";

$i = 0;
if ($res)
{
    while (!$res->EOF)
    {
        $planet[$i] = $res->fields;
        $i++;
        $res->Movenext();
    }
}

$num_planets = $i;
if ($num_planets < 1)
{
    echo "<br>$l_teamplanet_noplanet";
}
else
{
    echo "$l_pr_clicktosort<br><br>";
    echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=2>";
    echo "<TR BGCOLOR=\"$color_header\">";
    echo "<td><b><A HREF=team_planets.php?sort=sector>$l_sector</a></b></td>";
    echo "<td><b><A HREF=team_planets.php?sort=name>$l_name</a></b></td>";
    echo "<td><b><A HREF=team_planets.php?sort=ore>$l_ore</a></b></td>";
    echo "<td><b><A HREF=team_planets.php?sort=organics>$l_organics</a></b></td>";
    echo "<td><b><A HREF=team_planets.php?sort=goods>$l_goods</a></b></td>";
    echo "<td><b><A HREF=team_planets.php?sort=energy>$l_energy</a></b></td>";
    echo "<td><b><A HREF=team_planets.php?sort=colonists>$l_colonists</a></b></td>";
    echo "<td><b><A HREF=team_planets.php?sort=credits>$l_credits</a></b></td>";
    echo "<td><b><A HREF=team_planets.php?sort=fighters>$l_fighters</a></b></td>";
    echo "<td><b><A HREF=team_planets.php?sort=torp>$l_torps</a></b></td>";
    echo "<td><b>$l_base?</b></td><td><b>$l_selling?</b></td>";
    echo "<td><b>Player</b></td>";
    echo "</TR>";
    $total_organics = 0;
    $total_ore = 0;
    $total_goods = 0;
    $total_energy = 0;
    $total_colonists = 0;
    $total_credits = 0;
    $total_fighters = 0;
    $total_torp = 0;
    $total_base = 0;
    $total_selling = 0;
    $color = $color_line1;
    for ($i = 0; $i < $num_planets; $i++)
    {
        $total_organics += $planet[$i][organics];
        $total_ore += $planet[$i][ore];
        $total_goods += $planet[$i][goods];
        $total_energy += $planet[$i][energy];
        $total_colonists += $planet[$i][colonists];
        $total_credits += $planet[$i][credits];
        $total_fighters += $planet[$i][fighters];
        $total_torp += $planet[$i][torps];
        if ($planet[$i][base] == "Y")
        {
            $total_base += 1;
        }

        if ($planet[$i][sells] == "Y")
        {
            $total_selling += 1;
        }
        if (empty($planet[$i][name]))
        {
            $planet[$i][name] = "$l_unnamed";
        }

        $owner = $planet[$i][owner];
        $res = $db->Execute("SELECT character_name FROM $dbtables[ships] WHERE ship_id=$owner");
        $player = $res->fields[character_name];

        echo "<TR BGCOLOR=\"$color\">";
        echo "<td><A HREF=rsmove.php?engage=1&destination=". $planet[$i][sector_id] . ">". $planet[$i][sector_id] ."</a></td>";
        echo "<td>" . $planet[$i][name]              . "</td>";
        echo "<td>" . NUMBER($planet[$i][ore])       . "</td>";
        echo "<td>" . NUMBER($planet[$i][organics])  . "</td>";
        echo "<td>" . NUMBER($planet[$i][goods])     . "</td>";
        echo "<td>" . NUMBER($planet[$i][energy])    . "</td>";
        echo "<td>" . NUMBER($planet[$i][colonists]) . "</td>";
        echo "<td>" . NUMBER($planet[$i][credits])   . "</td>";
        echo "<td>" . NUMBER($planet[$i][fighters])  . "</td>";
        echo "<td>" . NUMBER($planet[$i][torps])     . "</td>";
        echo "<td>" . ($planet[$i][base] == 'Y' ? "$l_yes" : "$l_no") . "</td>";
        echo "<td>" . ($planet[$i][sells] == 'Y' ? "$l_yes" : "$l_no") . "</td>";
        echo "<td>" . $player                        . "</td>";
        echo "</TR>";

        if ($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }
    }

    echo "<TR BGCOLOR=\"$color\">";
    echo "<td></td>";
    echo "<td>$l_pr_totals</td>";
    echo "<td>" . NUMBER($total_ore) . "</td>";
    echo "<td>" . NUMBER($total_organics) . "</td>";
    echo "<td>" . NUMBER($total_goods) . "</td>";
    echo "<td>" . NUMBER($total_energy) . "</td>";
    echo "<td>" . NUMBER($total_colonists) . "</td>";
    echo "<td>" . NUMBER($total_credits) . "</td>";
    echo "<td>" . NUMBER($total_fighters) . "</td>";
    echo "<td>" . NUMBER($total_torp) . "</td>";
    echo "<td>" . NUMBER($total_base) . "</td>";
    echo "<td>" . NUMBER($total_selling) . "</td>";
    echo "<td></td>";
    echo "</TR>";
    echo "</TABLE>";
}

echo "<br><br>";
TEXT_GOTOMAIN();
include "footer.php";
?>
