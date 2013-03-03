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
// File: includes/xenobe_to_sec_def.php

if (strpos ($_SERVER['PHP_SELF'], 'xenobe_to_sec_def.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include './error.php';
}

function xenobe_to_sec_def ($db)
{
    // Xenobe to sector defense

    global $playerinfo, $targetlink, $xenobeisdead;

    // Check for sector defenses
    if ($targetlink > 0)
    {
        $resultf = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=? and defence_type ='F' ORDER BY quantity DESC", array ($targetlink));
        \bnt\dbop::dbresult ($db, $resultf, __LINE__, __FILE__);
        $i = 0;
        $total_sector_fighters = 0;
        if ($resultf instanceof ADORecordSet)
        {
            while (!$resultf->EOF)
            {
                $defences[$i] = $resultf->fields;
                $total_sector_fighters += $defences[$i]['quantity'];
                $i++;
                $resultf->MoveNext();
            }
        }

        $resultm = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=? and defence_type ='M'", array ($targetlink));
        \bnt\dbop::dbresult ($db, $resultm, __LINE__, __FILE__);
        $i = 0;
        $total_sector_mines = 0;
        if ($resultm instanceof ADORecordSet)
        {
            while (!$resultm->EOF)
            {
                $defences[$i] = $resultm->fields;
                $total_sector_mines += $defences[$i]['quantity'];
                $i++;
                $resultm->MoveNext();
            }
        }

        if ($total_sector_fighters > 0 || $total_sector_mines > 0 || ($total_sector_fighters > 0 && $total_sector_mines > 0)) // Dest link has defenses so lets attack them
        {
            \bnt\PlayerLog::writeLog ($db, $playerinfo['ship_id'], LOG_RAW, "ATTACKING SECTOR DEFENCES $total_sector_fighters fighters and $total_sector_mines mines.");
            $targetfighters = $total_sector_fighters;
            $playerbeams = \bnt\CalcLevels::Beams ($playerinfo['beams'], $level_factor);
            if ($playerbeams > $playerinfo['ship_energy'])
            {
                $playerbeams = $playerinfo['ship_energy'];
            }

            $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $playerbeams;
            $playershields = \bnt\CalcLevels::Shields ($playerinfo['shields'], $level_factor);
            if ($playershields > $playerinfo['ship_energy'])
            {
                $playershields = $playerinfo['ship_energy'];
            }

            $playertorpnum = round (pow ($level_factor, $playerinfo['torp_launchers'])) * 2;
            if ($playertorpnum > $playerinfo['torps'])
            {
                $playertorpnum = $playerinfo['torps'];
            }

            $playertorpdmg = $torp_dmg_rate * $playertorpnum;
            $playerarmor = $playerinfo['armor_pts'];
            $playerfighters = $playerinfo['ship_fighters'];
            $totalmines = $total_sector_mines;
            if ($totalmines > 1)
            {
                $roll = mt_rand (1 , $totalmines);
            }
            else
            {
                $roll = 1;
            }

            $totalmines = $totalmines - $roll;
            $playerminedeflect = $playerinfo['ship_fighters']; // Xenobe keep as many deflectors as fighters

            // Combat - Beams v fighters
            if ($targetfighters > 0 && $playerbeams > 0)
            {
                if ($playerbeams > round ($targetfighters / 2))
                {
                    $temp = round ($targetfighters / 2);
                    $targetfighters = $temp;
                    $playerbeams = $playerbeams - $temp;
                }
                else
                {
                    $targetfighters = $targetfighters - $playerbeams;
                    $playerbeams = 0;
                }
            }

            // Torpedoes v. fighters
            if ($targetfighters > 0 && $playertorpdmg > 0)
            {
                if ($playertorpdmg > round ($targetfighters / 2))
                {
                    $temp = round ($targetfighters / 2);
                    $targetfighters = $temp;
                    $playertorpdmg = $playertorpdmg - $temp;
                }
                else
                {
                    $targetfighters = $targetfighters - $playertorpdmg;
                    $playertorpdmg = 0;
                }
            }

            // Fighters v. fighters
            if ($playerfighters > 0 && $targetfighters > 0)
            {
                if ($playerfighters > $targetfighters)
                {
                    echo $langvars['l_sf_destfightall'];
                    $temptargfighters = 0;
                }
                else
                {
                    $temptargfighters = $targetfighters - $playerfighters;
                }

                if ($targetfighters > $playerfighters)
                {
                    $tempplayfighters = 0;
                }
                else
                {
                    $tempplayfighters = $playerfighters - $targetfighters;
                }

                $playerfighters = $tempplayfighters;
                $targetfighters = $temptargfighters;
            }

            // There are still fighters, so armor v. fighters
            if ($targetfighters > 0)
            {
                if ($targetfighters > $playerarmor)
                {
                    $playerarmor = 0;
                }
                else
                {
                    $playerarmor = $playerarmor - $targetfighters;
                }
            }

            // Get rid of the sector fighters that died
            $fighterslost = $total_sector_fighters - $targetfighters;
            \bnt\bntfighters::destroy ($db, $targetlink, $fighterslost);

            // Message the defence owner with what happened
            $l_sf_sendlog = str_replace("[player]", "Xenobe $playerinfo[character_name]", $langvars['l_sf_sendlog']);
            $l_sf_sendlog = str_replace("[lost]", $fighterslost, $l_sf_sendlog);
            $l_sf_sendlog = str_replace("[sector]", $targetlink, $l_sf_sendlog);
            bnt_sectorDefense::message_defense_owner ($db, $targetlink, $l_sf_sendlog);

            // Update Xenobe after comnbat
            $armor_lost = $playerinfo['armor_pts'] - $playerarmor;
            $fighters_lost = $playerinfo['ship_fighters'] - $playerfighters;
            $energy = $playerinfo['ship_energy'];
            $update1 = $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy=?, ship_fighters=ship_fighters-?, armor_pts=armor_pts-?,torps=torps-? WHERE ship_id=?", array ($energy, $fighters_lost, $armor_lost, $playertorpnum, $playerinfo['ship_id']));
            \bnt\dbop::dbresult ($db, $update1, __LINE__, __FILE__);

            // Check to see if Xenobe is dead
            if ($playerarmor < 1)
            {
                $l_sf_sendlog2 = str_replace("[player]", "Xenobe " . $playerinfo['character_name'], $langvars['l_sf_sendlog2']);
                $l_sf_sendlog2 = str_replace("[sector]", $targetlink, $l_sf_sendlog2);
                bnt_sectorDefense::message_defense_owner ($db, $targetlink, $l_sf_sendlog2);
                \bnt\bounty::cancel ($db, $playerinfo['ship_id']);
                \bnt\bntplayer::kill ($db, $playerinfo['ship_id'], false, $langvars);
                $xenobeisdead = 1;

                return;
            }

            // Xenobe is still alive, so he hits mines, and logs it
            $l_chm_hehitminesinsector = str_replace("[chm_playerinfo_character_name]", "Xenobe " . $playerinfo['character_name'], $langvars['l_chm_hehitminesinsector']);
            $l_chm_hehitminesinsector = str_replace("[chm_roll]", $roll, $l_chm_hehitminesinsector);
            $l_chm_hehitminesinsector = str_replace("[chm_sector]", $targetlink, $l_chm_hehitminesinsector);
            bnt_sectorDefense::message_defense_owner ($db, $targetlink, "$l_chm_hehitminesinsector");

            // Deflectors v. mines
            if ($playerminedeflect >= $roll)
            {
                // Took no mine damage due to virtual mine deflectors
            }
            else
            {
                $mines_left = $roll - $playerminedeflect;

                // Shields v. mines
                if ($playershields >= $mines_left)
                {
                    $update2 = $db->Execute("UPDATE {$db->prefix}ships SET ship_energy=ship_energy-? WHERE ship_id=?", array ($mines_left, $playerinfo['ship_id']));
                    \bnt\dbop::dbresult ($db, $update2, __LINE__, __FILE__);
                }
                else
                {
                    $mines_left = $mines_left - $playershields;

                    // Armor v. mines
                    if ($playerarmor >= $mines_left)
                    {
                        $update2 = $db->Execute("UPDATE {$db->prefix}ships SET armor_pts=armor_pts-?, ship_energy=0 WHERE ship_id=?", array ($mines_left, $playerinfo['ship_id']));
                        \bnt\dbop::dbresult ($db, $update2, __LINE__, __FILE__);
                    }
                    else
                    {
                        // Xenobe dies, logs the fact that he died
                        $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_playerinfo_character_name]", "Xenobe " . $playerinfo['character_name'], $langvars['l_chm_hewasdestroyedbyyourmines']);
                        $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_sector]", $targetlink, $l_chm_hewasdestroyedbyyourmines);
                        bnt_sectorDefense::message_defense_owner ($db, $targetlink, "$l_chm_hewasdestroyedbyyourmines");

                        // Actually kill the Xenobe now
                        \bnt\bntbounty::cancel ($db, $playerinfo['ship_id']);
                        \bnt\bntplayer::kill ($db, $playerinfo['ship_id'], false, $langvars);
                        $xenobeisdead = 1;
                        // Lets get rid of the mines now and return out of this function
                        \bnt\bntmines::explode ($db, $targetlink, $roll);

                        return;
                    }
                }
            }
            \bnt\bntmines::explode ($db, $targetlink, $roll); // Dispose of the mines now
        }
        else
        {
            // This was called without any sector defences to attack
            return;
        }
    }
}
?>
