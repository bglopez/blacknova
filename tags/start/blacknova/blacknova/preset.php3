<?
	include("config.php3");
	updatecookie();

	$title="Change Real Space Preset Sectors";
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
        bigtitle();
	if (!isset($change))
	{
		echo "<FORM ACTION=preset.php3 METHOD=POST>";
		echo "Preset 1: <INPUT TYPE=text NAME=preset1 SIZE=6 MAXLENGTH=6 VALUE=$playerinfo[preset1]><BR>";
		echo "Preset 2: <INPUT TYPE=text NAME=preset2 SIZE=6 MAXLENGTH=6 VALUE=$playerinfo[preset2]><BR>";
		echo "Preset 3: <INPUT TYPE=text NAME=preset3 SIZE=6 MAXLENGTH=6 VALUE=$playerinfo[preset3]><BR>";
		echo "<INPUT TYPE=hidden NAME=change VALUE=1>";
		echo "<BR><INPUT TYPE=submit VALUE=Submit><INPUT TYPE=Reset VALUE=Reset><BR><BR>";
		echo "</FORM>";

	} else {
		$preset1=round(abs($preset1));
		$preset2=round(abs($preset2));
		if ($preset1>$sector_max)
		{
			echo "Preset 1 of $preset1 exceeds universe max of $sector_max. No presets saved.<BR><BR>";	
		} elseif ($preset2>$sector_max) {
			echo "Preset 2 of $preset2 exceeds universe max of $sector_max. No presets saved.<BR><BR>";	
		} elseif ($preset3>$sector_max) {
			echo "Preset 3 of $preset3 exceeds universe max of $sector_max. No presets saved.<BR><BR>";	
		} else {
			$update = mysql_query("UPDATE ships SET preset1=$preset1, preset2=$preset2, preset3=$preset3 WHERE ship_id=$playerinfo[ship_id]");
			echo "Preset 1 set to $preset1, preset 2 set to $preset2 and preset 3 set to $preset3.<BR><BR>";
		}
	}

	echo "Click <a href=main.php3>here</a> to return to main menu.";
	include("footer.php3");

?> 