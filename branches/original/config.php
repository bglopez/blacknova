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
// File: config.php

// All sched_* vars are in minutes.
// These are true minutes, no matter to what interval you're running the scheduler script!
// The scheduler will auto-adjust, possibly running many of the same events in a single call.
$sched_ticks                = 1;                    // Set this to how often (in minutes) you are running the scheduler script.
$turns_per_tick             = 6;                    // Update how many turns per tick
$sched_turns                = 2;                    // New turns rate (also includes towing, xenobe)
$sched_ports                = 1;                    // How often port production occurs
$sched_planets              = 2;                    // How often planet production occurs
$sched_igb                  = 2;                    // How often IGB interests are added
$sched_ranking              = 30;                   // How often rankings will be generated
$sched_news                 = 15;                   // How often news are generated
$sched_degrade              = 6;                    // How often sector fighters degrade when unsupported by a planet
$sched_apocalypse           = 15;
$sched_thegovernor          = 1;
$doomsday_value             = 90000000;             // Number of colonists a planet needs before being affected by the apocalypse
$color_header               = "#500050";            // GUI colors - soon to be moved into templates
$color_line1                = "#300030";            // GUI colors - soon to be moved into templates
$color_line2                = "#400040";            // GUI colors - soon to be moved into templates
$mine_hullsize              = 8;                    // Minimum size hull has to be to hit mines
$ewd_maxhullsize            = 15;                   // Max hull size before EWD degrades
$sector_max                 = 1000;
$link_max                   = 10;
$universe_size              = 200;
$game_name                  = "Default Game Name";  // Please set this to a unique name for your game
$release_version            = "0.65";               // Please do not change this. Doing so will cause problems for the server lists, and setupinfo, and more.
$fed_max_hull               = 8;
$max_ranks                  = 100;
$rating_combat_factor       = .8;                   // Amount of rating gained from combat
$server_closed              = false;                // True = block logins but not new account creation
$account_creation_closed    = false;                // True = block new account creation
$newbie_nice                = "YES";
$newbie_extra_nice          = "YES";
$newbie_hull                = "8";
$newbie_engines             = "8";
$newbie_power               = "8";
$newbie_computer            = "8";
$newbie_sensors             = "8";
$newbie_armor               = "8";
$newbie_shields             = "8";
$newbie_beams               = "8";
$newbie_torp_launchers      = "8";
$newbie_cloak               = "8";
$allow_fullscan             = true;                 // Full long range scan
$allow_navcomp              = true;                 // Navigation computer
$allow_ibank                = true;                 // Intergalactic Bank (IGB)
$allow_genesis_destroy      = false;                // Genesis torps can destroy planets
$ibank_interest             = 0.0003;               // Interest rate for account funds - Note that this is calculated every system update!
$ibank_paymentfee           = 0.05;                 // Paymentfee
$ibank_loaninterest         = 0.0010;               // Loan interest (good idea to put double what you get on a planet)
$ibank_loanfactor           = 0.10;                 // One-time loan fee
$ibank_loanlimit            = 0.25;                 // Maximum loan allowed, percent of net worth
$default_prod_ore           = 20.0;                 // Default planet ore production percentage
$default_prod_organics      = 20.0;                 // Default planet organics production percentage
$default_prod_goods         = 20.0;                 // Default planet goods production percentage
$default_prod_energy        = 20.0;                 // Default planet energy production percentage
$default_prod_fighters      = 10.0;                 // Default planet fighters production percentage
$default_prod_torp          = 10.0;                 // Default planet torpedo production percentage
$ore_price                  = 11;
$ore_delta                  = 5;
$ore_rate                   = 75000;
$ore_prate                  = 0.25;
$ore_limit                  = 100000000;
$organics_price             = 5;
$organics_delta             = 2;
$organics_rate              = 5000;
$organics_prate             = 0.5;
$organics_limit             = 100000000;
$goods_price                = 15;
$goods_delta                = 7;
$goods_rate                 = 75000;
$goods_prate                = 0.25;
$goods_limit                = 100000000;
$energy_price               = 3;
$energy_delta               = 1;
$energy_rate                = 75000;
$energy_prate               = 0.5;
$energy_limit               = 1000000000;
$inventory_factor           = 1;
$upgrade_cost               = 1000;
$upgrade_factor             = 2;
$level_factor               = 1.5;
$dev_genesis_price          = 1000000;
$dev_beacon_price           = 100;
$dev_emerwarp_price         = 1000000;
$dev_warpedit_price         = 100000;
$dev_minedeflector_price    = 10;
$dev_escapepod_price        = 100000;
$dev_fuelscoop_price        = 100000;
$dev_lssd_price             = 10000000;
$fighter_price              = 50;
$fighter_prate              = .01;
$torpedo_price              = 25;
$torpedo_prate              = .025;
$torp_dmg_rate              = 10;
$credits_prate              = 3.0;
$armor_price                = 5;
$basedefense                = 1;                    // Additional factor added to tech levels by having a base on your planet. All your base are belong to us.
$colonist_price             = 5;
$colonist_production_rate   = .005;
$colonist_reproduction_rate = 0.0005;
$colonist_limit             = 100000000;
$organics_consumption       = 0.05;
$starvation_death_rate      = 0.01;
$interest_rate              = 1.0005;
$base_ore                   = 10000;
$base_goods                 = 10000;
$base_organics              = 10000;
$base_credits               = 10000000;
$base_modifier              = 1;
$start_fighters             = 10;
$start_armor                = 10;
$start_credits              = 1000;
$start_energy               = 100;
$start_turns                = 1200;
$max_turns                  = 2500;
$max_emerwarp               = 10;
$fullscan_cost              = 1;
$scan_error_factor          = 20;
$max_planets_sector         = 5;
$max_traderoutes_player     = 40;
$min_bases_to_own           = 3;
$default_lang               = 'english';
$IGB_min_turns              = 1200;                 // Turns a player has to play before ship transfers are allowed 0=disable
$IGB_svalue                 = 0.15;                 // Max amount of sender's value allowed for ship transfers 0=disable
$IGB_trate                  = 1440;                 // Time (in minutes) before two similar transfers are allowed for ship transfers.0=disable
$IGB_lrate                  = 1440;                 // Time (in minutes) players have to repay a loan
$IGB_tconsolidate           = 10;                   // Cost in turns for consolidate : 1/$IGB_consolidate
$corp_planet_transfers      = 0;                    // If transferring credits to/from corp planets is allowed. 1=enable
$min_value_capture          = 0;                    // Percantage of planet's value a ship must be worth to be able to capture it. 0=disable
$defence_degrade_rate       = 0.05;
$energy_per_fighter         = 0.10;
$bounty_maxvalue            = 0.15;                 // Max amount a player can place as bounty - good idea to make it the same as $IGB_svalue. 0=disable
$bounty_ratio               = 0.75;                 // Ratio of players networth before attacking results in a bounty. 0=disable
$bounty_minturns            = 500;                  // Minimum number of turns a target must have had before attacking them may not get you a bounty. 0=disable
$display_password           = false;                // If true, will display password on signup screen.
$space_plague_kills         = 0.20;                 // Percentage of colonists killed by space plague
$max_credits_without_base   = 10000000;             // Max amount of credits allowed on a planet without a base
$sofa_on                    = false;
$ksm_allowed                = true;
$xenobe_max                 = 10;                   // Sets the number of xenobe in the universe
$xen_start_credits          = 1000000;              // What Xenobe start with
$xen_unemployment           = 100000;               // Amount of credits each xenobe receive on each xenobe tick
$xen_aggression             = 100;                  // Percent of xenobe that are aggressive or hostile
$xen_planets                = 5;                    // Percent of created xenobe that will own planets. Recommended to keep at small percentage
$xenstartsize               = 15;                   // Max starting size of Xenobes at universe creation
$port_regenrate             = 10;
$footer_style               = 'old';                // Switch between old style footer and new style. Old is text, and only the time until next update. New is a table, and also includes server time.
$footer_show_debug          = true;
$sched_planet_valid_credits = true;                 // Limit captured planets Max Credits to max_credits_without_base
$max_upgrades_devices       = 55;                   // Must stay at 55 due to PHP/MySQL cap limit.
$max_emerwarp               = 10;
$max_genesis                = 10;
$max_beacons                = 10;
$max_warpedit               = 10;
$bounty_all_special         = true;                 // Stop access on all Special Ports when you have a federation bounty on you.
$bnt_ls                     = false;
$local_number_dec_point     = ".";                  // Localization (regional) settings - soon to be moved into languages
$local_number_thousands_sep = ",";                  // Localization (regional) settings - soon to be moved into languages
$language                   = "english";            // Localization (regional) settings - soon to be moved into languages

require "global_includes.php"; // A central location for including/requiring other files - Note that we use require because the game cannot function without it.
?>