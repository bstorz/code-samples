<?php
mysql_connect("localhost", "utelevators", "Utelevators01.") or die(mysql_error());
mysql_select_db("utelevators") or die(mysql_error());

$results = mysql_query("SELECT * FROM dorms") or die(mysql_error());
$responses = array("working"=>"Both Elevators <span data-status='working'>Working</span>",
					"broken"=>"Both Elevators <span data-status='broken'>Broken</span>",
					"slow"=>"One Elevator <span data-status='slow'>Broken</span>",
					"oneRepairing"=>"One Elevator <span data-status='oneRepairing'>Being Repaired</span>",
					"bothRepairing"=>"Both Elevators <span data-status='bothRepairing'>Being Repaired</span>"
			);
			
function prettyTime($seconds){
	if($seconds/86400 >= 10)
		printf("%d weeks and %d days",floor($seconds/604800),floor(($seconds%604800)/86400));
	else if($seconds/3600 > 24)
		printf("%d days and %d hours",floor($seconds/86400),floor(($seconds%86400)/3600));
	else if($seconds/3600 >= 1 && $seconds/3600 <= 24)
		printf("%d hours and %d minutes",floor($seconds/3600),floor(($seconds%3600)/60));
	else if($seconds/3600 < 1 && $seconds>59)
		printf("%d minutes and %d seconds",floor($seconds/60),$seconds%60);
	else echo "just a few seconds";
}
?>