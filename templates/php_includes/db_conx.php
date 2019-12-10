<?php

$db_conx = mysqli_connect("localhost", "root", "1234");
mysqli_query($db_conx, "SET character_set_results=utf8");
mb_language('uni'); 
mb_internal_encoding('UTF-8');
mysqli_select_db($db_conx, 'leaveapp')or die(mysqli_error());			//databas ename
mysqli_query($db_conx, "set names 'utf8'")or die(mysqli_error());
mysqli_query($db_conx, "SET character_set_client=utf8")or die(mysqli_error());
mysqli_query($db_conx, "SET character_set_connection=utf8")or die(mysqli_error());

?>