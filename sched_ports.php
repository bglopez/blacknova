<?php

    # Stop external linking.
    if ( preg_match("/sched_ports.php/i", $_SERVER['PHP_SELF']) )
    {
        echo "You can not access this file directly!";
        die();
    }

    # Update Ore in Ports
    echo "<B>PORTS</B><BR><BR>";
    echo "Adding ore to all commodities ports...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_ore=port_ore+($ore_rate*$multiplier*$port_regenrate) WHERE port_type='ore' AND port_ore<$ore_limit"));
    echo "Adding ore to all ore ports...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_ore=port_ore+($ore_rate*$multiplier*$port_regenrate) WHERE port_type!='special' AND port_type!='none' AND port_ore<$ore_limit"));
    echo "Ensuring minimum ore levels are 0...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_ore=0 WHERE port_ore<0"));
    echo "<BR>";

    # Update Organics in Ports
    echo "Adding organics to all commodities ports...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_organics=port_organics+($organics_rate*$multiplier*$port_regenrate) WHERE port_type='organics' AND port_organics<$organics_limit"));
    echo "Adding organics to all organics ports...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_organics=port_organics+($organics_rate*$multiplier*$port_regenrate) WHERE port_type!='special' AND port_type!='none' AND port_organics<$organics_limit"));
    echo "Ensuring minimum organics levels are 0...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_organics=0 WHERE port_organics<0"));
    echo "<BR>";

    # Update Goods in Ports
    echo "Adding goods to all commodities ports...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_goods=port_goods+($goods_rate*$multiplier*$port_regenrate) WHERE port_type='goods' AND port_goods<$goods_limit"));
    echo "Adding goods to all goods ports...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_goods=port_goods+($goods_rate*$multiplier*$port_regenrate) WHERE port_type!='special' AND port_type!='none' AND port_goods<$goods_limit"));
    echo "Ensuring minimum goods levels are 0...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_goods=0 WHERE port_goods<0"));
    echo "<BR>";

    # Update Energy in Ports
    echo "Adding energy to all commodities ports...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_energy=port_energy+($energy_rate*$multiplier*$port_regenrate) WHERE port_type='energy' AND port_energy<$energy_limit"));
    echo "Adding energy to all energy ports...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_energy=port_energy+($energy_rate*$multiplier*$port_regenrate) WHERE port_type!='special' AND port_type!='none' AND port_energy<$energy_limit"));
    echo "Ensuring minimum energy levels are 0...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_energy=0 WHERE port_energy<0"));
    echo "<BR>";

    # Now check to see if any ports are over max, if so rectify.
    echo "Checking Energy Port Cap...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_energy=$energy_limit WHERE port_energy > $energy_limit"));
    echo "Checking Goods Port Cap...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_goods=$goods_limit WHERE  port_goods > $goods_limit"));
    echo "Checking Organics Port Cap...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_organics=$organics_limit WHERE port_organics > $organics_limit"));
    echo "Checking Ore Port Cap...";
    QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_ore=$ore_limit WHERE port_ore > $ore_limit"));
    $multiplier = 0;
?>
