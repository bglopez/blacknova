<?

include("config.php");
updatecookie();

include("languages/$lang");

$basefontsize = 0;
$stylefontsize = "8Pt";

if($screenres >= 1024)
{
  $basefontsize = 1;
  $stylefontsize = "12Pt";
}

$title="Planet test";
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

?>

<style type="text/css">

.baseout {border-style: outset; border-color:#FFFFFF; border-width:5px 5px 5px 5px;}
.basecell {border-style: solid; border-color:#AAAAAA; border-width:1px 1px 1px 1px; background-color:#666666; width:50px; height:50px}
.faccell {border-style: solid; border-color:#000000; width:44px; height:44px}

</style>

<?

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = $res->fields;

$res = $db->Execute("SELECT zone_id,zone_name FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
$zoneinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if($res)
  $planetinfo=$res->fields;

if(empty($planetinfo))
  planet_die($l_planet_none);

if($playerinfo[sector] != $planetinfo[sector_id])
{
  if($playerinfo[on_planet] == 'Y')
    $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
  planet_die($l_planet_none);
}

if($planetinfo[name] == "")
  $planetinfo[name] = "Unnamed";

if($command == 'build')
  planet_build();
elseif($command == 'transfer')
  planet_transfer();
elseif($command == 'basebuild')
  planet_basebuild();

echo "<table border=2 cellspacing=2 cellpadding=2 bgcolor=#400040 width=75% align=center>" .
     "<tr><td align=center colspan=3><font color=silver size=" . ($basefontsize+2) . " face=arial>Planet <font color=white><b>$planetinfo[name]</b></font>, owned by " .
     "<b><font color=white> $playerinfo[character_name]</font></b>" .
     "</td></tr>" .
     "</table>";

?>

<table width=75% cellpadding=0 cellspacing=1 border=0 align=center>
<tr><td>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">&nbsp;<? echo $l_turns_have; ?></font><font color=white><b><? echo NUMBER($playerinfo[turns]) ?></b></font>
</td>
<td align=center>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial"><? echo $l_turns_used ?></font><font color=white><b><? echo NUMBER($playerinfo[turns_used]); ?></b></font>
</td>
<td align=right>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial"><? echo $l_score?></font><font color=white><b><? echo NUMBER($playerinfo[score])?>&nbsp;</b></font>
</td>
<tr><td>
<font color=silver size=<? echo $basefontsize + 2; ?> face="arial">&nbsp;<? echo $l_sector ?>: </font><font color=white><b><? echo $playerinfo[sector]; ?></b></font>
</td><td align=center>

<?
if(!empty($sectorinfo[beacon]))
{
  echo "<font color=white size=", $basefontsize + 2," face=\"arial\"><b>", $sectorinfo[beacon], "</b></font>";
}

if($zoneinfo[zone_id] < 5)
  $zoneinfo[zone_name] = $l_zname[$zoneinfo[zone_id]];

?>
</td><td align=right>

<a href="<? echo "zoneinfo.php?zone=$zoneinfo[zone_id]"; ?>"><b><? echo "<font size=", $basefontsize + 2," face=\"arial\">$zoneinfo[zone_name]</font>"; ?></b></font></a>&nbsp;
</td></tr>
</table>

<?

if($playerinfo[ship_id] != $planetinfo[owner])
{
}
else //player is planet owner
{
?>

<table width=100% border=0 align=center cellpadding=0 cellspacing=0">

<tr>

<td valign=top>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
<? echo $l_commands ?>
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<div class=mnu>
&nbsp;<a class=mnu href="planet.php?command=name&planet_id=<? echo $planet_id ?>"><? echo "Rename Planet"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="planet.php?command=land&planet_id=<? echo $planet_id ?>"><? echo "Land on Planet"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="planet.php?command=transfer&planet_id=<? echo $planet_id ?>"><? echo "Transfer Cargo"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="planet.php?command=sell&planet_id=<? echo $planet_id ?>"><? echo "Sell Commodities"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="planet.php?command=build&planet_id=<? echo $planet_id ?>"><? echo "Build Facilities"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="planet.php?command=scrap&planet_id=<? echo $planet_id ?>"><? echo "Scrap Facilities"; ?></a>&nbsp;<br>
</div>
</td></tr>
<tr><td nowrap>
<div class=mnu>
&nbsp;<a class=mnu href="main.php"><? echo "Main menu" ?></a>&nbsp;<br>
</div>
</td></tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
<? echo "Production"; ?>
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<a class=dis>
<img align=absmiddle height=12 width=12 alt="<? echo $l_ore ?>" src="images/ore.gif">&nbsp;<? echo $l_ore ?>&nbsp;<br><div class=mnu align=right>&nbsp;0&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_organics ?>" src="images/organics.gif">&nbsp;<? echo $l_organics ?>&nbsp;<br><div class=mnu align=right>&nbsp;0&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_goods ?>" src="images/goods.gif">&nbsp;<? echo $l_goods ?>&nbsp;<br><div class=mnu align=right>&nbsp;0&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_energy ?>" src="images/energy.gif">&nbsp;<? echo $l_energy ?>&nbsp;<br><div class=mnu align=right>&nbsp;0&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_colonists ?>" src="images/colonists.gif">&nbsp;<? echo $l_colonists ?>&nbsp;<br><div class=mnu align=right>&nbsp;0&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_credits ?>" src="images/credits.gif">&nbsp;<? echo $l_credits ?>&nbsp;<br><div class=mnu align=right>&nbsp;0&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_fighters ?>" src="images/tfighter.gif">&nbsp;<? echo $l_fighters ?>&nbsp;<br><div class=mnu align=right>&nbsp;0&nbsp</div>
</a>
</td></tr>
</table>

<td align=center>
<?
if($planetinfo[base] == 'N')
{
  echo "<font size=" . ($basefontsize+2) . "face=arial color=silver><b>This planet is only a dead, empty lump of rock floating in space.<p>For it to be able to support life, you must build a domed base on it first.</b></font>";
}
else
{
  echo "<font color=white size=4><b>Base Layout</b></font><p>";
  
  $grid= planet_BuildGrid();

  echo "<table id=base class=baseout border=0 cellspacing=0 cellpadding=0>";

  for($i=1;$i<=$base_ysize;$i++)
  {
    echo "<tr>\n";
    for($j=1;$j<=$base_ysize;$j++)
    {
      $img = $grid[($i * 10) + $j][image];
      if(!empty($img))
      {
        $imgstring = "background=images/$img";
        $class = "faccell";
        $border = $grid[($i * 10) + $j][border];
      }
      else
      {
        $imgstring = "";
        $class = "basecell";
        $border = "1px 1px 1px 1px";
      }

      echo "<td style=\"border-width: $border\" $imgstring id=\"$i$j\" class=\"$class\">&nbsp;</td>\n";
    }
    echo "</tr>\n";
  }
  echo "</table>";

}

?>


</td>

<td valign=top>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
<? echo "Stores"; ?>
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<a class=dis>
<img align=absmiddle height=12 width=12 alt="<? echo $l_ore ?>" src="images/ore.gif">&nbsp;<? echo $l_ore ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($planetinfo[ore]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_organics ?>" src="images/organics.gif">&nbsp;<? echo $l_organics ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($planetinfo[organics]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_goods ?>" src="images/goods.gif">&nbsp;<? echo $l_goods ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($planetinfo[goods]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_energy ?>" src="images/energy.gif">&nbsp;<? echo $l_energy ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($planetinfo[energy]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_colonists ?>" src="images/colonists.gif">&nbsp;<? echo $l_colonists ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($planetinfo[colonists]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_credits ?>" src="images/credits.gif">&nbsp;<? echo $l_credits ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($planetinfo[credits]); ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_fighters ?>" src="images/tfighter.gif">&nbsp;<? echo $l_fighters ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo NUMBER($planetinfo[fighters]); ?>&nbsp</div>
</a>
</td></tr>
</table>

<br>

<table border="0" cellpadding="0" cellspacing="0" align="center"><tr valign="top">
<td><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/lcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
<td nowrap bgcolor="#400040"><font face="verdana" size="1" color="#ffffff"><b>
<? echo "Defenses"; ?>
</b></font></td>
<td align="right"><table border="0" cellpadding="0" cellspacing="0" height="100%">
  <tr><td><img src="images/rcorner.gif" width="8" height="11" border="0"></td></tr>
  <tr><td bgcolor="#400040" height="100%"><img src="images/spacer.gif" width="8" height="100%" border="0"></td></tr>
</table></td>
</tr></table>

<TABLE BORDER=2 CELLPADDING=2 BGCOLOR="#500050" align="center">
<TR><TD NOWRAP>
<a class=dis>
&nbsp;<? echo "Total Offense" ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo "0" ?>&nbsp</div>
&nbsp;<? echo "Shields" ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo "0"; ?>&nbsp</div>
&nbsp;<? echo "Energy Drain" ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo "0" ?>&nbsp</div>
&nbsp;<? echo "Attack Drain" ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo "0" ?>&nbsp</div>
</a>
</td></tr>
</table>

</td>

</table>

<?
}

?>


<?

include("footer.php");

function planet_die($error_msg, $showmain = 1)
{
  echo "<p>$error_msg<p>";


  if($showmain == 1)
    TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

function planet_build()
{
  global $playerinfo, $planetinfo;
  global $planet_id;
  global $title;
  global $base_goods, $base_ore, $base_organics, $base_credits;
  global $color_line1, $color_line2;

  if($playerinfo[ship_id] != $planetinfo[owner])
    planet_die("Only planet owners can execute the build command.");

  if($planetinfo[base] == 'N')
  {
    $enough = 1;
    $ocolor= "#00FF00";
    $gcolor= "#00FF00";
    $rcolor= "#00FF00";
    $ccolor= "#00FF00";

    if($planetinfo[ore] < $base_ore)
    {
      $ocolor= "#FF0000";
      $enough=0;
    }

    if($planetinfo[organics] < $base_organics)
    {
      $rcolor= "#FF0000";
      $enough=0;
    }

    if($planetinfo[goods] < $base_goods)
    {
      $gcolor= "#FF0000";
      $enough=0;
    }

    if($planetinfo[credits] < $base_credits)
    {
      $ccolor= "#FF0000";
      $enough=0;
    }

    $title="Building a base";
    bigtitle();
  
    echo "<p>There is no base on this planet. To develop this planet, you must first build a domed base on it.<p>";
    
    echo "<table border=1 cellspacing=1 cellpadding=0 width=\"65%\">" .
         "<tr bgcolor=$color_line2><td width=50% align=center><b><font color=white>Base Cost</font></b></td>" .
         "<td align=center width=50%><b><font color=white>Planet Stores</font></b></td></tr>" .
         "<tr><td>" .
         "<table border=0 cellspacing=0 cellpadding=2 width=\"100%\" align=center>" .
         "<tr bgcolor=$color_line1><td width=\"50%\"><font color=white size=3>&nbsp;Ore</font></td><td width=\"50%\" align=right><font color=white size=3>" . NUMBER($base_ore) . "&nbsp;</font></td></tr>" .
         "<tr bgcolor=$color_line2><td width=\"50%\"><font color=white size=3>&nbsp;Goods</font></td><td width=\"50%\" align=right><font color=white size=3>" . NUMBER($base_goods) . "&nbsp;</font></td></tr>" .
         "<tr bgcolor=$color_line1><td width=\"50%\"><font color=white size=3>&nbsp;Organics</font></td><td width=\"50%\" align=right><font color=white size=3>" . NUMBER($base_organics) . "&nbsp;</font></td></tr>" .
         "<tr bgcolor=$color_line2><td width=\"50%\"><font color=white size=3>&nbsp;Credits</font></td><td width=\"50%\" align=right><font color=white size=3>" . NUMBER($base_credits) . "&nbsp;</font></td></tr>" .
         "</table>" .
         "</td><td>" .
         "<table border=0 cellspacing=0 cellpadding=2 width=\"100%\" align=center>" .
         "<tr bgcolor=$color_line1><td width=\"50%\"><font color=white size=3>&nbsp;Ore</font></td><td width=\"50%\" align=right><font color=$ocolor size=3>" . NUMBER($planetinfo[ore]) . "&nbsp;</font></td></tr>" .
         "<tr bgcolor=$color_line2><td width=\"50%\"><font color=white size=3>&nbsp;Goods</font></td><td width=\"50%\" align=right><font color=$gcolor size=3>" . NUMBER($planetinfo[goods]) . "&nbsp;</font></td></tr>" .
         "<tr bgcolor=$color_line1><td width=\"50%\"><font color=white size=3>&nbsp;Organics</font></td><td width=\"50%\" align=right><font color=$rcolor size=3>" . NUMBER($planetinfo[organics]) . "&nbsp;</font></td></tr>" .
         "<tr bgcolor=$color_line2><td width=\"50%\"><font color=white size=3>&nbsp;Credits</font></td><td width=\"50%\" align=right><font color=$ccolor size=3>" . NUMBER($planetinfo[credits]) . "&nbsp;</font></td></tr>" .
         "</table>" .
         "</tr>" .
         "</table>";

    
    if($enough == 0)
      echo "<p><font color=red><b>You do not have sufficient resources on this planet to build a domed base.</b></font><p>";
    else
      echo "<p><font color=silver><a href=planet.php?command=basebuild&planet_id=$planet_id>Build</a> a domed base on this planet!</font><p>";

  }
  else
  {
    
  }

  planet_die("<p>Click <a href=planet.php?planet_id=$planet_id>here</a> to return to the main planet menu.<p>", 0);
}

function planet_transfer()
{
  global $playerinfo, $planetinfo, $planet_id;
  global $l_planet_cinfo, $l_planet_transfer_link;
  global $title, $color_line1, $color_line2, $color_header;
  global $l_commodity, $l_ore, $l_organics, $l_goods, $l_energy, $l_colonists, $l_torps, $l_fighters, $l_credits;
  global $l_planet_toplanet, $l_planet, $l_ship, $l_all;

  $title="Planet Transfer";
  bigtitle();

  if($playerinfo[ship_id] != $planetinfo[owner])
  {
    if($planetinfo[corp] == 0)
      planet_die("You can't transfer cargo to planets you do not own.");

    if($planetinfo[corp] != $playerinfo[team])
      planet_die("You can't transfer cargo to planets you do not own.");
  }

  $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
  $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];
  $l_planet_cinfo=str_replace("[cargo]",NUMBER($free_holds),$l_planet_cinfo);
  $l_planet_cinfo=str_replace("[energy]",NUMBER($free_power),$l_planet_cinfo);
  echo "$l_planet_cinfo<BR><BR>";
  echo "<FORM ACTION=planet2.php?planet_id=$planet_id METHOD=POST>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo"<TR BGCOLOR=\"$color_header\"><TD><B>$l_commodity</B></TD><TD><B>$l_planet</B></TD><TD><B>$l_ship</B></TD><TD><B>$l_planet_transfer_link</B></TD><TD><B>$l_planet_toplanet</B></TD><TD><B>$l_all?</B></TD></TR>";
  echo"<TR BGCOLOR=\"$color_line1\"><TD>$l_ore</TD><TD>" . NUMBER($planetinfo[ore]) . "</TD><TD>" . NUMBER($playerinfo[ship_ore]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_ore SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpore VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allore VALUE=-1></TD></TR>";
  echo"<TR BGCOLOR=\"$color_line2\"><TD>$l_organics</TD><TD>" . NUMBER($planetinfo[organics]) . "</TD><TD>" . NUMBER($playerinfo[ship_organics]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_organics SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tporganics VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allorganics VALUE=-1></TD></TR>";
  echo"<TR BGCOLOR=\"$color_line1\"><TD>$l_goods</TD><TD>" . NUMBER($planetinfo[goods]) . "</TD><TD>" . NUMBER($playerinfo[ship_goods]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_goods SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpgoods VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allgoods VALUE=-1></TD></TR>";
  echo"<TR BGCOLOR=\"$color_line2\"><TD>$l_energy</TD><TD>" . NUMBER($planetinfo[energy]) . "</TD><TD>" . NUMBER($playerinfo[ship_energy]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_energy SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpenergy VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allenergy VALUE=-1></TD></TR>";
  echo"<TR BGCOLOR=\"$color_line1\"><TD>$l_colonists</TD><TD>" . NUMBER($planetinfo[colonists]) . "</TD><TD>" . NUMBER($playerinfo[ship_colonists]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_colonists SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpcolonists VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allcolonists VALUE=-1></TD></TR>";
  echo"<TR BGCOLOR=\"$color_line2\"><TD>$l_fighters</TD><TD>" . NUMBER($planetinfo[fighters]) . "</TD><TD>" . NUMBER($playerinfo[ship_fighters]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_fighters SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpfighters VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allfighters VALUE=-1></TD></TR>";
  echo"<TR BGCOLOR=\"$color_line1\"><TD>$l_torps</TD><TD>" . NUMBER($planetinfo[torps]) . "</TD><TD>" . NUMBER($playerinfo[torps]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_torps SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tptorps VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=alltorps VALUE=-1></TD></TR>";
  echo"<TR BGCOLOR=\"$color_line2\"><TD>$l_credits</TD><TD>" . NUMBER($planetinfo[credits]) . "</TD><TD>" . NUMBER($playerinfo[credits]) . "</TD><TD><INPUT TYPE=TEXT NAME=transfer_credits SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpcredits VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allcredits VALUE=-1></TD></TR>";
  echo "</TABLE><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=$l_planet_transfer_link>&nbsp;<INPUT TYPE=RESET VALUE=Reset>";
  echo "</FORM>";

  planet_die("<p>Click <a href=planet.php?planet_id=$planet_id>here</a> to return to the main planet menu.<p>", 0);

}

function planet_basebuild()
{
  global $playerinfo, $planetinfo;
  global $planet_id;
  global $title;
  global $base_goods, $base_ore, $base_organics, $base_credits;
  global $db, $dbtables;

  $title="Building a base";
  bigtitle();

  if($planetinfo[base] == 'Y')
    planet_die("There is already a base on this planet.");
  
  if($playerinfo[ship_id] != $planetinfo[owner])
    planet_die("You can't build a base on a planet you do not own.");

  if($base_goods > $planetinfo[goods] || $base_ore > $planetinfo[ore] || $base_organics > $planetinfo[organics] || $base_credits > $planetinfo[credits])
    planet_die("You do not have enough resources on this planet to build a base.");

  $db->Execute("UPDATE $dbtables[planets] SET ore=ore-$base_ore, goods=goods-$base_goods, organics=organics-$base_organics, credits=credits-$base_credits, base='Y' WHERE planet_id=$planet_id");

  echo "Base has been built successfully.";
  planet_die("<p>Click <a href=planet.php?planet_id=$planet_id>here</a> to return to the main planet menu.<p>", 0);
}

function planet_BuildGrid()
{
  global $planet_id;
  global $db, $dbtables;
  global $base_xsize, $base_ysize;

  $res = $db->Execute("SELECT grid FROM $dbtables[bases] WHERE planet_id=$planet_id");
  
  $tgrid = explode(" ", $res->fields[grid]);
  
  $pos = 0;
  for ($i = 1; $i <= $base_ysize; $i++)
  {
    for ($j = 1; $j <= $base_xsize; $j++)
    {
      if($tgrid[$pos] == 0)
      {
        $grid[($i * 10) + $j][image] = "";
      }
      else
      {
      }
      $pos++;
    }
  }

  return $grid;
}
?>
