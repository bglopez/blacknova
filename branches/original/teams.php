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
// File: teams.php

// Added a quick fix for creating a new team with the same name
// This file needs to be completely recoded from scratch :(

include "config.php";
updatecookie();

include "languages/$lang";
$title = $l_team_title;
include "header.php";
include_once "includes/defence_vs_defence.php";
include_once "includes/kick_off_planet.php";

if (checklogin())
{
    die();
}

bigtitle();
$testing = false; // set to false to get rid of password when creating new team

if (!isset($_REQUEST['whichteam']))
{
    $_REQUEST['whichteam'] = '';
}

if (!isset($_REQUEST['teamwhat']))
{
    $_REQUEST['teamwhat'] = '';
}

if (!isset($_REQUEST['confirmleave']))
{
    $_REQUEST['confirmleave'] = '';
}

if (!isset($_REQUEST['invited']))
{
    $_REQUEST['invited'] = '';
}

if (!isset($_POST['teamname']))
{
    $_POST['teamname'] = '';
}
$teamname = $_POST['teamname'];

$whichteam = stripnum($_REQUEST['whichteam']);
$teamwhat  = stripnum($_REQUEST['teamwhat']);
$confirmleave = stripnum($_REQUEST['confirmleave']);
$invited      = stripnum($_REQUEST['invited']);

// Setting up some recordsets.
// I noticed before the rewriting of this page that in some case recordset may be fetched more thant once, which is NOT optimized.

// Get user info.
$result = $db->Execute("SELECT $dbtables[ships].*, $dbtables[teams].team_name, $dbtables[teams].description, $dbtables[teams].creator, $dbtables[teams].id
            FROM $dbtables[ships]
            LEFT JOIN $dbtables[teams] ON $dbtables[ships].team = $dbtables[teams].id
            WHERE $dbtables[ships].email='$username'") or die($db->ErrorMsg());
$playerinfo    = $result->fields;

// Typecast into integers (this also removes all non numbers)
$teamwhat = NULL;
if (isset($_REQUEST['teamwhat']))
{
    $teamwhat  = (int)$_REQUEST['teamwhat'];
}

$whichteam = NULL;
if (isset($_REQUEST['whichteam']))
{
    $whichteam = (int)$_REQUEST['whichteam'];
}

// We do not want to query the database, if it is not necessary.
if ($playerinfo['team_invite'] != "")
{
    // Get invite info
    $invite = $db->Execute(" SELECT $dbtables[ships].ship_id, $dbtables[ships].team_invite, $dbtables[teams].team_name,$dbtables[teams].id
            FROM $dbtables[ships]
            LEFT JOIN $dbtables[teams] ON $dbtables[ships].team_invite = $dbtables[teams].id
            WHERE $dbtables[ships].email='$username'") or die($db->ErrorMsg());
    $invite_info  = $invite->fields;
}

// Get Team Info
if (!is_null($whichteam))
{
    $result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$whichteam") or die($db->ErrorMsg());
    $team        = $result_team->fields;
}
else
{
    $result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]") or die($db->ErrorMsg());
    $team        = $result_team->fields;
}

switch ($teamwhat)
{
    case 1: // INFO on single team
    {
        showinfo($whichteam, 0);
        LINK_BACK();
        break;
    }

    case 2: // LEAVE
    {
        if (!isTeamMember($team, $playerinfo))
        {
            echo "<b><font color=red>An error occured</font></b><br>You are not a member of this Team.";
            LINK_BACK();
            break;
        }

        if (!$confirmleave)
        {
            echo "$l_team_confirmleave <b>$team[team_name]</b> ? <a href=\"teams.php?teamwhat=$teamwhat&confirmleave=1&whichteam=$whichteam\">$l_yes</a> - <a href=\"teams.php\">$l_no</a><br><br>";
        }
        elseif ($confirmleave == 1)
        {
            if ($team[number_of_members] == 1)
            {
                if (!isTeamOwner($team, $playerinfo))
                {
                    $l_team_error = str_replace("[error]", "<b><font color=red>An error occured</font></b><br>", $l_team_error);
                    echo $l_team_error;
                    LINK_BACK();
                    continue;
                }

                $db->Execute("DELETE FROM $dbtables[teams] WHERE id=$whichteam");
                $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
                $db->Execute("UPDATE $dbtables[ships] SET team_invite=0 WHERE team_invite=$whichteam");

                $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] AND base='Y'");
                $i=0;
                while (!$res->EOF)
                {
                    $row = $res->fields;
                    $sectors[$i] = $row[sector_id];
                    $i++;
                    $res->MoveNext();
                }

                $db->Execute("UPDATE $dbtables[planets] SET corp=0 WHERE owner=$playerinfo[ship_id]");
                if (!empty($sectors))
                {
                    foreach ($sectors as $sector)
                    {
                        calc_ownership($sector);
                    }
                }
                defence_vs_defence ($db, $dbtables, $playerinfo['ship_id']);
                kick_off_planet($db, $dbtables, $playerinfo['ship_id'], $whichteam);

                $l_team_onlymember = str_replace("[team_name]", "<b>$team[team_name]</b>", $l_team_onlymember);
                echo $l_team_onlymember . "<br><br>";
                playerlog ($db, $dbtables, $playerinfo[ship_id], LOG_TEAM_LEAVE, "$team[team_name]");
            }
            else
            {
                if (isTeamOwner($team, $playerinfo))
                {
                    echo "$l_team_youarecoord <b>$team[team_name]</b>. $l_team_relinq<br><br>";
                    echo "<form action='teams.php' method=post>";
                    echo "<table><input type=hidden name=teamwhat value=$teamwhat><input type=hidden name=confirmleave value=2><input type=hidden name=whichteam value=$whichteam>";
                    echo "<tr><td>$l_team_newc</td><td><select name=newcreator>";
                    $res = $db->Execute("SELECT character_name,ship_id FROM $dbtables[ships] WHERE team=$whichteam ORDER BY character_name ASC");
                    while (!$res->EOF)
                    {
                        $row = $res->fields;
                        if (!isTeamOwner($team, $row))
                        {
                            echo "<option value=$row[ship_id]>$row[character_name]";
                        }
                        $res->MoveNext();
                    }
                    echo "</select></td></tr>";
                    echo "<tr><td><input type=submit value=$l_submit></td></tr>";
                    echo "</table>";
                    echo "</form>";
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
                    $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members-1 WHERE id=$whichteam");

                    $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] AND base='Y' AND corp!=0");
                    $i=0;
                    while (!$res->EOF)
                    {
                        $sectors[$i] = $res->fields[sector_id];
                        $i++;
                        $res->MoveNext();
                    }

                    $db->Execute("UPDATE $dbtables[planets] SET corp=0 WHERE owner=$playerinfo[ship_id]");
                    if (!empty($sectors))
                    {
                        foreach ($sectors as $sector)
                        {
                            calc_ownership($sector);
                        }
                    }

                    echo "$l_team_youveleft <b>$team[team_name]</b>.<br><br>";
                    defence_vs_defence($playerinfo[ship_id]);
                    kick_off_planet ($db, $dbtables, $playerinfo['ship_id'], $whichteam);
                    playerlog ($db, $dbtables, $playerinfo[ship_id], LOG_TEAM_LEAVE, "$team[team_name]");
                    playerlog ($db, $dbtables, $team[creator], LOG_TEAM_NOT_LEAVE, "$playerinfo[character_name]");
                }
            }
        }
        elseif ($confirmleave == 2)
        {
            // owner of a team is leaving and set a new owner
            $res = $db->Execute("SELECT character_name FROM $dbtables[ships] WHERE ship_id=$newcreator");
            $newcreatorname = $res->fields;
            echo "$l_team_youveleft <b>$team[team_name]</b> $l_team_relto $newcreatorname[character_name].<br><br>";
            $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
            $db->Execute("UPDATE $dbtables[ships] SET team=$newcreator WHERE team=$creator");
            $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members-1,creator=$newcreator WHERE id=$whichteam");

            $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] AND base='Y' AND corp!=0");
            $i=0;
            while (!$res->EOF)
            {
                $sectors[$i] = $res->fields[sector_id];
                $i++;
                $res->MoveNext();
            }

            $db->Execute("UPDATE $dbtables[planets] SET corp=0 WHERE owner=$playerinfo[ship_id]");
            if (!empty($sectors))
            {
                foreach ($sectors as $sector)
                {
                    calc_ownership($sector);
                }
            }

            playerlog ($db, $dbtables, $playerinfo[ship_id], LOG_TEAM_NEWLEAD, "$team[team_name]|$newcreatorname[character_name]");
            playerlog ($db, $dbtables, $newcreator, LOG_TEAM_LEAD,"$team[team_name]");
        }

        LINK_BACK();
        break;
    }

    case 3: // JOIN
    {
        if ($playerinfo[team] <> 0)
        {
            echo $l_team_leavefirst . "<br>";
        }
        else
        {
            if ($playerinfo[team_invite] == $whichteam)
            {
                $db->Execute("UPDATE $dbtables[ships] SET team=$whichteam,team_invite=0 WHERE ship_id=$playerinfo[ship_id]");
                $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members+1 WHERE id=$whichteam");
                echo "$l_team_welcome <b>$team[team_name]</b>.<br><br>";
                playerlog ($db, $dbtables, $playerinfo[ship_id], LOG_TEAM_JOIN, "$team[team_name]");
                playerlog ($db, $dbtables, $team[creator], LOG_TEAM_NEWMEMBER, "$team[team_name]|$playerinfo[character_name]");
            }
            else
            {
                echo "$l_team_noinviteto<br>";
            }
        }
        LINK_BACK();
        break;
    }

    case 4:
    {
        echo "Not implemented yet. Sorry! :)<br><br>";
        LINK_BACK();
        break;
    }

    case 5: // Eject member
    {
        // Check if Co-ordinator of team.
        // If not display "An error occured, You are not the leader of this Team." message.
        // Then show link back and break;

        if (!isTeamOwner($team, $playerinfo))
        {
            $l_team_error = str_replace("[error]", "<b><font color=red>An error occured</font></b><br>", $l_team_error);
            echo $l_team_error;
            LINK_BACK();
            continue;
        }
        else
        {
            $who = stripnum($who);
            $result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id=$who");
            $whotoexpel = $result->fields;
            if (!$confirmed)
            {
                echo "$l_team_ejectsure $whotoexpel[character_name]? <a href=\"teams.php?teamwhat=$teamwhat&confirmed=1&who=$who\">$l_yes</a> - <a href=\"teams.php\">$l_no</a><br>";
            }
            else
            {
                // check whether the player we are ejecting might have already left in the meantime
                // should go here if ($whotoexpel[team] ==

                $db->Execute("UPDATE $dbtables[planets] SET corp='0' WHERE owner='$who'");
                $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$who'");

                // No more necessary due to COUNT(*) in previous SQL statement
                // $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members-1 WHERE id=$whotoexpel[team]");

                playerlog ($db, $dbtables, $who, LOG_TEAM_KICK, "$team[team_name]");
                echo "$whotoexpel[character_name] $l_team_ejected<br>";
            }
            LINK_BACK();
        }
        break;
    }

    case 6: // Create Team
    {
        if ($playerinfo['team'] <> 0)
        {
            echo $l_team_leavefirst . "<br>";
            LINK_BACK();
            continue;
        }

        if (!$teamname)
        {
            echo "<form action=\"teams.php\" method=post>";
            echo $l_team_entername . ": ";
            if ($testing)
            {
                echo "<input type=hidden name=swordfish value='$swordfish'>";
            }
            echo "<input type=hidden name=teamwhat value=$teamwhat>";
            echo "<input type=text name=teamname size=40 maxlength=40><br>";
            echo $l_team_enterdesc . ": ";
            echo "<input type=text name=teamdesc size=40 maxlength=254><br>";
            echo "<input type=submit value=$l_submit><input type=reset value=$l_reset>";
            echo "</form>";
            echo "<br><br>";
        }
        else
        {
            $teamname = trim(htmlspecialchars($teamname));
            $teamdesc = trim(htmlspecialchars($teamdesc));

            if (!validate_team($teamname, $teamdesc, $playerinfo['ship_id']))
            {
                echo "<span style='color:#f00;'>Team Creation Failed</span><br>Sorry you have either entered an invalid Team name or Team Description.<br>\n";
                LINK_BACK();
                break;
            }

            $res = $db->Execute("INSERT INTO $dbtables[teams] (id,creator,team_name,number_of_members,description) VALUES ('$playerinfo[ship_id]','$playerinfo[ship_id]','$teamname','1','$teamdesc')");
            $db->Execute("INSERT INTO $dbtables[zones] VALUES(NULL,'$teamname\'s Empire', $playerinfo[ship_id], 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
            $db->Execute("UPDATE $dbtables[ships] SET team='$playerinfo[ship_id]' WHERE ship_id='$playerinfo[ship_id]'");
            echo "$l_team_team <b>$teamname</b> $l_team_hcreated.<br><br>";
            playerlog ($db, $dbtables, $playerinfo[ship_id], LOG_TEAM_CREATE, "$teamname");
        }
        LINK_BACK();
        break;
    }

    case 7: // INVITE player
    {
        if (!isTeamMember($team, $playerinfo))
        {
            echo "<br>You are not in this team!<br>";
            LINK_BACK();
            break;
        }

        if (!$invited)
        {
            echo "<form action='teams.php' method=post>";
            echo "<table><input type=hidden name=teamwhat value=$teamwhat><input type=hidden name=invited value=1><input type=hidden name=whichteam value=$whichteam>";
            echo "<tr><td>$l_team_selectp:</td><td><select name=who style='width:200px;'>";

            $res = $db->Execute("SELECT character_name,ship_id FROM $dbtables[ships] WHERE team<>$whichteam AND ship_destroyed ='N' AND turns_used >0 ORDER BY character_name ASC");
            while (!$res->EOF)
            {
                $row = $res->fields;
                if (!isTeamOwner($team, $row))
                {
                    echo "<option value=$row[ship_id]>$row[character_name]";
                }
                $res->MoveNext();
            }

            echo "</select></td></tr>";
            echo "<tr><td><input type=submit value=$l_submit></td></tr>";
            echo "</table>";
            echo "</form>";
        }
        else
        {
            if ($playerinfo[team] == $whichteam)
            {
                if (is_null($who))
                {
                    echo "No player was selected.<br>\n";
                            echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu<br><br>";
                            break;
                }
                $res = $db->Execute("SELECT character_name,team_invite FROM $dbtables[ships] WHERE ship_id=$who");
                $newpl = $res->fields;
                if ($newpl[team_invite])
                {
                    $l_team_isorry = str_replace("[name]", $newpl[character_name], $l_team_isorry);
                    echo $l_team_isorry . "<br><br>";
                }
                else
                {
                    $db->Execute("UPDATE $dbtables[ships] SET team_invite=$whichteam WHERE ship_id=$who");
                    echo $l_team_plinvted . "<br>" . $l_team_plinvted2 . "<br>";
                    playerlog ($db, $dbtables, $who,LOG_TEAM_INVITE, "$team[team_name]");
                }
            }
            else
            {
                echo $l_team_notyours . "<br>";
            }
        }
        echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu<br><br>";
        break;
    }
    case 8: // REFUSE invitation
    {
        echo "$l_team_refuse <b>$invite_info[team_name]</b>.<br><br>";
        $db->Execute("UPDATE $dbtables[ships] SET team_invite=0 WHERE ship_id=$playerinfo[ship_id]");
        playerlog ($db, $dbtables, $team[creator], LOG_TEAM_REJECT, "$playerinfo[character_name]|$invite_info[team_name]");
        LINK_BACK();
        break;
    }

    case 9: // Edit Team
    {
        // Check if Co-ordinator of team.
        // If not display "An error occured, You are not the leader of this Team." message.
        // Then show link back and break;

        if (isTeamOwner($team, $playerinfo) != true)
        {
            $l_team_error = str_replace("[error]", "<b><font color=red>An error occured</font></b><br>", $l_team_error);
            echo $l_team_error;
            LINK_BACK();
            break;
        }

        if (!$update)
        {
            echo "<form action=\"teams.php\" method=post>";
            echo $l_team_edname . ": <br>";
            echo "<input type=hidden name=swordfish value='$swordfish'>";
            echo "<input type=hidden name=teamwhat value=$teamwhat>";
            echo "<input type=hidden name=whichteam value=$whichteam>";
            echo "<input type=hidden name=update value=true>";
            echo "<input type=text name=teamname size=40 maxlength=40 value=\"".$team[team_name]."\"><br>";
            echo $l_team_eddesc . ": <br>";
            echo "<input type=text name=teamdesc size=40 maxlength=254 value=\"".$team[description]."\"><br>";
            echo "<input type=submit value=$l_submit><input type=reset value=$l_reset>";
            echo "</form>";
            echo "<br><br>";
        }
        else
        {
            $teamname = trim(htmlspecialchars($teamname));
            $teamdesc = trim(htmlspecialchars($teamdesc));

            if (!validate_team($teamname, $teamdesc, $playerinfo[ship_id]))
            {
                echo "<span style='color:#f00;'>Team Edit Failed</span><br>Sorry you have either entered an invalid Team name or Team Description.<br>\n";
                LINK_BACK();
                break;
            }

            $res = $db->Execute("UPDATE $dbtables[teams] SET team_name='$teamname', description='$teamdesc' WHERE id=$whichteam") or die("<font color=red>error: " . $db->ErrorMSG() . "</font>");
            echo "$l_team_team <b>$teamname</b> $l_team_hasbeenr<br><br>";

            // Adding a log entry to all members of the renamed team
            $result_team_name = $db->Execute("SELECT ship_id FROM $dbtables[ships] WHERE team=$whichteam AND ship_id<>$playerinfo[ship_id]") or die("<font color=red>error: " . $db->ErrorMsg() . "</font>");
            playerlog ($db, $dbtables, $playerinfo[ship_id], LOG_TEAM_RENAME, "$teamname");
            while (!$result_team_name->EOF)
            {
                $teamname_array = $result_team_name->fields;
                playerlog ($db, $dbtables, $teamname_array[ship_id], LOG_TEAM_M_RENAME, "$teamname");
                $result_team_name->MoveNext();
            }
        }
        LINK_BACK();
        break;
    }

    default:
    {
        if (!$playerinfo['team'])
        {
            echo $l_team_notmember;
            DISPLAY_INVITE_INFO();
        }
        else
        {
            if ($playerinfo['team'] < 0)
            {
                $playerinfo['team'] = -$playerinfo['team'];
                $result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
                $whichteam = $result->fields;
                echo "$l_team_urejected <B>$whichteam[team_name]</B><br><br>";

                // No more necessary due to COUNT(*) in previous SQL statement
                // AND already done in case 5:
                # $db->Execute("UPDATE $dbtables[ships] SET team='0' WHERE ship_id='$playerinfo[ship_id]'");
                # $db->Execute("UPDATE $dbtables[teams] SET number_of_members=number_of_members-1 WHERE id=$whichteam");
                # playerlog ($db, $dbtables, $playerinfo[ship_id], LOG_TEAM_KICK, "$whichteam[team_name]");

                LINK_BACK();
                break;
            }
            $result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
            $whichteam = $result->fields;;
            if ($playerinfo['team_invite'])
            {
                $result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team_invite]");
                $whichinvitingteam = $result->fields;
            }
            $isowner = isTeamOwner($whichteam, $playerinfo);
            showinfo($playerinfo['team'], $isowner);
        }
        $res= $db->Execute("SELECT COUNT(*) as TOTAL FROM $dbtables[teams] WHERE admin='N';");
        $num_res = $res->fields;

        if ($num_res['TOTAL'] > 0)
        {
            DISPLAY_ALL_TEAMS();
        }
        else
        {
            echo $l_team_noteams . "<br><br>";
        }
        break;
    }
} // End of switch.

echo "<br><br>";
TEXT_GOTOMAIN();

function isTeamMember($team, $playerinfo)
{
    // Check to see if the player is in a team?  if not return false right there, else carry on.
    if ($playerinfo['team'] == 0)
    {
        return false;
    }

    // Check to see if the player is a member of $team['id'] if so return true, else return false.
    if ($playerinfo['team'] == $team['id'])
    {
        return true;
    }
    else
    {
        return false;
    }
}

function isTeamOwner($team, $playerinfo)
{
    // Check to see if the player is in a team?  if not return false right there, else carry on.
    if ($playerinfo['team'] == 0)
    {
        return false;
    }

    // Check to see if the player is the Owner of $team['creator'] if so return true, else return false.
    if ($playerinfo['ship_id'] == $team['creator'])
    {
        return true;
    }
    else
    {
        return false;
    }

}

function LINK_BACK()
{
    global $l_clickme, $l_team_menu;
    echo "<br><br><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<br><br>";
}

// Rewritten display of teams list
function DISPLAY_ALL_TEAMS()
{
    global $color, $color_line1, $color_line2, $color_header, $order, $type, $l_team_galax, $l_team_members, $l_team_member, $l_team_coord, $l_score, $l_name;
    global $db, $dbtables;

    echo "<br><br>$l_team_galax<br>";
    echo "<table style='width:100%; border:#fff 1px solid;' border='0' cellspacing='0' cellpadding='2'>";
    echo "<tr bgcolor=\"$color_header\">";

    if ($type == "d")
    {
        $type = "a";
        $by = "ASC";
    }
    else
    {
        $type = "d";
        $by = "DESC";
    }
    echo "<td><b><a class='new_link' style='font-size:14px;' href=teams.php?order=team_name&type=$type>$l_name</a></b></td>";
    echo "<td><b><a class='new_link' style='font-size:14px;' href=teams.php?order=number_of_members&type=$type>$l_team_members</a></b></td>";
    echo "<td><b><a class='new_link' style='font-size:14px;' href=teams.php?order=character_name&type=$type>$l_team_coord</a></b></td>";
    echo "<td><b><a class='new_link' style='font-size:14px;' href=teams.php?order=total_score&type=$type>$l_score</a></b></td>";
    echo "</tr>";
    $sql_query = "SELECT $dbtables[ships].character_name,
                COUNT(*) as number_of_members,
                ROUND(SQRT(SUM(POW($dbtables[ships].score,2)))) as total_score,
                $dbtables[teams].id,
                $dbtables[teams].team_name,
                $dbtables[teams].creator
                FROM $dbtables[ships]
                LEFT JOIN $dbtables[teams] ON $dbtables[ships].team = $dbtables[teams].id
                WHERE $dbtables[ships].team = $dbtables[teams].id AND admin='N'
                GROUP BY $dbtables[teams].team_name";

    // Setting if the order is Ascending or descending, if any.
    // Default is ordered by teams.team_name
    if ($order)
    {
        $sql_query = $sql_query ." ORDER BY " . $order . " $by";
    }

    $res = $db->Execute($sql_query) or die($db->ErrorMsg());
    $color = $color_line1;

    while (!$res->EOF)
    {
        $row = $res->fields;
        echo "<tr bgcolor=\"$color\">";
        echo "<td><a href=teams.php?teamwhat=1&whichteam=".$row[id].">".$row[team_name]."</a></td>";
        echo "<td>".$row[number_of_members]."</td>";

        // This fixes it so that it actually displays the coordinator, and not the first member of the team.
        $sql_query_2 = "SELECT character_name FROM $dbtables[ships] WHERE ship_id = $row[creator]";
        $res2 = $db->Execute($sql_query_2) or die($db->ErrorMsg());
        while (!$res2->EOF)
        {
            $row2 = $res2->fields;
            $res2->MoveNext();
        }

        // If there is a way to redo the original sql query instead, please, do so, but I didnt see a way to.
        echo "<td><a href=mailto2.php?name=".$row2[character_name].">".$row2[character_name]."</a></td>";
        echo "<td>$row[total_score]</td>";
        echo "</tr>";
        if ($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }

        $res->MoveNext();
    }
    echo "</table><br>";
}

function DISPLAY_INVITE_INFO()
{
    global $playerinfo, $invite_info, $l_team_noinvite, $l_team_ifyouwant, $l_team_tocreate, $l_clickme, $l_team_injoin, $l_team_tojoin, $l_team_reject, $l_team_or;
    if (!$playerinfo['team_invite'])
    {
        echo "<br><br><font color=blue size=2><b>$l_team_noinvite</b></font><br>";
        echo $l_team_ifyouwant . "<br>";
        echo "<a href=\"teams.php?teamwhat=6\">$l_clickme</a> $l_team_tocreate<br><br>";
    }
    else
    {
        echo "<br><br><font color=blue size=2><b>$l_team_injoin ";
        echo "<a href=teams.php?teamwhat=1&whichteam=$playerinfo[team_invite]>$invite_info[team_name]</a>.</b></font><br>";
        echo "<a href=teams.php?teamwhat=3&whichteam=$playerinfo[team_invite]>$l_clickme</a> $l_team_tojoin <b>$invite_info[team_name]</b> $l_team_or <a href=teams.php?teamwhat=8&whichteam=$playerinfo[team_invite]>$l_clickme</a> $l_team_reject<br><br>";
    }
}

function showinfo($whichteam,$isowner)
{
    global $playerinfo, $invite_info, $team, $l_team_coord, $l_team_member, $l_options, $l_team_ed, $l_team_inv, $l_team_leave, $l_team_members, $l_score, $l_team_noinvites, $l_team_pending;
    global $db, $dbtables, $l_team_eject;

    // Heading
    echo"<div align=center>";
    echo "<h3><font color=white><B>$team[team_name]</b>";
    echo "<br><font size=2>\"<i>$team[description]</i>\"</font></h3>";
    if ($playerinfo[team] == $team[id])
    {
        echo "<font color=white>";
        if ($playerinfo[ship_id] == $team[creator])
        {
            echo "$l_team_coord ";
        }
        else
        {
            echo "$l_team_member ";
        }
        echo "$l_options<br><font size=2>";
        if ( isTeamOwner($team, $playerinfo) == true)
        {
            echo "[<a href=teams.php?teamwhat=9&whichteam=$playerinfo[team]>$l_team_ed</a>] - ";
        }
        echo "[<a href=teams.php?teamwhat=7&whichteam=$playerinfo[team]>$l_team_inv</a>] - [<a href=teams.php?teamwhat=2&whichteam=$playerinfo[team]>$l_team_leave</a>]</font></font>";
    }
    DISPLAY_INVITE_INFO();
    echo "</div>";

    // Main table
    echo "<table border=2 cellspacing=2 cellpadding=2 bgcolor=\"#400040\" width=\"75%\" align=center>";
    echo "<tr>";
    echo "<td><font color=white>$l_team_members</font></td>";
    echo "</tr><tr bgcolor=$color_line2>";
    $result  = $db->Execute("SELECT * FROM $dbtables[ships] WHERE team=$whichteam");
    while (!$result->EOF)
    {
        $member = $result->fields;
        echo "<td> - $member[character_name] ($l_score $member[score])";
        if ($isowner && ($member[ship_id] != $playerinfo[ship_id]))
        {
            echo " - <font size=2>[<a href=\"teams.php?teamwhat=5&who=$member[ship_id]\">$l_team_eject</a>]</font></td>";
        }
        else
        {
            if ($member[ship_id] == $team[creator])
            {
                echo " - $l_team_coord </td>";
            }
        }
        echo "</tr><tr bgcolor=$color_line2>";
        $result->MoveNext();
    }

    // Displays for members name
    $res = $db->Execute("SELECT ship_id,character_name FROM $dbtables[ships] WHERE team_invite=$whichteam");
    echo "<td bgcolor=$color_line2><font color=white>$l_team_pending <b>$team[team_name]</b></font></td>";
    echo "</tr><tr>";
    if ($res->RecordCount() > 0)
    {
        echo "</tr><tr bgcolor=$color_line2>";
        while (!$res->EOF)
        {
            $who = $res->fields;
            echo "<td> - $who[character_name]</td>";
            echo "</tr><tr bgcolor=$color_line2>";
            $res->MoveNext();
        }
    }
    else
    {
        echo "<td>$l_team_noinvites <b>$team[team_name]</b>.</td>";
        echo "</tr><tr>";
    }
    echo "</tr></table>";
}

function validate_team($name = NULL, $desc = NULL, $creator = NULL)
{
    global $db,$dbtables, $db_type;

    $name = trim($name);
    $desc = trim($desc);
    $creator = (int)$creator;

    if ( (is_null($name) || empty($name)) || (is_null($desc) || empty($desc)) || (is_null($creator) || empty($creator)) )
    {
        return false;
    }

    if (!preg_match('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $name));
    {
        return false;
    }

    if (!preg_match('/[^A-Za-z0-9\_\s\-\.\']+/', ' ', $desc));
    {
        return false;
    }

    // Just a test to see if an team with a name of $name exists.
    // This is just a temp fix until we find a better one.
    $res= $db->Execute("SELECT COUNT(*) as found FROM $dbtables[teams] WHERE team_name = '{$name}' AND creator != $creator;");
    $num_res = $res->fields;
    if ($num_res['found'] > 0)
    {
        return false;
    }

    return true;
}

include "footer.php";
?>
