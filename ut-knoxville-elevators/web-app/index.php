<?php require("library.php"); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>UT Elevators</title>
		<link rel="stylesheet" href="css/style.css" />
		<link media="only screen and (max-device-width: 480px)" rel="stylesheet" type="text/css" href="css/mobile.css" />
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery-ui.js"></script>
		
		
		<!-- Setup Webapp for iOS if they create a bookmark for it. -->
		<meta name = "viewport" content = "width = device-width, user-scalable = no">
		<meta name = "apple-mobile-web-app-capable" content = "yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<link rel="apple-touch-startup-image" href="img/splashx.png" sizes="320x460" />
		<link rel="apple-touch-startup-image" href="img/splash@2x.png" sizes="640x920" />
		<link rel="apple-touch-icon-precomposed" href="img/icon.png"/> 
		
		<!-- Keeps the evil IE at bay. -->
		<meta http-equiv="X-UA-Compatible" content="IE=9" />
	</head>
	<body>
<?php
		// Check for mobile device.
                require_once 'Mobile_Detect.php';
                $detect = new Mobile_Detect();
                $layout = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'mobile') : 'desktop');
                if($layout == "desktop" || $layout == "tablet"){      
	     ?>
	     <div id="banner-ad">
			<script type="text/javascript"><!--
				google_ad_client = "ca-pub-0569546432505936";
				/* UT Elevators Ad - Banner */
				google_ad_slot = "0248068760";
				google_ad_width = 468;
				google_ad_height = 60;
				//-->
			</script>
			<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
		</div>
		
<?php
                }
                else{
         ?>
         <div id="mobile-banner-ad">
			<script type="text/javascript"><!--
				google_ad_client = "ca-pub-0569546432505936";
				/* UT Elevators Ad - Mobile */
				google_ad_slot = "1717008698";
				google_ad_width = 320;
				google_ad_height = 50;
				//-->
			</script>
			<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
		</div>
         <?php
	         }
	     ?>
		<div id="float"></div>
		<div id="content">
			<div id="elevators">
				<h1><span class="go-vols">UT Knoxville</span> Elevators</h1>
				<div id="infoline"><a href="#faq">My dorm isn't listed (and other questions).</a></div>
				<div id="dorms">
					<?php
						while($data = mysql_fetch_array($results)){
							echo "<div class='dorm ".$data["status"]."' data-id='".$data["id"]."'>\n";
								echo "\t\t\t\t\t\t<h2>".$data["name"]."</h2>\n";
								echo "\t\t\t\t\t\t<div class='info'><div class='status'>".$responses[$data['status']]."</div>\n";
								echo "\t\t\t\t\t\t<div class='length'>for ";
								prettyTime(time()-strtotime($data['timestamp']));
								echo "</div></div>";
								echo "<a href='#'>Click to Change Status</a>";
							
								echo "\n\t\t\t\t\t\t<div class='available-statuses'>";
								echo "\n\t\t\t\t\t\t\t<h3>Change Status</h3>";
								echo "\n\t\t\t\t\t\t\t<select>";
									foreach(array_keys($responses) as $status){
										echo "\n\t\t\t\t\t\t\t\t<option class='".$status."'";
										if($status == $data["status"]) echo " selected='selected'"; 
										echo ">".$responses[$status]."</option>";
									}
								echo "\n\t\t\t\t\t\t\t</select>";
								echo "\n\t\t\t\t\t\t</div>";
							echo "\n\t\t\t\t\t</div>";
						}
					?>					
				</div>
			</div>
			<div id="faq">
				<h1><span class="go-vols">University of Tennessee Knoxville</span> Elevators FAQ</h1>
				<dl>
					<dt>How does this whole thing work?</dt>
						<dd>Its simple.  If the elevator breaks, someone changes the status.  Then everyone else knows to either expect slow elevators or take the stairs.  When the elevator is fixed, someone changes the status again.  Piece of cake.</dd>
					<dt>My dorm / apartment building isn't listed.</dt>
						<dd>Currently, University of Tennessee Knoxville Elevators is only being deployed in North Carrick.  This helps me work out any bugs, and determine what things need to be changed.  If all goes well, I hope to expand to South Carrick and the rest of the Presidential Courtyard by the end of 2012.</dd>
					<dt>Is there an app for that?</dt>
						<dd>Yes.  The official iPhone app will soon be released in the App Store.  Android and Windows Phone users can just visit this site to be automatically redirected to the mobile version.</dd>
					<dt>Is this provided by the <span class="go-vols">University of Tennessee</span> Housing?</dt>
						<dd>No.  This is 100% student made, maintained, and contributed.  I'm simply a fellow student and dorm dweller who hates being late to class because of broken elevators.</dd>
					<dt>Is this service free? How does it stay running?</dt>
						<dd>Yes.  Its 100% free, and it always will be.  This service is supported by advertisements.  Not the obtrusive pop-uppy kind, the kind that stays in a little box at the top of the page.</dd>
				</dl>
				<div><a href='#' class='cancel'>Close FAQ</a></div>
			</div>
			<div id="refresh">Automatically Updates Every 10 Seconds - <a href="#">Update Now</a></div>
			<div id="footer">Copyright &copy; 2012 <a href="http://www.brandonstorz.com">Brandon Storz</a></div>
		</div>
		<script type="text/javascript" src="js/runtime.js"></script>
	</body>
</html>