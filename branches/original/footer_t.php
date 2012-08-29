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
// File: footer.php

global $sched_ticks, $footer_show_time, $footer_show_debug, $no_db;

// New database driven language entries
load_languages($db, $lang, array('footer','global_includes'), $langvars);

$online = (integer) 0;

if (!$no_db)
{
    $res = $db->Execute("SELECT COUNT(*) AS loggedin FROM {$db->prefix}ships WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP({$db->prefix}ships.last_login)) / 60 <= 5 AND email NOT LIKE '%@xenobe';");
    db_op_result ($db, $res, __LINE__, __FILE__);
    if ($res instanceof ADORecordSet)
    {
        $row = $res->fields;
        $online = $row['loggedin'];
    }
}

global $BenchmarkTimer;
if (is_object ($BenchmarkTimer) )
{
    $stoptime = $BenchmarkTimer->stop();
    $elapsed = $BenchmarkTimer->elapsed();
    $elapsed = substr ($elapsed, 0, 5);
}
else
{
    $elapsed = 999;
}

// Suppress the news ticker on the IGB and index pages
$no_ticker = (!(preg_match("/index.php/i", $_SERVER['PHP_SELF']) || preg_match("/igb.php/i", $_SERVER['PHP_SELF'])));

// Update counter
$mySEC = (integer) 0;

if (!$no_db)
{
    $res = $db->Execute("SELECT last_run FROM {$db->prefix}scheduler LIMIT 1");
    db_op_result ($db, $res, __LINE__, __FILE__);
    if ($res instanceof ADORecordSet)
    {
        $result = $res->fields;
        $mySEC = ($sched_ticks * 60) - (TIME () - $result['last_run']);
    }
}
// End update counter

if ($footer_show_time == true) // Make the SF logo a little bit larger to balance the extra line from the benchmark for page generation
{
    $sf_logo_type = '14';
    $sf_logo_width = "150";
    $sf_logo_height = "40";
}
else
{
    $sf_logo_type = '11';
    $sf_logo_width = "120";
    $sf_logo_height = "30";
}

if (!$no_ticker)
{
    $sf_logo_type++; // Make the SF logo darker for all pages except login. No need to change the sizes as 12 is the same size as 11 and 15 is the same size as 14.
}

if (!isset($_GET['lang']))
{
    $sf_logo_link = '';
}
else
{
    $sf_logo_link = "?lang=" . $_GET['lang'];
}

$elapsed = number_format ($elapsed, 2);
$mem_peak_usage = floor (memory_get_peak_usage() / 1024);

// Set array with all used variables in page
$variables['players_online'] = $online;
$variables['no_ticker'] = $no_ticker;
$variables['mySEC'] = $mySEC;
$variables['sched_ticks'] = $sched_ticks;
$variables['sf_logo_type'] = $sf_logo_type;
$variables['sf_logo_height'] = $sf_logo_height;
$variables['sf_logo_width'] = $sf_logo_width;
$variables['sf_logo_link'] = $sf_logo_link;
$variables['elapsed'] = $elapsed;
$variables['mem_peak_usage'] = $mem_peak_usage;
$variables['footer_show_debug'] = $footer_show_debug;
?>