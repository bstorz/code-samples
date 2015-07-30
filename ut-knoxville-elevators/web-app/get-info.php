<?php
require("library.php"); 

//Terrible Solution.  Fix Later.
$result = mysql_fetch_assoc($results);

echo "<div class='status'>".$responses[$result['status']]."</div>";
echo "<div class='length'>for ";
prettyTime(time()-strtotime($result["timestamp"]));
echo "</div>";

?>