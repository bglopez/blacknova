<?php

##############################################
## Note to self, remove all the debug admin ##
## logs to reduce the amount records in the ##
## logs table and to also reduce the total  ##
## size of the database.                    ##
##############################################

include("config.php");
updatecookie();
include("languages/$lang");


connectdb();

if(checklogin())
{
	die();//shouldnt we redirect to login page or show the login notice here?
}

$title=$l_att_title;
include("header.php");

//-------------------------------------------------------------------------------------------------
$db->Execute("LOCK TABLES $dbtables[ships] WRITE, $dbtables[universe] WRITE, $dbtables[bounty] WRITE $dbtables[zones] READ, $dbtables[planets] WRITE, $dbtables[news] WRITE, $dbtables[logs] WRITE");
$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo=$result->fields;

$ship_id = stripnum($ship_id);

$result2 = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE ship_id='$ship_id'");
$targetinfo=$result2->fields;

bigtitle();

srand((double)microtime()*1000000);
$playerscore = gen_score($playerinfo['ship_id']);
//echo $playerscore;
$targetscore = gen_score($targetinfo['ship_id']);
//echo $targetscore;
$playerscore = $playerscore * $playerscore;
$targetscore = $targetscore * $targetscore;
/* check to ensure target is in the same sector as player */
if($targetinfo['sector'] != $playerinfo['sector'] || $targetinfo['on_planet'] == "Y")
{
	echo "$l_att_notarg<BR><BR>";
}
elseif($playerinfo['turns'] < 1)
{
	echo "$l_att_noturn<BR><BR>";
}
else if( isSameTeam($playerinfo['team'], $targetinfo['team']) )
{
	echo "<div style='color:#FFFF00;'>Sorry, You cannot attack a fellow Teamemate!</div>\n";
}
else
{
	/* determine percent chance of success in detecting target ship - based on player's sensors and opponent's cloak */
	$success = (10 - $targetinfo['cloak'] + $playerinfo['sensors']) * 5;
	if($success < 5)
	{
		$success = 5;
	}

	if($success > 95)
	{
		$success = 95;
	}
	$flee = (10 - $targetinfo['engines'] + $playerinfo['engines']) * 5;
	$roll = rand(1, 100);
	$roll2 = rand(1, 100);

	$res = $db->Execute("SELECT allow_attack,$dbtables[universe].zone_id FROM $dbtables[zones],$dbtables[universe] WHERE sector_id='$targetinfo[sector]' AND $dbtables[zones].zone_id=$dbtables[universe].zone_id");
	$zoneinfo = $res->fields;
	if($zoneinfo['allow_attack'] == 'N')
	{
		echo "$l_att_noatt<BR><BR>";
	}
	elseif($flee < $roll2)
	{
		echo "$l_att_flee<BR><BR>";
		$db->Execute("UPDATE $dbtables[ships] SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
		playerlog($targetinfo['ship_id'], LOG_ATTACK_OUTMAN, "$playerinfo[character_name]");
	}
	elseif($roll > $success)
	{
		/* if scan fails - inform both player and target. */
		echo "$l_planet_noscan<BR><BR>";
		$db->Execute("UPDATE $dbtables[ships] SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
		playerlog($targetinfo['ship_id'], LOG_ATTACK_OUTSCAN, "$playerinfo[character_name]");
	}
	else
	{
		/* if scan succeeds, show results and inform target. */
		$shipavg = get_avg_tech($targetship, "ship");

		if($shipavg > $ewd_maxhullsize)
		{
			$chance = ($shipavg - $ewd_maxhullsize) * 10;
		}
		else
		{
			$chance = 0;
		}
		$random_value = rand(1,100);

		if($targetinfo['dev_emerwarp'] > 0 && $random_value > $chance)
		{
			/* need to change warp destination to random sector in universe */
			$rating_change=round($targetinfo['rating']*.1);
			$dest_sector=rand(1, $sector_max-1);
			$db->Execute("UPDATE $dbtables[ships] SET turns=turns-1,turns_used=turns_used+1,rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
			$l_att_ewdlog=str_replace("[name]",$playerinfo['character_name'],$l_att_ewdlog);
			$l_att_ewdlog=str_replace("[sector]",$playerinfo['sector'],$l_att_ewdlog);
			playerlog($targetinfo['ship_id'], LOG_ATTACK_EWD, "$playerinfo[character_name]");
			$result_warp = $db->Execute ("UPDATE $dbtables[ships] SET sector=$dest_sector, dev_emerwarp=dev_emerwarp-1,cleared_defences=' ' WHERE ship_id=$targetinfo[ship_id]");
			log_move($targetinfo['ship_id'],$dest_sector);
			echo "$l_att_ewd<BR><BR>";
		}
		else
		{
			if(($targetscore / $playerscore < $bounty_ratio || $targetinfo['turns_used'] < $bounty_minturns) && ( preg_match("/(\@xenobe)$/",$targetinfo['email']) === 0 )) // bounty-free Xenobe attacking allowed.
			{
				//changed xen check to a regexp cause a player could put @xen or whatever in his email address
				// so (\@xenobe) is an exact match and the $ symbol means "this is the *end* of the string
				//so our custom @xenobe names will match, nothing else will
				// Check to see if there is Federation bounty on the player. If there is, people can attack regardless.
				$btyamount = 0;
				$hasbounty = $db->Execute("SELECT SUM(amount) AS btytotal FROM $dbtables[bounty] WHERE bounty_on = $targetinfo[ship_id] AND placed_by = 0");
				if($hasbounty)
				{
					$resx = $hasbounty->fields;
					$btyamount = $resx['btytotal'];
				}

				if($btyamount <= 0)
				{
					$bounty = ROUND($playerscore * $bounty_maxvalue);
					$insert = $db->Execute("INSERT INTO $dbtables[bounty] (bounty_on,placed_by,amount) values ($playerinfo[ship_id], 0 ,$bounty)");
					playerlog($playerinfo['ship_id'],LOG_BOUNTY_FEDBOUNTY,"$bounty");
					echo "<div style='color:#FF0000;'>{$l_by_fedbounty2}</div>\n";
					echo "<br />\n";
				}
			}

			if($targetinfo['dev_emerwarp'] > 0)
			{
				playerlog($targetinfo['ship_id'], LOG_ATTACK_EWDFAIL, $playerinfo['character_name']);
			}
			$targetenergy = $targetinfo['ship_energy'];
			$playerenergy = $playerinfo['ship_energy'];
			//I added these two so we can have a value for debugging and reporting totals
			//if we use the variables in calcs below, change the display of stats too

			$targetbeams = NUM_BEAMS($targetinfo['beams']);
			if($targetbeams>$targetinfo['ship_energy'])
			{
				$targetbeams=$targetinfo['ship_energy'];
			}
			$targetinfo['ship_energy']=$targetinfo['ship_energy']-$targetbeams;
			//why dont we set targetinfo[ship_energy] to a variable instead?

			$playerbeams = NUM_BEAMS($playerinfo['beams']);
			if($playerbeams>$playerinfo['ship_energy'])
			{
				$playerbeams=$playerinfo['ship_energy'];
			}
			$playerinfo['ship_energy']=$playerinfo['ship_energy']-$playerbeams;

			$playershields = NUM_SHIELDS($playerinfo['shields']);
			if($playershields>$playerinfo['ship_energy'])
			{
				$playershields=$playerinfo['ship_energy'];
			}
			$playerinfo['ship_energy']=$playerinfo['ship_energy']-$playershields;

			$targetshields = NUM_SHIELDS($targetinfo['shields']);
			if($targetshields>$targetinfo['ship_energy'])
			{
				$targetshields=$targetinfo['ship_energy'];
			}
			$targetinfo['ship_energy']=$targetinfo['ship_energy']-$targetshields;

			$playertorpnum = round(mypw($level_factor,$playerinfo['torp_launchers']))*10;
			if($playertorpnum > $playerinfo['torps'])
			{
				$playertorpnum = $playerinfo['torps'];
			}

			$targettorpnum = round(mypw($level_factor,$targetinfo['torp_launchers']))*10;
			if($targettorpnum > $targetinfo['torps'])
			{
				$targettorpnum = $targetinfo['torps'];
			}
			$playertorpdmg = $torp_dmg_rate*$playertorpnum;
			$targettorpdmg = $torp_dmg_rate*$targettorpnum;
			$playerarmor = $playerinfo['armor_pts'];
			$targetarmor = $targetinfo['armor_pts'];
			$playerfighters = $playerinfo['ship_fighters'];
			$targetfighters = $targetinfo['ship_fighters'];
			$targetdestroyed = 0;
			$playerdestroyed = 0;

			echo "$l_att_att $targetinfo[character_name] $l_abord $targetinfo[ship_name]:<BR><BR>";

			$bcs_info = NULL;
			$bcs_info[] = array("Beams(lvl)",		"{$playerbeams}({$playerinfo['beams']})",				"{$targetbeams}({$targetinfo['beams']})" );
			$bcs_info[] = array("Shields(lvl)",		"{$playershields}({$playerinfo['shields']})",			"{$targetshields}({$targetinfo['shields']})" );
			$bcs_info[] = array("Energy(Start)",	"{$playerinfo['ship_energy']}({$playerenergy})",		"{$targetinfo['ship_energy']}({$targetenergy})" );
			$bcs_info[] = array("Torps(lvl)",		"{$playertorpnum}({$playerinfo['torp_launchers']})",	"{$targettorpnum}({$targetinfo['torp_launchers']})" );
			$bcs_info[] = array("TorpDmg",			"{$playertorpdmg}",										"{$targettorpdmg}" );
			$bcs_info[] = array("Fighters",			"{$playerfighters}",									"{$targetfighters}" );
			$bcs_info[] = array("Armor(lvl)",		"{$playerarmor}({$playerinfo['armor']})",				"{$targetarmor}({$targetinfo['beams']})" );
			$bcs_info[] = array("Escape Pod",		"{$playerinfo['dev_escapepod']}",						"{$targetinfo['dev_escapepod']}" );

			echo "<div style='width:800px; margin:auto; text-align:center; color:#FFFFFF;'>\n";

			echo "  <div style='text-align:center; font-size:24px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Blacknova Combat System. (<span style='color:#00FF00;'>BETA</span>)</div>\n";
			echo "  <div style='height:1px;'></div>\n";

			echo "<table style='width:100%; border:none; background-color:#FFCC00;' cellpadding='0' cellspacing='1'>\n";

			echo "  <tr style='background-color:{$color_header}; font-size:16px;'>\n";
			echo "    <td style='text-align:center; font-weight:bold;background-color:{$color_header};'>Stats</td>\n";
			echo "    <td style='width:33%; text-align:center; font-weight:bold;background-color:{$color_header};'>You [<span style='color:#00FF00; font-size:12px; font-weight:normal;'>{$playerinfo['character_name']}</span>]</td>\n";
			echo "    <td style='width:33%; text-align:center; font-weight:bold;background-color:{$color_header};'>Target [<span style='color:#00FF00; font-size:12px; font-weight:normal;'>{$targetinfo['character_name']}</span>]</td>\n";
			echo "  </tr>\n";

			$color = $color_line1;

			for ($bcs_index=0; $bcs_index<count($bcs_info); $bcs_index++)
			{
				echo "  <tr>\n";
				echo "    <td style='text-align:right; font-weight:bold; padding:4px;background-color:{$color};'>{$bcs_info[$bcs_index][0]}</td>\n";
				echo "    <td style='width:33%; text-align:right; padding:4px;background-color:{$color};'>{$bcs_info[$bcs_index][1]}</td>\n";
				echo "    <td style='width:33%; text-align:right; padding:4px;background-color:{$color};'>{$bcs_info[$bcs_index][2]}</td>\n";
				echo "  </tr>\n";

				if($color == $color_line1)
				{
					$color = $color_line2;
				}
				else
				{
					$color = $color_line1;
				}
			}
			echo "</table>\n";
			echo "  <div style='height:4px;'></div>\n";

			echo "  <div style='text-align:left; font-size:14px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Beams</div>\n";
			echo "  <div style='height:1px;'></div>\n";

			echo "  <div style='text-align:left; font-size:12px; padding:4px; background-color:{$color_line1}; border:#FFCC00 1px solid;'>\n";

			$bcs_stats_info = false;

			if($targetfighters > 0 && $playerbeams > 0)
			{
				$bcs_stats_info = true;
				if($playerbeams > round($targetfighters / 2))
				{
					$temp = round($targetfighters/2);
					$lost = $targetfighters-$temp;
					//maybe we should report on how many beams fired , etc for comparision/bugtracking
					echo "$targetinfo[character_name] $l_att_lost $lost $l_fighters<BR>";
					$targetfighters = $temp;
					$playerbeams = $playerbeams-$lost;
				}
				else
				{
					$targetfighters = $targetfighters-$playerbeams;
					echo "$targetinfo[character_name] $l_att_lost $playerbeams $l_fighters<BR>";
					$playerbeams = 0;
				}
			}

			if($playerfighters > 0 && $targetbeams > 0)
			{
				$bcs_stats_info = true;
				if($targetbeams > round($playerfighters / 2))
				{
					$temp=round($playerfighters/2);
					$lost=$playerfighters-$temp;
					echo "$l_att_ylost $lost $l_fighters<BR>";
					$playerfighters=$temp;
					$targetbeams=$targetbeams-$lost;
				}
				else
				{
					$playerfighters=$playerfighters-$targetbeams;
					echo "$l_att_ylost $targetbeams $l_fighters<BR>";
					$targetbeams=0;
				}
			}
			
			if($playerbeams > 0)
			{
				$bcs_stats_info = true;
				if($playerbeams > $targetshields)
				{
					$playerbeams=$playerbeams-$targetshields;
					$targetshields=0;
					echo "$targetinfo[character_name]". $l_att_sdown ."<BR>";
				}
				else
				{
					echo "$targetinfo[character_name]" . $l_att_shits ." $playerbeams $l_att_dmg.<BR>";
					$targetshields=$targetshields-$playerbeams;
					$playerbeams=0;
				}
			}
			
			if($targetbeams > 0)
			{
				$bcs_stats_info = true;
				if($targetbeams > $playershields)
				{
					$targetbeams=$targetbeams-$playershields;
					$playershields=0;
					echo "$l_att_ydown<BR>";
				}
				else
				{
					echo "$l_att_yhits $targetbeams $l_att_dmg.<BR>";
					$playershields=$playershields-$targetbeams;
					$targetbeams=0;
				}
			}
			
			if($playerbeams > 0)
			{
				$bcs_stats_info = true;
				if($playerbeams > $targetarmor)
				{
					$targetarmor=0;
					echo "$targetinfo[character_name] " .$l_att_sarm ."<BR>";
				}
				else
				{
					$targetarmor=$targetarmor-$playerbeams;
					echo "$targetinfo[character_name]". $l_att_ashit ." $playerbeams $l_att_dmg.<BR>";
				}
			}
			
			if($targetbeams > 0)
			{
				$bcs_stats_info = true;
				if($targetbeams > $playerarmor)
				{
					$playerarmor=0;
					echo "$l_att_yarm<BR>";
				}
				else
				{
					$playerarmor=$playerarmor-$targetbeams;
					echo "$l_att_ayhit $targetbeams $l_att_dmg.<BR>";
				}
			}

			if ($bcs_stats_info == false)
			{
				echo "No information available.<br />\n";
			}
			echo "  </div>\n";
			echo "  <div style='height:4px;'></div>\n";


			echo "  <div style='text-align:left; font-size:14px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Torpedos</div>\n";
			echo "  <div style='height:1px;'></div>\n";

			echo "  <div style='text-align:left; font-size:12px; padding:4px; background-color:{$color_line1}; border:#FFCC00 1px solid;'>\n";
			$bcs_stats_info = false;

			if($targetfighters > 0 && $playertorpdmg > 0)
			{
				$bcs_stats_info = true;
				if($playertorpdmg > round($targetfighters / 2))
				{
					$temp=round($targetfighters/2);
					$lost=$targetfighters-$temp;
					echo "$targetinfo[character_name] $l_att_lost $lost $l_fighters<BR>";
					$targetfighters=$temp;
					$playertorpdmg=$playertorpdmg-$lost;
				}
				else
				{
					$targetfighters=$targetfighters-$playertorpdmg;
					echo "$targetinfo[character_name] $l_att_lost $playertorpdmg $l_fighters<BR>";
					$playertorpdmg=0;
				}
			}
			
			if($playerfighters > 0 && $targettorpdmg > 0)
			{
				$bcs_stats_info = true;
				if($targettorpdmg > round($playerfighters / 2))
				{
					$temp=round($playerfighters/2);
					$lost=$playerfighters-$temp;
					echo "$l_att_ylost $lost $l_fighters<BR>";
					echo "$temp - $playerfighters - $targettorpdmg";
					$playerfighters=$temp;
					$targettorpdmg=$targettorpdmg-$lost;
				}
				else
				{
					$playerfighters=$playerfighters-$targettorpdmg;
					echo "$l_att_ylost $targettorpdmg $l_fighters<BR>";
					$targettorpdmg=0;
				}
			}

			if($playertorpdmg > 0)
			{
				$bcs_stats_info = true;
				if($playertorpdmg > $targetarmor)
				{
					$targetarmor=0;
					echo "$targetinfo[character_name]" . $l_att_sarm ."<BR>";
				}
				else
				{
					$targetarmor=$targetarmor-$playertorpdmg;
					echo "$targetinfo[character_name]" . $l_att_ashit . " $playertorpdmg $l_att_dmg.<BR>";
				}
			}

			if($targettorpdmg > 0)
			{
				$bcs_stats_info = true;
				if($targettorpdmg > $playerarmor)
				{
					$playerarmor=0;
					echo "$l_att_yarm<BR>";
				}
				else
				{
					$playerarmor=$playerarmor-$targettorpdmg;
					echo "$l_att_ayhit $targettorpdmg $l_att_dmg.<BR>";
				}
			}

			if ($bcs_stats_info == false)
			{
				echo "No information available.<br />\n";
			}

			echo "  </div>\n";
			echo "  <div style='height:4px;'></div>\n";


			echo "  <div style='text-align:left; font-size:14px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Fighters</div>\n";
			echo "  <div style='height:1px;'></div>\n";

			echo "  <div style='text-align:left; font-size:12px; padding:4px; background-color:{$color_line1}; border:#FFCC00 1px solid;'>\n";
			$bcs_stats_info = false;

			if($playerfighters > 0 && $targetfighters > 0)
			{
				$bcs_stats_info = true;
				if($playerfighters > $targetfighters)
				{
					echo "$targetinfo[character_name] $l_att_lostf<BR>";
					$temptargfighters=0;
				}
				else
				{
					echo "$targetinfo[character_name] $l_att_lost $playerfighters $l_fighters.<BR>";
					$temptargfighters=$targetfighters-$playerfighters;
				}
			
				if($targetfighters > $playerfighters)
				{
					echo "$l_att_ylostf<BR>";
					$tempplayfighters=0;
				}
				else
				{
					echo "$l_att_ylost $targetfighters $l_fighters.<BR>";
					$tempplayfighters=$playerfighters-$targetfighters;
				}
				$playerfighters=$tempplayfighters;
				$targetfighters=$temptargfighters;
			}

			if($playerfighters > 0)
			{
				$bcs_stats_info = true;
				if($playerfighters > $targetarmor)
				{
					$targetarmor=0;
					echo "$targetinfo[character_name]". $l_att_sarm . "<BR>";
				}
				else
				{
					$targetarmor=$targetarmor-$playerfighters;
					echo "$targetinfo[character_name]" . $l_att_ashit ." $playerfighters $l_att_dmg.<BR>";
				}
			}

			if($targetfighters > 0)
			{
				$bcs_stats_info = true;
				if($targetfighters > $playerarmor)
				{
					$playerarmor=0;
					echo "$l_att_yarm<BR>";
				}
				else
				{
					$playerarmor=$playerarmor-$targetfighters;
					echo "$l_att_ayhit $targetfighters $l_att_dmg.<BR>";
				}
			}

			if ($bcs_stats_info == false)
			{
				echo "No information available.<br />\n";
			}
			echo "  </div>\n";
			echo "  <div style='height:4px;'></div>\n";

			echo "  <div style='text-align:left; font-size:14px; font-weight:bold; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Outcome</div>\n";
			echo "  <div style='height:1px;'></div>\n";

			echo "  <div style='text-align:left; font-size:12px; padding:4px; background-color:{$color_line1}; border:#FFCC00 1px solid;'>\n";

			if($targetarmor < 1)
			{
				echo "$targetinfo[character_name]". $l_att_sdest ."<BR>";
				if($targetinfo['dev_escapepod'] == "Y")
				{
					$rating=round($targetinfo['rating']/2);
					echo "$l_att_espod (<span style='color:#FFFF00;'>You destroyed their ship but they got away in their Escape Pod</span>)<br />";
					$db->Execute("UPDATE $dbtables[ships] SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating',cleared_defences=' ',dev_lssd='N' WHERE ship_id=$targetinfo[ship_id]");
					playerlog($targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|Y");
					collect_bounty($playerinfo['ship_id'],$targetinfo['ship_id']);
				}
				else
				{
					playerlog($targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
					db_kill_player($targetinfo['ship_id']);
					collect_bounty($playerinfo['ship_id'],$targetinfo['ship_id']);
				}
			
				if($playerarmor > 0)
				{
					$rating_change=round($targetinfo['rating']*$rating_combat_factor);
					//Updating to always get a positive rating increase for xenobe and the credits they are carrying - rjordan
					$salv_credits = 0;

					if ( preg_match("/(\@xenobe)$/", $targetinfo['email']) !== 0 )
					{
						$db->Execute("UPDATE $dbtables[xenobe] SET active= N WHERE xenobe_id=$targetinfo[email]");
						if ($rating_change > 0)
						{
							$rating_change = 0 - $rating_change;
							playerlog($targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
							collect_bounty($playerinfo['ship_id'],$targetinfo['ship_id']);
							db_kill_player($targetinfo['ship_id']);
						}
						$salv_credits = $targetinfo['credits'];
					}
			
					$free_ore = round($targetinfo['ship_ore']/2);
					$free_organics = round($targetinfo['ship_organics']/2);
					$free_goods = round($targetinfo['ship_goods']/2);
					$free_holds = NUM_HOLDS($playerinfo['hull']) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
					if($free_holds > $free_goods)
					{
						$salv_goods=$free_goods;
						$free_holds=$free_holds-$free_goods;
					}
					elseif($free_holds > 0)
					{
						$salv_goods=$free_holds;
						$free_holds=0;
					}
					else
					{
						$salv_goods=0;
					}
					if($free_holds > $free_ore)
					{
						$salv_ore=$free_ore;
						$free_holds=$free_holds-$free_ore;
					}
					elseif($free_holds > 0)
					{
						$salv_ore=$free_holds;
						$free_holds=0;
					}
					else
					{
						$salv_ore=0;
					}
					if($free_holds > $free_organics)
					{
						$salv_organics=$free_organics;
						$free_holds=$free_holds-$free_organics;
					}
					elseif($free_holds > 0)
					{
						$salv_organics=$free_holds;
						$free_holds=0;
					}
					else
					{
						$salv_organics=0;
					}
					$ship_value=$upgrade_cost*(round(mypw($upgrade_factor, $targetinfo['hull']))+round(mypw($upgrade_factor, $targetinfo['engines']))+round(mypw($upgrade_factor, $targetinfo['power']))+round(mypw($upgrade_factor, $targetinfo['computer']))+round(mypw($upgrade_factor, $targetinfo['sensors']))+round(mypw($upgrade_factor, $targetinfo['beams']))+round(mypw($upgrade_factor, $targetinfo['torp_launchers']))+round(mypw($upgrade_factor, $targetinfo['shields']))+round(mypw($upgrade_factor, $targetinfo['armor']))+round(mypw($upgrade_factor, $targetinfo['cloak'])));
					$ship_salvage_rate=rand(10,20);
					$ship_salvage=$ship_value*$ship_salvage_rate/100+$salv_credits;  //added credits for xenobe - 0 if normal player - GunSlinger
			
					$l_att_ysalv=str_replace("[salv_ore]",$salv_ore,$l_att_ysalv);
					$l_att_ysalv=str_replace("[salv_organics]",$salv_organics,$l_att_ysalv);
					$l_att_ysalv=str_replace("[salv_goods]",$salv_goods,$l_att_ysalv);
					$l_att_ysalv=str_replace("[ship_salvage_rate]",$ship_salvage_rate,$l_att_ysalv);
					$l_att_ysalv=str_replace("[ship_salvage]",$ship_salvage,$l_att_ysalv);
					$l_att_ysalv=str_replace("[rating_change]",NUMBER(abs($rating_change)),$l_att_ysalv);
			
					echo "{$l_att_ysalv}<br />\n";
					$update3 = $db->Execute ("UPDATE $dbtables[ships] SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$playerinfo[ship_id]");
					$armor_lost=$playerinfo['armor_pts']-$playerarmor;
					$fighters_lost=$playerinfo['ship_fighters']-$playerfighters;
					$energy=$playerinfo['ship_energy'];
					$update3b = $db->Execute ("UPDATE $dbtables[ships] SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armor_pts=armor_pts-$armor_lost, torps=torps-$playertorpnum, turns=turns-1, turns_used=turns_used+1, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
					echo "$l_att_ylost $armor_lost $l_armorpts, $fighters_lost $l_fighters, $l_att_andused $playertorpnum $l_torps.<BR>";
				}
			}
			else
			{
				$l_att_stilship=str_replace("[name]",$targetinfo['character_name'],$l_att_stilship);
				echo "$l_att_stilship<BR>";
			
				$rating_change=round($targetinfo['rating']*.1);
				$armor_lost=$targetinfo['armor_pts']-$targetarmor;
				$fighters_lost=$targetinfo['ship_fighters']-$targetfighters;
				$energy=$targetinfo['ship_energy'];

				playerlog($targetinfo['ship_id'], LOG_ATTACKED_WIN, "$playerinfo[character_name]|$armor_lost|$fighters_lost");
				$update4 = $db->Execute ("UPDATE $dbtables[ships] SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armor_pts=armor_pts-$armor_lost, torps=torps-$targettorpnum WHERE ship_id=$targetinfo[ship_id]");
			
				$armor_lost=$playerinfo['armor_pts']-$playerarmor;
				$fighters_lost=$playerinfo['ship_fighters']-$playerfighters;
				$energy=$playerinfo['ship_energy'];

				$update4b = $db->Execute ("UPDATE $dbtables[ships] SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armor_pts=armor_pts-$armor_lost, torps=torps-$playertorpnum, turns=turns-1, turns_used=turns_used+1, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
				echo "$l_att_ylost $armor_lost $l_armorpts, $fighters_lost $l_fighters, $l_att_andused $playertorpnum $l_torps.<BR><BR>";
			}
			
			if($playerarmor < 1)
			{
				echo "$l_att_yshiplost<BR><BR>";
				if($playerinfo['dev_escapepod'] == "Y")
				{
					$rating=round($playerinfo['rating']/2);
					echo "$l_att_loosepod<BR><BR>";
					$db->Execute("UPDATE $dbtables[ships] SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating',dev_lssd='N' WHERE ship_id=$playerinfo[ship_id]");
					collect_bounty($targetinfo[ship_id],$playerinfo[ship_id]);
				}
				else
				{
					echo "Didnt have pod?! $playerinfo[dev_escapepod]<br>";
					db_kill_player($playerinfo['ship_id']);
					collect_bounty($targetinfo[ship_id],$playerinfo[ship_id]);
				}
			
				if($targetarmor > 0)
				{
					$free_ore = round($playerinfo[ship_ore]/2);
					$free_organics = round($playerinfo[ship_organics]/2);
					$free_goods = round($playerinfo[ship_goods]/2);
					$free_holds = NUM_HOLDS($targetinfo[hull]) - $targetinfo[ship_ore] - $targetinfo[ship_organics] - $targetinfo[ship_goods] - $targetinfo[ship_colonists];
					if($free_holds > $free_goods)
					{
						$salv_goods=$free_goods;
						$free_holds=$free_holds-$free_goods;
					}
					elseif($free_holds > 0)
					{
						$salv_goods=$free_holds;
						$free_holds=0;
					}
					else
					{
						$salv_goods=0;
					}
			
					if($free_holds > $free_ore)
					{
						$salv_ore=$free_ore;
						$free_holds=$free_holds-$free_ore;
					}
					elseif($free_holds > 0)
					{
						$salv_ore=$free_holds;
						$free_holds=0;
					}
					else
					{
						$salv_ore=0;
					}
			
					if($free_holds > $free_organics)
					{
						$salv_organics=$free_organics;
						$free_holds=$free_holds-$free_organics;
					}
					elseif($free_holds > 0)
					{
						$salv_organics=$free_holds;
						$free_holds=0;
					}
					else
					{
						$salv_organics=0;
					}
					$ship_value=$upgrade_cost*(round(mypw($upgrade_factor, $playerinfo[hull]))+round(mypw($upgrade_factor, $playerinfo[engines]))+round(mypw($upgrade_factor, $playerinfo[power]))+round(mypw($upgrade_factor, $playerinfo[computer]))+round(mypw($upgrade_factor, $playerinfo[sensors]))+round(mypw($upgrade_factor, $playerinfo[beams]))+round(mypw($upgrade_factor, $playerinfo[torp_launchers]))+round(mypw($upgrade_factor, $playerinfo[shields]))+round(mypw($upgrade_factor, $playerinfo[armor]))+round(mypw($upgrade_factor, $playerinfo[cloak])));
					$ship_salvage_rate=rand(10,20);
					$ship_salvage=$ship_value*$ship_salvage_rate/100+$salv_credits;  //added credits for xenobe - 0 if normal player - GunSlinger
			
					$l_att_salv=str_replace("[salv_ore]",$salv_ore,$l_att_salv);
					$l_att_salv=str_replace("[salv_organics]",$salv_organics,$l_att_salv);
					$l_att_salv=str_replace("[salv_goods]",$salv_goods,$l_att_salv);
					$l_att_salv=str_replace("[ship_salvage_rate]",$ship_salvage_rate,$l_att_salv);
					$l_att_salv=str_replace("[ship_salvage]",$ship_salvage,$l_att_salv);
					$l_att_salv=str_replace("[name]",$targetinfo[character_name],$l_att_salv);
			
					echo "$l_att_salv<BR>";
					$update6 = $db->Execute ("UPDATE $dbtables[ships] SET credits=credits+$ship_salvage, ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods WHERE ship_id=$targetinfo[ship_id]");
					$armor_lost=$targetinfo[armor_pts]-$targetarmor;
					$fighters_lost=$targetinfo[ship_fighters]-$targetfighters;
					$energy=$targetinfo[ship_energy];
					$update6b = $db->Execute ("UPDATE $dbtables[ships] SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armor_pts=armor_pts-$armor_lost, torps=torps-$targettorpnum WHERE ship_id=$targetinfo[ship_id]");
				}
			}

			echo "  </div>\n";

			echo "  <div style='height:1px;'></div>\n";
			echo "  <div style='text-align:right; font-size:10px; padding:4px; background-color:{$color_header}; border:#FFCC00 1px solid;'>Layout created by TheMightyDude</div>\n";

			echo "</div>\n";
		}
	}
}
$db->Execute("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

$_SESSION['in_combat'] = (boolean) false;

TEXT_GOTOMAIN();

include("footer.php");

?>