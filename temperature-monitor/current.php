<?php
	function voltsToFarenheit($volts){
		return (($volts - 0.5) * 100.0) * (9.0/5.0) + 32.0;
	}
	
	$con = mysql_connect("localhost","root","root");
	$now = time();
	if(!$con) die('COULD NOT CONNECT: ' . mysql_error());
	mysql_select_db("temp_mon",$con);
	
	$result = mysql_query("SELECT * FROM temp ORDER BY timestamp DESC LIMIT 0,1");
	$row = mysql_fetch_array($result);
	echo "<span class='currentTemp'><span class='theTemp'>".voltsToFarenheit($row['temp'])."</span>&deg;F</span> on ".date("M d, Y \a\\t h:i:s a",strtotime($row['timestamp']));
?>