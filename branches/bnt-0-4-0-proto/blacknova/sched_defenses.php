<?
  if (preg_match("/sched_defenses.php/i", $PHP_SELF)) {
      echo "You can not access this file directly!";
      die();
  }

  $db->Execute("DELETE from $dbtables[sector_defence] where quantity <= 0");

?>