<?php
	//Get Posted Data
	if(isset($_POST['y1'])){
		$date = $_POST['y1']."-".$_POST['m1']."-".$_POST['d1']." ";
		if($_POST['a1'] == "am") $date1=$date.$_POST['h1'].":00:00";
		else $date1=$date.(intval($_POST['h1'])+12).":00:00";
		
		$date = $_POST['y2']."-".$_POST['m2']."-".$_POST['d2']." ";
		if($_POST['a2'] == "am") $date2=$date.$_POST['h2'].":00:00";
		else $date2=$date.(intval($_POST['h2'])+12).":00:00";
	}

	//Handle Conversions	
	function voltsToFarenheit($volts){
		return (($volts - 0.5) * 100.0) * (9.0/5.0) + 32.0;
	}

	//Mysql Connections
	mysql_connect("localhost","root","root") or die('Could not Connect: ' . mysql_error());
	mysql_select_db("temp_mon");
	
	//Get and Process Results
	$result = mysql_query("SELECT * FROM temp WHERE timestamp BETWEEN '".$date1."' AND '".$date2."'") or die(mysql_error());
  	$prev = false;
  	
  	$prevTimestamp = time();
  	$totalVolts = 0;
  	$count = 0;
  	$data = array();
  	
  	while($row = mysql_fetch_array($result)){
  		$time = date("m-d-y H:i",strtotime($row['timestamp']));
  		if($time != $prev){
  			if($prev != false){
	  			//Throw outputted data into an array.
	  			array_push($data, array("y"=>date("m-d-y @ h:i",$prevTimestamp),"a"=>voltsToFarenheit($totalVolts/$count)));
		  	}
		  	  
	  		$prev = $time;
	  		$prevTimestamp = strtotime($row['timestamp']);
	  		$totalVolts = $row['temp'];
	  		$count = 1;
  		}
  		else{
	  		$totalVolts += $row['temp'];
	  		$count++;
  		}
 	}
    //Throw outputted data into an array.
	array_push($data, array("y"=>date("m-d-y @ h:i",$prevTimestamp),"a"=>voltsToFarenheit($totalVolts/$count)));
  	
  	//echo count($data)."<br /><br />";
  	//JSON-itize it.
    echo json_encode($data);
?>