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
&nbsp;<a class=mnu href="newplanet.php?command=name&planet_id=<? echo $planet_id ?>"><? echo "Rename Planet"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="newplanet.php?command=land&planet_id=<? echo $planet_id ?>"><? echo "Land on Planet"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="newplanet.php?command=transfer&planet_id=<? echo $planet_id ?>"><? echo "Transfer Cargo"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="newplanet.php?command=sell&planet_id=<? echo $planet_id ?>"><? echo "Sell Commodities"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="newplanet.php?command=build&planet_id=<? echo $planet_id ?>"><? echo "Build Facilities"; ?></a>&nbsp;<br>
&nbsp;<a class=mnu href="newplanet.php?command=scrap&planet_id=<? echo $planet_id ?>"><? echo "Scrap Facilities"; ?></a>&nbsp;<br>
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
  echo "<font size=" . ($basefontsize+2) . "face=arial color=white><b>This planet is only a dead, empty lump of rock floating in space.<p>For it to be able to support life, you must build a domed base on it first.</b></font>";
}
else
{
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
<img align=absmiddle height=12 width=12 alt="<? echo $l_ore ?>" src="images/ore.gif">&nbsp;<? echo $l_ore ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo $planetinfo[ore]; ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_organics ?>" src="images/organics.gif">&nbsp;<? echo $l_organics ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo $planetinfo[organics]; ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_goods ?>" src="images/goods.gif">&nbsp;<? echo $l_goods ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo $planetinfo[goods]; ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_energy ?>" src="images/energy.gif">&nbsp;<? echo $l_energy ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo $planetinfo[energy]; ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_colonists ?>" src="images/colonists.gif">&nbsp;<? echo $l_colonists ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo $planetinfo[colonists]; ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_credits ?>" src="images/credits.gif">&nbsp;<? echo $l_credits ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo $planetinfo[credits]; ?>&nbsp</div>
<img align=absmiddle height=12 width=12 alt="<? echo $l_fighters ?>" src="images/tfighter.gif">&nbsp;<? echo $l_fighters ?>&nbsp;<br><div class=mnu align=right>&nbsp;<? echo $planetinfo[fighters]; ?>&nbsp</div>
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

function planet_die($error_msg)
{
  echo "<p>$error_msg<p>";


  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}
?>
