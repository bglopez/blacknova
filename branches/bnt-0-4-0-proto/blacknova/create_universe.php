<?

include("config.php");
include("includes/schema.php");
updatecookie();

$title="Create Universe";
include("header.php");

connectdb();

bigtitle();

if($swordfish != $adminpass)
{
  echo "<form action=create_universe.php method=post>";
  echo "Password: <input type=password name=swordfish size=20 maxlength=20><BR><BR>";
  echo "<input type=submit value=Submit><input type=reset value=Reset>";
  echo "</form>";
}
elseif($swordfish == $adminpass && $engage == "")
{
  echo "Max sectors set to $sector_max in config.php<BR><BR>";
  echo "<form action=create_universe.php method=post>";
  echo "<table>";
  echo "<tr><td><b><u>Base/Planet Setup</u></b></td><td></td></tr>";
  echo "<tr><td>Percent Special</td><td><input type=text name=special size=2 maxlength=2 value=1></td></tr>";
  echo "<tr><td>Percent Ore</td><td><input type=text name=ore size=2 maxlength=2 value=20></td></tr>";
  echo "<tr><td>Percent Organics</td><td><input type=text name=organics size=2 maxlength=2 value=20></td></tr>";
  echo "<tr><td>Percent Goods</td><td><input type=text name=goods size=2 maxlength=2 value=20></td></tr>";
  echo "<tr><td>Percent Energy</td><td><input type=text name=energy size=2 maxlength=2 value=20></td></tr>";
  echo "<tr><td>Percent Empty</td><td>Equal to 100 - total of above.</td></tr>";
  echo "<tr><td>Initial Commodities to Sell<br><td><input type=text name=initscommod size=5 maxlength=5 value=100.00> % of max</td></tr>";
  echo "<tr><td>Initial Commodities to Buy<br><td><input type=text name=initbcommod size=5 maxlength=5 value=100.00> % of max</td></tr>";
  echo "<tr><td><b><u>Sector/Link Setup</u></b></td><td></td></tr>";
  $fedsecs = intval($sector_max / 300); 
  $loops = intval($sector_max / 300);
  echo "<TR><TD>Number of Federation sectors</TD><TD><INPUT TYPE=TEXT NAME=fedsecs SIZE=4 MAXLENGTH=4 VALUE=$fedsecs></TD></TR>";
  echo "<tr><td>Number of loops</td><td><input type=text name=loops size=2 maxlength=2 value=$loops></td></tr>";
  echo "<tr><td>Percent of sectors with unowned planets</td><td><input type=text name=planets size=2 maxlength=2 value=10></td></tr>";
  echo "<tr><td></td><td><input type=hidden name=engage value=1><input type=hidden name=swordfish value=$swordfish><input type=submit value=Submit><input type=reset value=Reset></td></tr>";
  echo "</table>";
  echo "</form>";
}
elseif($swordfish == $adminpass && $engage == "1")
{
  if($fedsecs > $sector_max)
  {
    echo "The number of Federation sectors must be smaller than the size of the universe!";
    include("footer.php");
    die();
  }
  echo "So you would like your $sector_max sector universe to have:<BR><BR>";
  $spp = round($sector_max*$special/100);
  echo "$spp special ports<BR>";
  $oep = round($sector_max*$ore/100);
  echo "$oep ore ports<BR>";
  $ogp = round($sector_max*$organics/100);
  echo "$ogp organics ports<BR>";
  $gop = round($sector_max*$goods/100);
  echo "$gop goods ports<BR>";
  $enp = round($sector_max*$energy/100);
  echo "$enp energy ports<BR>";
  echo "$initscommod% initial commodities to sell<BR>";
  echo "$initbcommod% initial commodities to buy<BR>";
  $empty = $sector_max-$spp-$oep-$ogp-$gop-$enp;
  echo "$empty empty sectors<BR>";
  echo "$fedsecs Federation sectors<BR>";
  echo "$loops loops<BR>";
  $nump = round ($sector_max*$planets/100);
  echo "$nump unowned planets<BR><BR>";
  echo "If this is correct, click confirm - otherwise go back.<BR>";
  echo "<form action=create_universe.php method=post>";
  echo "<input type=hidden name=spp value=$spp>";
  echo "<input type=hidden name=oep value=$oep>";
  echo "<input type=hidden name=ogp value=$ogp>";
  echo "<input type=hidden name=gop value=$gop>";
  echo "<input type=hidden name=enp value=$enp>";
  echo "<input type=hidden name=initscommod value=$initscommod>";
  echo "<input type=hidden name=initbcommod value=$initbcommod>";
  echo "<input type=hidden name=nump value=$nump>";
  echo "<INPUT TYPE=HIDDEN NAME=fedsecs VALUE=$fedsecs>";
  echo "<input type=hidden name=loops value=$loops>";
  echo "<input type=hidden name=engage value=2><input type=hidden name=swordfish value=$swordfish>";
  echo "<input type=submit value=Confirm>";
  echo "</form>";
  echo "<BR><BR><FONT COLOR=RED>WARNING: ALL TABLES WILL BE DROPPED AND THE GAME WILL BE RESET WHEN YOU CLICK 'CONFIRM' !</FONT>";
}
elseif($swordfish==$adminpass && $engage=="2")
{
  //Create the database
  create_schema();

  echo "Creating sector 0 - Sol...<BR>";
  $initsore = $ore_limit * $initscommod / 100.0;
  $initsorganics = $organics_limit * $initscommod / 100.0;
  $initsgoods = $goods_limit * $initscommod / 100.0;
  $initsenergy = $energy_limit * $initscommod / 100.0;
  $initbore = $ore_limit * $initbcommod / 100.0;
  $initborganics = $organics_limit * $initbcommod / 100.0;
  $initbgoods = $goods_limit * $initbcommod / 100.0;
  $initbenergy = $energy_limit * $initbcommod / 100.0;
  $insert = $db->Execute("INSERT INTO $dbtables[universe] (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES ('0', 'Sol', '1', 'special', '0', '0', '0', '0', 'Sol: Hub of the Universe', '0', '0', '0')");
  $update = $db->Execute("UPDATE $dbtables[universe] SET sector_id=0 WHERE sector_id=1");

  echo "Creating sector 1 - Alpha Centauri...<BR>";
  $insert = $db->Execute("INSERT INTO $dbtables[universe] (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES ('1', 'Alpha Centauri', '1', 'energy',  '0', '0', '0', '0', 'Alpha Centauri: Gateway to the Galaxy', '0', '0', '1')");
  $remaining = $sector_max-1;
  srand((double)microtime()*1000000);
  echo "Creating remaining $remaining sectors...";
  for($i=1; $i<=$remaining; $i++)
  {
    $distance=rand(1,$universe_size);
    $angle1=rand(0,180);
    $angle2=rand(0,90);
    $insert = $db->Execute("INSERT INTO $dbtables[universe] (sector_id,zone_id,angle1,angle2,distance) VALUES ('','1',$angle1,$angle2,$distance)");
  }
  echo "<BR>Selecting $fedsecs Federation sectors...<BR>";
  $replace = $db->Execute("REPLACE INTO $dbtables[zones] (zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('1', 'Unchartered space', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', '0' )");
  $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('2', 'Federation space', 0, 'N', 'N', 'N', 'N', 'N', 'N',  'Y', 'N', '$fed_max_hull')");
  $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('3', 'Free-Trade space', 0, 'N', 'N', 'Y', 'N', 'N', 'N','Y', 'N', '0')");
  $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('4', 'War Zone', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y','N', 'Y', '0')");
  $update = $db->Execute("UPDATE $dbtables[universe] SET zone_id='2' WHERE sector_id<$fedsecs");
  echo "Selecting $spp sectors for additional special ports...<BR>";
  for($i=2; $i<=$sector_max; $i++)
  {
    $num = rand(2, $sector_max - 1);
    $sectors[$i] = $num;
  }
//  $sectors=range(2,$sector_max);
//  shuffle($sectors);
  for($i=2; $i<$spp; $i++)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id='3',port_type='special' WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $oep sectors for ore ports...<BR>";
  $last = $spp;
  for($i=$last; $i<$last+$oep; $i++)
  {
    $db->Execute("UPDATE $dbtables[universe] SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $ogp sectors for organics ports...<BR>";
  $last = $last + $oep;
  for($i=$last; $i<$last+$ogp; $i++)
  {
    $db->Execute("UPDATE $dbtables[universe] SET port_type='organics',port_ore=$initbore,port_organics=$initsorganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $gop sectors for goods ports...<BR>";
  $last = $last + $gop;
  for($i=$last; $i<$last+$gop; $i++)
  {
    $db->Execute("UPDATE $dbtables[universe] SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $enp sectors for energy ports...<BR>";
  $last = $last + $gop;
  for($i=$last; $i<$last+$enp; $i++)
  {
    $db->Execute("UPDATE $dbtables[universe] SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initsenergy WHERE sector_id=$sectors[$i]");
    echo "$sectors[$i] - ";
  }
  echo "done<BR>";
  echo "Selecting $nump sectors for unowned planets...<BR>";
  for($i=0; $i<=$sector_max; $i++)
  {
    $num = rand(0, $sector_max - 1);
    $sectors[$i] = $num;
  }
//  $sectors=range(0,$sector_max);
//  shuffle($sectors);
  
  $grid = "";

  for($i=0; $i< $base_xsize * $base_ysize; $i++)
    $grid.= "0 ";
  
  $planet_id = 1;
  for($i=0; $i<$nump; $i++)
  {
    $select = $db->Execute("SELECT $dbtables[universe].sector_id FROM $dbtables[universe], $dbtables[zones] WHERE $dbtables[universe].sector_id=$sectors[$i] AND $dbtables[zones].zone_id=$dbtables[universe].zone_id AND $dbtables[zones].allow_planet='N'") or die("DB error");
    if($select->RecordCount() == 0)
    {
        $insert = $db->Execute("INSERT INTO $dbtables[planets] (colonists, owner, corp, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, sector_id) VALUES (2,0,0,$default_prod_ore,$default_prod_organics,$default_prod_goods,$default_prod_energy, $default_prod_fighters, $default_prod_torp,$sectors[$i])");
        $insert = $db->Execute("INSERT INTO $dbtables[bases] (planet_id, grid) VALUES($planet_id, '$grid')");
        echo "$sectors[$i] - ";
        $planet_id++;
    }
    else
      echo "<BR>Planet skipped in sector $sectors[$i]<BR>";
  }
  echo "done<BR>";
  $loopsize = round($sector_max/$loops);
  $start = 0;
  $finish = $loopsize - 1;
  for($i=1; $i<=$loops; $i++)
  {
    echo "Creating loop $i of $loopsize sectors - from sector $start to $finish...<BR>";
    for($j=$start; $j<$finish; $j++)
    {
      $k = $j + 1;
      $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($j,$k)");
      $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($k,$j)");
      echo "$j<=>$k - ";
    }
    $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($start,$finish)");
    $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($finish,$start)");
    echo "$finish<=>$start";
    echo "done loop $i<BR>";
    $start=$finish+1;
    $finish=$finish+$loopsize;
    if ($finish>$sector_max) $finish=$sector_max;

  }
  echo "Randomly One-way Linking Sectors...<BR>";
  for($i=0; $i<=$sector_max; $i++)
  {
    $num = rand(0, $sector_max - 1);
    $sectors[$i] = $num;
  }
  //$sectors=range(0,$sector_max);
  //shuffle($sectors);
  for($i=0; $i<=$sector_max; $i++)
  {
    $dups = $db->Execute("SELECT * from $dbtables[links] where link_start = $i and link_dest = $sectors[$i]");
    if($dups->RecordCount() == 0) 
    {
       $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($i,$sectors[$i])");
       echo "$i=>$sectors[$i] - ";
    }
  }
  echo "done.<BR>";
  echo "Randomly Two-way Linking Sectors...<BR>";
  for($i=0; $i<=$sector_max; $i++)
  {
    $num = rand(0, $sector_max - 1);
    $sectors[$i] = $num;
  }
  //$sectors=range(0,$sector_max);
  //shuffle($sectors);
  for($i=0; $i<=$sector_max; $i++)
  {
    $dups = $db->Execute("SELECT * from $dbtables[links] where (link_start = $i and link_dest= $sectors[$i]) or (link_start = $sectors[$i] and link_dest = $i)");
    if($dups->RecordCount() == 0)
    {
       $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($i,$sectors[$i])");
       $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($sectors[$i],$i)");
    }
    echo "$i<=>$sectors[$i] - ";
  }
  echo "done.<BR>";
  
  $update = $db->Execute("INSERT INTO $dbtables[ibank_accounts] (ship_id,balance,loan) VALUES (1,0,0)");
  
  $password = substr($admin_mail, 0, $maxlen_password);
  echo "Creating default $admin_mail login, password: $password<BR>";
  $stamp=date("Y-m-d H:i:s");
  $db->Execute("INSERT INTO $dbtables[ships] VALUES('','WebMaster','N','WebMaster','$password','$admin_mail',0,0,0,0,0,0,0,0,0,0,$start_armour,0,$start_credits,0,0,0,0,$start_energy,0,$start_fighters,$start_turns,'','N',0,1,0,0,'N','N',0,0, '$stamp',0,0,0,0,'N','1.1.1.1',0,0,0,0,'Y','N','N','Y',' ','$default_lang', 'Y')");
  $db->Execute("INSERT INTO $dbtables[zones] VALUES('','WebMaster\'s Territory', 1, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
  
  echo "<p>Configuring game scheduler...<br>Ticks happen every $sched_ticks minutes<p>";

  echo "Turns will happen every $sched_turns minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_turns.php', '')");

  echo "Defenses will be checked every $sched_turns minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_defenses.php', '')");

  echo "Furangees will play every $sched_turns minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_furangee.php', '')");

  echo "Interests on IGB accounts will be accumulated every $sched_IGB minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_IGB, 0, 'sched_IGB.php', '')");

  echo "News will be generated every $sched_news minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_news, 0, 'sched_news.php', '')");

  echo "Planets will generate production every $sched_planets minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_planets, 0, 'sched_planets.php', '')");

  echo "Ports will regenerate every $sched_ports minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_ports, 0, 'sched_ports.php', '')");

  echo "Ships will be towed from fed sectors every $sched_turns minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_tow.php', '')");

  echo "Rankings will be generated every $sched_ranking minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_ranking, 0, 'sched_ranking.php', '')");

  echo "Sector Defences will degrade every $sched_degrade minutes.<br>";
  $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_degrade, 0, 'sched_degrade.php', '')");


  echo "done.<BR>";
}
else
{
  echo "Huh?";
}

echo "<BR><BR>Click <A HREF=login.php>here</A> to return to the login screen.";
  
include("footer.php");

?> 
