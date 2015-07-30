<?php
	mysql_connect("localhost", "utelevators", "Utelevators01.") or die(mysql_error());
	mysql_select_db("utelevators") or die(mysql_error());

	$results = mysql_query("SELECT * FROM dorms") or die(mysql_error());
	$data = array();
	
	while($result = mysql_fetch_assoc($results)) array_push($data, $result);
	echo json_encode($data);
?>