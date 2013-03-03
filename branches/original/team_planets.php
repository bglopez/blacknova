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

include './global_includes.php';

if (check_login ($db, $lang, $langvars)) // Checks player login, sets playerinfo
{
    die ();
}

// New database driven language entries
load_languages ($db, $lang, array ('team_planets', 'planet_report', 'planet', 'main', 'port', 'common', 'global_includes', 'global_funcs', 'footer', 'news'), $langvars);

$title = $l_teamplanet_title;
include './header.php';

$res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
\bnt\dbop::dbresult ($db, $res, __LINE__, __FILE__);
$playerinfo = $res->fields;

if ($playerinfo['team'] == 0)
{
    echo "<br>$l_teamplanet_notally";
    echo "<br><br>";
    \bnt\bnttext::gotomain ($langvars);
    include './footer.php';

    return;
}

$query = "SELECT * FROM {$db->prefix}planets WHERE corp=$playerinfo[team]";
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
\bnt\dbop::dbresult ($db, $res, __LINE__, __FILE__);
echo "<h1>" . $title . "</h1>\n";

echo "<br>";
echo "<strong><a href=planet_report.php>$l_teamplanet_personal</a></strong>";
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
    echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
    echo "<tr bgcolor=\"$color_header\">";
    echo "<td><strong><a href=team_planets.php?sort=sector>$l_sector</a></strong></td>";
    echo "<td><strong><a href=team_planets.php?sort=name>$l_name</a></strong></td>";
    echo "<td><strong><a href=team_planets.php?sort=ore>$l_ore</a></strong></td>";
    echo "<td><strong><a href=team_planets.php?sort=organics>$l_organics</a></strong></td>";
    echo "<td><strong><a href=team_planets.php?sort=goods>$l_goods</a></strong></td>";
    echo "<td><strong><a href=team_planets.php?sort=energy>$l_energy</a></strong></td>";
    echo "<td><strong><a href=team_planets.php?sort=colonists>$l_colonists</a></strong></td>";
    echo "<td><strong><a href=team_planets.php?sort=credits>$l_credits</a></strong></td>";
    echo "<td><strong><a href=team_planets.php?sort=fighters>$l_fighters</a></strong></td>";
    echo "<td><strong><a href=team_planets.php?sort=torp>$l_torps</a></strong></td>";
    echo "<td><strong>$l_base?</strong></td><td><strong>$l_selling?</strong></td>";
    echo "<td><strong>Player</strong></td>";
    echo "</tr>";
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
        $total_organics += $planet[$i]['organics'];
        $total_ore += $planet[$i]['ore'];
        $total_goods += $planet[$i]['goods'];
        $total_energy += $planet[$i]['energy'];
        $total_colonists += $planet[$i]['colonists'];
        $total_credits += $planet[$i]['credits'];
        $total_fighters += $planet[$i]['fighters'];
        $total_torp += $planet[$i]['torps'];
        if ($planet[$i]['base'] == "Y")
        {
            $total_base += 1;
        }

        if ($planet[$i]['sells'] == "Y")
        {
            $total_selling += 1;
        }
        if (empty($planet[$i]['name']))
        {
            $planet[$i]['name'] = "$l_unnamed";
        }

        $owner = $planet[$i]['owner'];
        $res = $db->Execute("SELECT character_name FROM {$db->prefix}ships WHERE ship_id=$owner");
        \bnt\dbop::dbresult ($db, $res, __LINE__, __FILE__);
        $player = $res->fields['character_name'];

        echo "<tr bgcolor=\"$color\">";
        echo "<td><a href=rsmove.php?engage=1&destination=". $planet[$i]['sector_id'] . ">". $planet[$i]['sector_id'] ."</a></td>";
        echo "<td>" . $planet[$i]['name']              . "</td>";
        echo "<td>" . number_format ($planet[$i]['ore'], 0, $local_number_dec_point, $local_number_thousands_sep)       . "</td>";
        echo "<td>" . number_format ($planet[$i]['organics'], 0, $local_number_dec_point, $local_number_thousands_sep)  . "</td>";
        echo "<td>" . number_format ($planet[$i]['goods'], 0, $local_number_dec_point, $local_number_thousands_sep)     . "</td>";
        echo "<td>" . number_format ($planet[$i]['energy'], 0, $local_number_dec_point, $local_number_thousands_sep)    . "</td>";
        echo "<td>" . number_format ($planet[$i]['colonists'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
        echo "<td>" . number_format ($planet[$i]['credits'], 0, $local_number_dec_point, $local_number_thousands_sep)   . "</td>";
        echo "<td>" . number_format ($planet[$i]['fighters'], 0, $local_number_dec_point, $local_number_thousands_sep)  . "</td>";
        echo "<td>" . number_format ($planet[$i]['torps'], 0, $local_number_dec_point, $local_number_thousands_sep)     . "</td>";
        echo "<td>" . ($planet[$i]['base'] == 'Y' ? "$l_yes" : "$l_no") . "</td>";
        echo "<td>" . ($planet[$i]['sells'] == 'Y' ? "$l_yes" : "$l_no") . "</td>";
        echo "<td>" . $player                        . "</td>";
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

    echo "<tr bgcolor=\"$color\">";
    echo "<td></td>";
    echo "<td>$l_pr_totals</td>";
    echo "<td>" . number_format ($total_ore, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format ($total_organics, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format ($total_goods, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format ($total_energy, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format ($total_colonists, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format ($total_credits, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format ($total_fighters, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format ($total_torp, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format ($total_base, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td>" . number_format ($total_selling, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>";
    echo "<td></td>";
    echo "</tr>";
    echo "</table>";
}

echo "<br><br>";
\bnt\bnttext::gotomain ($langvars);
include './footer.php';
?>
