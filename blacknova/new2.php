<?
include("config.php");
include("languages/$lang");

$title=$l_new_title2;

include("header.php");


bigtitle();

connectdb();

if($account_creation_closed)
{
  die($l_new_closed_message);
}
$character=htmlspecialchars($character,ENT_QUOTES);
$shipname=htmlspecialchars($shipname,ENT_QUOTES);
$character=ereg_replace("[^[:digit:][:space:][:alpha:][\']]"," ",$character);
$shipname=ereg_replace("[^[:digit:][:space:][:alpha:][\']]"," ",$shipname);

$username = $HTTP_POST_VARS['username']; //This needs to STAY before the db query

if(!get_magic_quotes_gpc())
{
  $username = addslashes($username);
  $character = addslashes($character);
  $shipname = addslashes($shipname);
}

$flag=0;
if ($username=='' || $character=='' || $shipname=='' ) { echo "$l_new_blank<BR>"; $flag=1;}

$result = $db->Execute ("SELECT email, character_name FROM $dbtables[players] where email='$username' OR character_name='$character'");

if ($result>0)
{
  while (!$result->EOF)
  {
    $row = $result->fields;
    if (strtolower($row[email])==strtolower($username)) { echo "$l_new_inuse  $l_new_4gotpw1 <a href=mail.php?mail=$username>$l_clickme</a> $l_new_4gotpw2<BR>"; $flag=1;}
    if (strtolower($row[character_name])==strtolower($character)) { echo "$l_new_inusechar<BR>"; $flag=1;}
    $result->MoveNext();
  }
}

$result = $db->Execute ("SELECT name FROM $dbtables[ships] where name='$shipname'");

if ($result>0)
{
  while (!$result->EOF)
  {
    $row = $result->fields;
    if (strtolower($row[name])==strtolower($shipname)) { echo "$l_new_inuseship $l_new_4gotpw1 <a href=mail.php?mail=$username>$l_clickme</a> $l_new_4gotpw2<BR>"; $flag=1;}
    $result->MoveNext();
  }
}

if ($flag==0)
{

  /* insert code to add player to database */
  $makepass="";
  $syllables="er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
  $syllable_array=explode(",", $syllables);
  srand((double)microtime()*1000000);
  for ($count=1;$count<=4;$count++) {
    if (rand()%10 == 1) {
      $makepass .= sprintf("%0.0f",(rand()%50)+1);
    } else {
      $makepass .= sprintf("%s",$syllable_array[rand()%62]);
    }
  }

  $shipid = newplayer($username, $character, $makepass, $shipname);

  $l_new_message = str_replace("[pass]", $makepass, $l_new_message);

  $msg = "$l_new_message\r\n\r\nhttp://$SERVER_NAME$gamepath\r\n";
  $msg = ereg_replace("\r\n.\r\n","\r\n. \r\n",$msg);
  $hdrs .= "From: BlackNova Mailer <$admin_mail>\r\n";

  $e_response=mail($username,$l_new_topic, $msg,$hdrs);
  
  if($display_password)
  {
    echo $l_new_pwis . " " . $makepass . "<BR><BR>";
  }
  if ($Enable_EmailLoggerModule AND $modules['ELM'])
  {
    if ($e_response===TRUE)
    {
      echo "<font color=\"lime\">Email sent to $username</font> - \n";
      AddELog($username,Registering,'Y',$l_new_topic,$e_response);
    }
    else
    {
      echo "<font color=\"Red\">Email failed to send to $username</font> - \n";
      AddELog($username,Registering,'N',$l_new_topic,$e_response);
    }
  }
  else
  {
    echo "<font color=\"lime\">Email sent to $username</font><br>";
  }
  echo "<BR>";
  echo "<A HREF=login.php class=nav>$l_clickme</A> $l_new_login";

} else {

  echo $l_new_err;
}

include("footer.php");
?>
