<?php 

require("library.php");

$status = "default";
if($_POST["status"] == "working") $status = "working";
else if($_POST["status"] == "broken") $status = "broken";
else if($_POST["status"] == "slow") $status = "slow";
else if($_POST["status"] == "oneRepairing") $status = "oneRepairing";
else if($_POST["status"] == "bothRepairing") $status = "bothRepairing";

if(mysql_query("UPDATE dorms SET status='$status' WHERE id='$_POST[id]'")) echo $_POST[id];
else echo "fail";


?>