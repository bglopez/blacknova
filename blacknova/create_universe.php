<script language="php">
include("config.php");
include("languages/$lang");

/*
##############################################################################
# Create Universe Script                                                     #
#                                                                            #
# ChangeLog                                                                  #
#  Nov 2, 01 - Wandrer - Rewritten mostly from scratch                       #
##############################################################################
*/

/*
##############################################################################
# Define Functions for this script                                           #
##############################################################################
*/

### Description: Create Benchmark Class

class c_Timer {
   var $t_start = 0;
   var $t_stop = 0;
   var $t_elapsed = 0;

   function start() { $this->t_start = microtime(); }

   function stop()  { $this->t_stop  = microtime(); }

   function elapsed() {
      $start_u = substr($this->t_start,0,10); $start_s = substr($this->t_start,11,10);
      $stop_u  = substr($this->t_stop,0,10);  $stop_s  = substr($this->t_stop,11,10);
      $start_total = doubleval($start_u) + $start_s;
      $stop_total  = doubleval($stop_u) + $stop_s;
      $this->t_elapsed = $stop_total - $start_total;
      return $this->t_elapsed;
}
}

function PrintFlush($Text="") {
print "$Text";
flush();
}

### End defining functions.

### Start Timer
$BenchmarkTimer = new c_Timer;
$BenchmarkTimer->start();

### Set timelimit and randomize timer.

set_time_limit(0);
srand((double)microtime()*1000000);

### Include config files and db scheme.

include("includes/schema.php");

### Update cookie.
updatecookie();

$title="Create Universe";
include("header.php");

### Connect to the database.

connectdb();

### Print Title on Page.

bigtitle();

### Manually set step var if info isn't correct.

if($swordfish != $adminpass) {
$step="0";
}

if($swordfish == $adminpass && $engage == "") {
$step="1";
}

if($swordfish == $adminpass && $engage == "1") {
$step="2";
}

### Main switch statement.

switch ($step) {
   case "1":
      echo "<form action=create_universe.php method=post>";
      echo "<table>";
      echo "<tr><td><b><u>Base/Planet Setup</u></b></td><td></td></tr>";
      echo "<tr><td>Percent Special</td><td><input type=text name=special size=5 maxlength=5 value=1></td></tr>";
      echo "<tr><td>Percent Ore</td><td><input type=text name=ore size=5 maxlength=5 value=15></td></tr>";
      echo "<tr><td>Percent Organics</td><td><input type=text name=organics size=5 maxlength=5 value=10></td></tr>";
      echo "<tr><td>Percent Goods</td><td><input type=text name=goods size=5 maxlength=5 value=15></td></tr>";
      echo "<tr><td>Percent Energy</td><td><input type=text name=energy size=5 maxlength=5 value=10></td></tr>";
      echo "<tr><td>Percent Empty</td><td>Equal to 100 - total of above.</td></tr>";
      echo "<tr><td>Initial Commodities to Sell<br><td><input type=text name=initscommod size=6 maxlength=6 value=100.00> % of max</td></tr>";
      echo "<tr><td>Initial Commodities to Buy<br><td><input type=text name=initbcommod size=6 maxlength=6 value=100.00> % of max</td></tr>";
      echo "<tr><td><b><u>Sector/Link Setup</u></b></td><td></td></tr>";
      $fedsecs = intval($sector_max / 200);
      $loops = intval($sector_max / 500);
      echo "<tr><td>Number of sectors total (<b>overrides config.php</b>)</td><td><input type=text name=sektors size=5 maxlength=5 value=$sector_max></td></tr>";
      echo "<TR><TD>Number of Federation sectors</TD><TD><INPUT TYPE=TEXT NAME=fedsecs SIZE=6 MAXLENGTH=6 VALUE=$fedsecs></TD></TR>";
      echo "<tr><td>Number of loops</td><td><input type=text name=loops size=6 maxlength=6 value=$loops></td></tr>";
      echo "<tr><td>Percent of sectors with unowned planets</td><td><input type=text name=planets size=5 maxlength=5 value=10></td></tr>";
      echo "<tr><td></td><td><input type=hidden name=engage value=1><input type=hidden name=step value=2><input type=hidden name=swordfish value=$swordfish><input type=submit value=Submit><input type=reset value=Reset></td></tr>";
      echo "</table>";
      echo "</form>";
      break;
   case "2":
      $sector_max = round($sektors);
      if($fedsecs > $sector_max) {
         echo "The number of Federation sectors must be smaller than the size of the universe!";
         break;
      }
      $spp = round($sector_max*$special/100);
      $oep = round($sector_max*$ore/100);
      $ogp = round($sector_max*$organics/100);
      $gop = round($sector_max*$goods/100);
      $enp = round($sector_max*$energy/100);
      $empty = $sector_max-$spp-$oep-$ogp-$gop-$enp;
      $nump = round ($sector_max*$planets/100);
      echo "So you would like your $sector_max sector universe to have:<BR><BR>";
      echo "$spp special ports<BR>";
      echo "$oep ore ports<BR>";
      echo "$ogp organics ports<BR>";
      echo "$gop goods ports<BR>";
      echo "$enp energy ports<BR>";
      echo "$initscommod% initial commodities to sell<BR>";
      echo "$initbcommod% initial commodities to buy<BR>";
      echo "$empty empty sectors<BR>";
      echo "$fedsecs Federation sectors<BR>";
      echo "$loops loops<BR>";
      echo "$nump unowned planets<BR><BR>";
      echo "If this is correct, click confirm - otherwise go back.<BR>";
      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=3>";
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
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<input type=submit value=Confirm>";
      echo "</form>";
      echo "<BR><BR><FONT COLOR=RED>";
      echo "WARNING: ALL TABLES WILL BE DROPPED AND THE GAME WILL BE RESET WHEN YOU CLICK 'CONFIRM'!</FONT>";
      break;
   case "3":
      create_schema();
      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=4>";
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
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<input type=submit value=Confirm>";
      echo "</form>";
      break;
   case "4":
      print("Creating sector 0 - Sol ");
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;
      $insert = $db->Execute("INSERT INTO $dbtables[universe] (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES ('0', 'Sol', '1', 'special', '0', '0', '0', '0', 'Sol: Hub of the Universe', '0', '0', '0')");
      PrintFlush($db->ErrorMsg());
      $update = $db->Execute("UPDATE $dbtables[universe] SET sector_id=0 WHERE sector_id=1");
      print("");
      PrintFlush("- completed successfully.<BR>");
      print("Creating sector 1 - Alpha Centauri ");
      $insert = $db->Execute("INSERT INTO $dbtables[universe] (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES ('1', 'Alpha Centauri', '1', 'energy',  '0', '0', '0', '0', 'Alpha Centauri: Gateway to the Galaxy', '0', '0', '1')");
      PrintFlush($db->ErrorMsg());
      print("");
      PrintFlush("- completed successfully.<BR>");
      $remaining = $sector_max-1;
      print("Creating remaining $remaining sectors ");
      ### Cycle through remaining sectors
      ### We are going to split the amount into groups of 500 and bulk pass all the info to mysql to
      ### figure out.
      $i=0;
      while ($i < ($remaining-500)):
         $insert="INSERT INTO $dbtables[universe] (sector_id,zone_id,angle1,angle2,distance) VALUES ";
         for ($j=1; $j<=499; $j++) {
            $distance=intval(rand(1,$universe_size));
            $angle1=rand(0,180);
            $angle2=rand(0,90);
            $insert.="('','1',$angle1,$angle2,$distance),";
         }
         $insert.="('','1',$angle1,$angle2,$distance);";
         $i=$i+500;
         ### Now lets post the information to the mysql database.
         $db->Execute("$insert");
         PrintFlush($db->ErrorMsg());
      endwhile;
      ### Now lets do the remaining sectors.
      $insert="INSERT INTO $dbtables[universe] (sector_id,zone_id,angle1,angle2,distance) VALUES ";
      for ($j=$i; $j<=$remaining-1; $j++) {
         $distance=intval(rand(1,$universe_size));
         $angle1=rand(0,180);
         $angle2=rand(0,90);
          $insert.="('','1',$angle1,$angle2,$distance),";
      }
      $insert.="('','1',$angle1,$angle2,$distance);";
      $j=$j+1;
      ### Now lets post the information to the mysql database.
      $db->Execute("$insert");
      $i=$j;
      print("");
      PrintFlush("- completed successfully.<br>");
      print("Selecting $fedsecs Federation sectors ");
      $replace = $db->Execute("REPLACE INTO $dbtables[zones] (zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('1', 'Unchartered space', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', '0' )");
      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('2', 'Federation space', 0, 'N', 'N', 'N', 'N', 'N', 'N',  'Y', 'N', '$fed_max_hull')");
      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('3', 'Free-Trade space', 0, 'N', 'N', 'Y', 'N', 'N', 'N','Y', 'N', '0')");
      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('4', 'War Zone', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y','N', 'Y', '0')");
      $update = $db->Execute("UPDATE $dbtables[universe] SET zone_id='2' WHERE sector_id<$fedsecs");
      print("");
      PrintFlush("- completed successfully.<BR>");
      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Special Ports
      print("Selecting $spp sectors for additional special ports ");
      $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' order by rand(unix_timestamp()) desc limit $spp");
      $i=0;
      $j=0;
      $update="UPDATE $dbtables[universe] SET zone_id='3',port_type='special' WHERE ";
      if($sql_query)
      {
         while (!$sql_query->EOF) {
            $result = $sql_query->fields;
            if ($i>499) {
               $update.="sector_id=9999999;";
               $db->Execute("$update");
               $update="UPDATE universe SET zone_id='3',port_type='special' WHERE ";
               $i=0;
               $j++;
               PrintFlush(". ");
            } else {
               $update.="(port_type='none' and sector_id=$result[sector_id]) or ";
               $i++;
               $j++;
            }
            $sql_query->MoveNext();
         }
      }
      $update.="sector_id=9999999";
      $db->Execute("$update");
      print("");
      PrintFlush("- completed successfully.<BR>");  
      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Ore Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;
      print("Selecting $oep sectors for ore ports ");
      $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' and rand() order by rand() desc limit $oep");
      $update="UPDATE $dbtables[universe] SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
      $i=0;
      $j=0;
      if($sql_query)
      {
         while (!$sql_query->EOF) {
            $result = $sql_query->fields;
            if ($i>499) {
               $update.="sector_id=9999999;";
               $db->Execute($update);
               $update="UPDATE $dbtables[universe] SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
               $i=0;
               $j++;
            } else {
               $update.="(port_type='none' and sector_id=$result[sector_id]) or ";
               $i++;
              $j++;
            }
            $sql_query->Movenext();
         }
      }
      print("");
      PrintFlush("- completed successfully. <BR>");
      $update.="sector_id=9999999";
      $db->Execute($update);
      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Organic Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;
      print("Selecting $ogp sectors for organic ports ");
      $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' and rand() order by rand() desc limit $ogp");
      $update="UPDATE $dbtables[universe] SET port_type='organics',port_ore=$initbore,port_organics=$initsorganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
      $i=0;
      $j=0;
      if($sql_query)
      {
         while (!$sql_query->EOF) {
            $result = $sql_query->fields;
            if ($i>499) {
               $update.="sector_id=9999999;";
               $db->Execute($update);
               $update="UPDATE $dbtables[universe] SET port_type='organics',port_ore=$initbore,port_organics=$initsorganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
               $i=0;
               $j++;
            } else {
               $update.="(port_type='none' and sector_id=$result[sector_id]) or ";
               $i++;
               $j++;
            }
            $sql_query->Movenext();
         }
      }
      print("");
      PrintFlush("- completed successfully. <BR>");
      $update.="sector_id=9999999";
      $db->Execute($update);
      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Goods Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;
      print("Selecting $gop sectors for goods ports ");
      $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' and rand() order by rand() desc limit $gop");
      $update="UPDATE $dbtables[universe] SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
      $i=0;
      $j=0;
      if($sql_query)
      {
         while (!$sql_query->EOF) {
            $result = $sql_query->fields;
            if ($i>499) {
               $update.="sector_id=9999999;";
               $db->Execute($update);
               $update="UPDATE $dbtables[universe] SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
               $i=0;
               $j++;
            } else {
               $update.="(port_type='none' and sector_id=$result[sector_id]) or ";
               $i++;
               $j++;
            }
            $sql_query->Movenext();
         }
      }
      print("");
      PrintFlush("- completed successfully. <BR>");
      $update.="sector_id=9999999";
      $db->Execute($update);
      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Energy Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;
      print("Selecting $enp sectors for energy ports ");
      $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' and rand() order by rand() desc limit $enp");
      $update="UPDATE $dbtables[universe] SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
      $i=0;
      $j=0;
      if($sql_query)
      {
         while (!$sql_query->EOF) {
            $result = $sql_query->fields;
            if ($i>499) {
               $update.="sector_id=9999999;";
               $db->Execute($update);
               $update="UPDATE $dbtables[universe] SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
               $i=0;
               $j++;
            } else {
	       $update.="(port_type='none' and sector_id=$result[sector_id]) or ";
               $i++;
               $j++;
            }
            $sql_query->movenext();
         }
      }
      print("");
      PrintFlush("- completed successfully. <BR>");
      $update.="sector_id=9999999";
      $db->Execute($update);
      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=5>";
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
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<input type=submit value=Confirm>";
      echo "</form>";
      break;
   case "5":
      PrintFlush("Selecting $nump sectors to place unowned planets in.<BR>");
      for($i=0; $i<=$sector_max; $i++)
      {
         $num = rand(0, $sector_max - 1);
         $sectors[$i] = $num;
      }
      for($i=0; $i<$nump; $i++)
      {
         $select = $db->Execute("SELECT $dbtables[universe].sector_id FROM $dbtables[universe], $dbtables[zones] WHERE $dbtables[universe].sector_id=$sectors[$i] AND $dbtables[zones].zone_id=$dbtables[universe].zone_id AND $dbtables[zones].allow_planet='N'") or die("DB error");
         if($select->RecordCount() == 0)
         {
            $insert = $db->Execute("INSERT INTO $dbtables[planets] (colonists, owner, corp, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, sector_id) VALUES (2,0,0,$default_prod_ore,$default_prod_organics,$default_prod_goods,$default_prod_energy, $default_prod_fighters, $default_prod_torp,$sectors[$i])");
            #echo "$sectors[$i] - ";
         }
         else
            echo "The planet in sector $sectors[$i] was skipped<BR>";
      }
      echo "Unowned planet placement completed successfully.<BR>";
      $loopsize = round($sector_max/$loops);
      $start = 0;
      $finish = $loopsize - 1;
      for($i=1; $i<=$loops; $i++)
      {
         echo "<BR>Creating loop $i of $loopsize sectors (from sector $start to $finish) ";
         for($j=$start; $j<$finish; $j++)
         {
            $k = $j + 1;
            $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($j,$k)");
            $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($k,$j)");
            #echo "$j<=>$k - ";
         }
         $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($start,$finish)");
         $update = $db->Execute("INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ($finish,$start)");
         #echo "$finish<=>$start";
         echo "- loop $i completed successfully.";
         $start=$finish+1;
         $finish=$finish+$loopsize;
         if ($finish>$sector_max) $finish=$sector_max;

      }
      PrintFlush("<BR>Creating $i sector warp-loops (out of $nump sectors) - completed successfully.<BR>");
      PrintFlush("<BR>Randomly One-way Linking $i Sectors (out of $sector_max sectors) ");
      $i=0;
      while ($i < ($sector_max-500)):
         $insert="INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ";
         for ($j=1; $j<=499; $j++) {
            $link1=intval(rand(1,$sector_max));
            $link2=intval(rand(1,$sector_max));
            $insert.="($link1,$link2),";
         }
         $link1=intval(rand(1,$sector_max));
         $link2=intval(rand(1,$sector_max));
         $insert.="($link1,$link2);";
         $i=$i+500;
         ### Now lets post the information to the mysql database.
         $db->Execute($insert);
         PrintFlush($db->ErrorMsg());
         # PrintFlush("Finished linking $i sectors ( out of $sector_max sectors)...<br>");
      endwhile;
      ### Now lets do the remaining sectors.
      $insert="INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ";
      for ($j=$i; $j<=$sector_max-1; $j++) {
            $link1=intval(rand(1,$sector_max));
            $link2=intval(rand(1,$sector_max));
            $insert.="($link1,$link2),";
      }
      $link1=intval(rand(1,$sector_max));
      $link2=intval(rand(1,$sector_max));
      $insert.="($link1,$link2);";
      $j=$j+1;
      ### Now lets post the information to the mysql database.
      $db->Execute($insert);
      $i=$j;
      print("");
      PrintFlush("- completed successfully.");
      PrintFlush("<BR>Randomly Two-way Linking Sectors ");
      $i=0;
      while ($i < ($sector_max-500)):
         $insert="INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ";
         for ($j=1; $j<=499; $j++) {
            $link1=intval(rand(1,$sector_max));
            $link2=intval(rand(1,$sector_max));
            $insert.="($link1,$link2),";
            $insert.="($link2,$link1),";
         }
         $link1=intval(rand(1,$sector_max));
         $link2=intval(rand(1,$sector_max));
         $insert.="($link1,$link2),";
         $insert.="($link2,$link1);";
         $i=$i+500;
         ### Now lets post the information to the mysql database.
         $db->Execute($insert);
         PrintFlush($db->ErrorMsg());
         # PrintFlush("Finished linking $i sectors ( out of $sector_max sectors)...<br>");
      endwhile;
      ### Now lets do the remaining sectors.
      $insert="INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ";
      for ($j=$i; $j<=$sector_max-1; $j++) {
         $link1=intval(rand(1,$sector_max));
         $link2=intval(rand(1,$sector_max));
         $insert.="($link1,$link2),";
         $insert.="($link2,$link1),";
      }
      $link1=intval(rand(1,$sector_max));
      $link2=intval(rand(1,$sector_max));
      $insert.="($link1,$link2),";
      $insert.="($link2,$link1);";
      $j=$j+1;
      ### Now lets post the information to the mysql database.
      $db->Execute($insert);
      $i=$j;
      print("");
      PrintFlush("- completed successfully. <BR>");
      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=7>";
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
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<input type=submit value=Confirm>";
      echo "</form>";
      break;
   case "7":
      echo "<B><BR>Configuring game scheduler<BR></B>";

      echo "<BR>Update ticks will occur every $sched_ticks minutes<BR>";
 
      echo "Turns will occur every $sched_turns minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_turns.php', '',unix_timestamp(now()))");

      echo "Defenses will be checked every $sched_turns minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_defenses.php', '',unix_timestamp(now()))");

      echo "Furangees will play every $sched_turns minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_furangee.php', '',unix_timestamp(now()))");

      echo "Interests on IGB accounts will be accumulated every $sched_IGB minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_IGB, 0, 'sched_IGB.php', '',unix_timestamp(now()))");

      echo "News will be generated every $sched_news minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_news, 0, 'sched_news.php', '',unix_timestamp(now()))");

      echo "Planets will generate production every $sched_planets minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_planets, 0, 'sched_planets.php', '',unix_timestamp(now()))");

      echo "Ports will regenerate every $sched_ports minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_ports, 0, 'sched_ports.php', '',unix_timestamp(now()))");

      echo "Ships will be towed from fed sectors every $sched_turns minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_tow.php', '',unix_timestamp(now()))");

      echo "Rankings will be generated every $sched_ranking minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_ranking, 0, 'sched_ranking.php', '',unix_timestamp(now()))");

      echo "Sector Defences will degrade every $sched_degrade minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_degrade, 0, 'sched_degrade.php', '',unix_timestamp(now()))");

      echo "The planetary apocalypse will occur every $sched_apocalypse minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_apocalypse, 0, 'sched_apocalypse.php', '',unix_timestamp(now()))");

      echo "<B><BR>Configuring ship types<p></B>";

      echo "Inserting ship type: Spacewagon..."; //starting ship
      $db->Execute("INSERT INTO $dbtables[ship_types] VALUES (" .
                   "1, " .                //type_id
                   "'Spacewagon', " .     //name
                   "'spacewagon.gif', " . //image
                   "'The Spacewagon ship class is often referred to as \"flying trash can\". The surname certainly befits its appearance, as well as functionnality. This is the standard ship issued by the Federation to new colonists departing Earth. This class of ship possesses minimum cargo and weapons space. In addition, its short range engines are not suited for long space travel. Hopping along warp lanes is the only viable way of moving through the universe using this ship.'," .
                   "'Y', " .              //buyable
                   "1000, " .             //cost_credits
                   "0, " .                //cost_ore
                   "0, " .                //cost_goods
                   "0, " .                //cost_energy
                   "0, " .                //cost_organics
                   "10, " .               //turnstobuild
                   "0, " .                //minhull
                   "5, " .                //maxhull
                   "0, " .                //minengines
                   "4, " .                //maxengines
                   "0, " .                //minpower
                   "1, " .                //maxpower (that's MISTER power for you) ;)
                   "0, " .                //mincomputer
                   "1, " .                //maxcomputer
                   "0, " .                //minsensors
                   "2, " .                //maxsensors
                   "0, " .                //minbeams
                   "0, " .                //maxbeams
                   "0, " .                //mintorp_launchers
                   "0, " .                //maxtorp_launchers
                   "0, " .                //minshields
                   "0, " .                //maxshields
                   "0, " .                //minarmour
                   "2, " .                //maxarmour
                   "0, " .                //mincloak
                   "4  " .                //maxcloak
                   ")");
      echo "done<br>";

      echo "Inserting ship type: Stinger..."; //base attack ship
      $db->Execute("INSERT INTO $dbtables[ship_types] VALUES (" .
                   "2, " .                //type_id
                   "'Stinger', " .        //name
                   "'stinger.gif', " .    //image
                   "'This small attack ship is a favorite among space pirates. Its small, lightweigth hull allows for tight turning and a good cruise speed. Since most of the ship\'s limited interior space is used by the weapons system and engines, this ship can carry minimal cargo. Ideal for small slave raids.'," .
                   "'Y', " .              //buyable
                   "80000, " .            //cost_credits
                   "0, " .                //cost_ore
                   "0, " .                //cost_goods
                   "0, " .                //cost_energy
                   "0, " .                //cost_organics
                   "50, " .               //turnstobuild
                   "3, " .                //minhull
                   "4, " .                //maxhull
                   "5, " .                //minengines
                   "8, " .                //maxengines
                   "4, " .                //minpower
                   "8, " .                //maxpower 
                   "4, " .                //mincomputer
                   "8, " .                //maxcomputer
                   "4, " .                //minsensors
                   "8, " .                //maxsensors
                   "4, " .                //minbeams
                   "8, " .                //maxbeams
                   "0, " .                //mintorp_launchers
                   "1, " .                //maxtorp_launchers
                   "0, " .                //minshields
                   "2, " .                //maxshields
                   "0, " .                //minarmour
                   "2, " .                //maxarmour
                   "4, " .                //mincloak
                   "4  " .                //maxcloak
                   ")");
      echo "done<br>";

      echo "Inserting ship type: Marauder..."; //base trade ship
      $db->Execute("INSERT INTO $dbtables[ship_types] VALUES (" .
                   "3, " .                //type_id
                   "'Marauder', " .       //name
                   "'marauder.gif', " .   //image
                   "'The marauder is the standard Feredation supply ship. It offers reasonable cargo space and can be decently outfitted with enough armour to resist attacks from pirates. It is well-liked by Federation officials, who turn its spacious cargo bay into luxuriant living quarters.'," .
                   "'Y', " .              //buyable
                   "120000, " .           //cost_credits
                   "0, " .                //cost_ore
                   "0, " .                //cost_goods
                   "0, " .                //cost_energy
                   "0, " .                //cost_organics
                   "60, " .               //turnstobuild
                   "5, " .                //minhull
                   "12, " .               //maxhull
                   "2, " .                //minengines
                   "6, " .                //maxengines
                   "1, " .                //minpower
                   "4, " .                //maxpower 
                   "1, " .                //mincomputer
                   "4, " .                //maxcomputer
                   "2, " .                //minsensors
                   "4, " .                //maxsensors
                   "1, " .                //minbeams
                   "4, " .                //maxbeams
                   "1, " .                //mintorp_launchers
                   "4, " .                //maxtorp_launchers
                   "2, " .                //minshields
                   "6, " .                //maxshields
                   "2, " .                //minarmour
                   "6, " .                //maxarmour
                   "2, " .                //mincloak
                   "4  " .                //maxcloak
                   ")");
      echo "done<br>";

      echo "Inserting ship type: Katana..."; //base balanced
      $db->Execute("INSERT INTO $dbtables[ship_types] VALUES (" .
                   "4, " .                //type_id
                   "'Katana', " .         //name
                   "'katana.gif', " .     //image
                   "'The Katana is the latest technological wonder of the Federation. This ship combines most of the advantages of the Stinger class with those of the Marauder. Nice all-rounder, this ship is perfect for the successful space adventurer. It is also the strongest ship made available to the general population by the Federation.'," .
                   "'Y', " .              //buyable
                   "180000, " .           //cost_credits
                   "0, " .                //cost_ore
                   "0, " .                //cost_goods
                   "0, " .                //cost_energy
                   "0, " .                //cost_organics
                   "55, " .               //turnstobuild
                   "3, " .                //minhull
                   "8, " .                //maxhull
                   "3, " .                //minengines
                   "6, " .                //maxengines
                   "2, " .                //minpower
                   "6, " .                //maxpower 
                   "2, " .                //mincomputer
                   "6, " .                //maxcomputer
                   "1, " .                //minsensors
                   "6, " .                //maxsensors
                   "1, " .                //minbeams
                   "6, " .                //maxbeams
                   "1, " .                //mintorp_launchers
                   "6, " .                //maxtorp_launchers
                   "1, " .                //minshields
                   "6, " .                //maxshields
                   "3, " .                //minarmour
                   "6, " .                //maxarmour
                   "3, " .                //mincloak
                   "5  " .                //maxcloak
                   ")");
      echo "done<br>";

/************************************************************
Ships from here will have to be built, and the stats above
will probably all be changed when testing, so no use defining
these right now.
************************************************************/

      echo "Inserting ship type: Wraith...";
      echo "done<br>";

      echo "Inserting ship type: Raven...";
      echo "done<br>";

      echo "Inserting ship type: Triton...";
      echo "done<br>";

      echo "Inserting ship type: Phoenix...";
      echo "done<br>";

      echo "Inserting ship type: Sequoia...";
      echo "done<br>";

      echo "Inserting ship type: Valkyrie...";
      echo "done<br>";

      echo "Inserting ship type: Nemesis...";
      echo "done<br>";

      echo "Inserting ship type: Golem...";
      echo "done<br>";

      echo "Inserting ship type: Behemoth...";
      echo "done<br>";

      $password = substr($admin_mail, 0, $maxlen_password);
      echo "<BR><BR><center><B>Your admin login is: <BR>";
      echo "<BR>Username: $admin_mail";
      echo "<BR>Password: $password<BR></B></center>";
      newplayer($admin_mail, "WebMaster", $password, "WebMaster\'s Ship");
  
      PrintFlush("<BR><BR><center><BR><B>Congratulations! Universe created successfully.<BR>");
      PrintFlush("Click <A HREF=login.php>here</A> to return to the login screen.</B></center>");
      break;
   default:
      echo "<form action=create_universe.php method=post>";
      echo "Password: <input type=password name=swordfish size=20 maxlength=20>&nbsp;&nbsp;";
      echo "<input type=submit value=Submit><input type=hidden name=step value=1>";
      echo "<input type=reset value=Reset>";
      echo "</form>";
      break;
}

$StopTime=$BenchmarkTimer->stop();
$Elapsed=$BenchmarkTimer->elapsed();
PrintFlush("<br>Elapsed Time - $Elapsed");
include("footer.php");
</script>
