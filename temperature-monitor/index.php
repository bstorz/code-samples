<?php $now = time(); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Temperature Monitor</title>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
		<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
		<script src="https://raw.github.com/oesmith/morris.js/0.3.3/morris.js"></script>
		
		<style type="text/css">
			html,body{
				font: 14px/1.5 "Helvetica";
				margin: 15px 20px;
			}
			div#graph{
				background: #eee;
				border: 1px solid #ddd;
				border-radius: 15px;
				margin:20px 0 0 0;
			}
			div#current{
				color: #555;
				margin: 15px 0px;
				font-weight: bold;
				font-size: 15px;
			}
			div#current a{
				color: #63b4ff;
				font-size: 12px;
				text-decoration: none;
			}
			div#current span.currentTemp{
				color: #000;
				font-size: 18px;
				font-weight: bold;
				display: inline-block;
				margin: 3px;
			}
			span.hot{
				color: #961414;
			}
			span.cold{
				color: #1341d6;
			}
			span.comfortable{
				color: #83097d;
			}
			div#graph.hot{
				background: #961414;
				color: #fff;
			}
			div#graph.cold{
				background: #1341d6;
				color: #fff;
			}
			div#graph.comfortable{
				background: #83097d;
				color: #fff;
			}
			div#graph.comfortable text{
				fill: #fff !important;
			}
			div#graph.hot text{
				fill: #fff !important;
			}
			div#graph.cold text{
				fill: #fff !important;
			}
		</style>
	</head>
	<body>
		<div id="current">Current temperature is <span></span> <a href="#refresh">(refresh)</a></div>
		<form id="selectDay">
			<select name="m1" id="m1">
			<?php for($j=1;$j<=12;$j++) echo "<option value='".$j."'>".date("M",strtotime("2012-".$j."-01"))."</option>"; ?>
			</select>
			<select name="d1" id="d1">
			<?php for($j=1;$j<=32;$j++) echo "<option value='".$j."'>".$j."</option>"; ?>
			</select>
			<select name="y1" id="y1">
			<?php for($j=2011;$j<=date("Y",$now);$j++) echo "<option value='".$j."'>".$j."</option>"; ?>
			</select>
			<select name="h1" id="h1">
			<?php	
				for($j=0;$j<=11;$j++){
					echo "<option value='".$j."'";
					if($j==0) echo ">12:00</option>";
					else echo ">".$j.":00</option>";
				}
			?>
			</select>
			<select name="a1" id="a1">
				<option value="am">AM</option>
				<option value="pm">PM</option>
			</select>
			<br />
			<select name="m2" id="m2">
			<?php for($j=1;$j<=12;$j++) echo "<option value='".$j."'>".date("M",strtotime("2012-".$j."-01"))."</option>"; ?>
			</select>
			<select name="d2" id="d2">
			<?php for($j=1;$j<=32;$j++) echo "<option value='".$j."'>".$j."</option>"; ?>
			</select>
			<select name="y2" id="y2">
			<?php for($j=2011;$j<=date("Y",$now);$j++) echo "<option value='".$j."'>".$j."</option>"; ?>
			</select>
			<select name="h2" id="h2">
			<?php	
				for($j=0;$j<=11;$j++){
					echo "<option value='".$j."'";
					if($j==0) echo ">12:00</option>";
					else echo ">".$j.":00</option>";
				}
			?>
			</select>
			<select name="a2" id="a2">
				<option value="am">AM</option>
				<option value="pm">PM</option>
			</select>
			<br />
			<input type="submit" value="Change" id="changeRestraints" />
		</form>
		
		<div id="graph"><div id="graph-area"></div></div>
		<div id="data"></div>
		
		<script type="text/javascript">
			//Setup Defaults
			var output = "Loading";
			var date = new Date;
			y1=date.getFullYear();
			y2=date.getFullYear();
			m1=date.getMonth()+1;
			m2=date.getMonth()+1;
			d1=date.getDate();
			d2=date.getDate();
			h1=0;
			h2=date.getHours();
			a1 = "am";
			a2 = "am";
			if(h2>12){
				h2=h2-12;
				a2 = "pm";
			}
			$("#m1").children("option[value="+m1+"]").attr("selected","selected");
			$("#m2").children("option[value="+m2+"]").attr("selected","selected");
			$("#d1").children("option[value="+d1+"]").attr("selected","selected");
			$("#d2").children("option[value="+d2+"]").attr("selected","selected");
			$("#y1").children("option[value="+y1+"]").attr("selected","selected");
			$("#y2").children("option[value="+y2+"]").attr("selected","selected");
			$("#h1").children("option[value="+h1+"]").attr("selected","selected");
			$("#h2").children("option[value="+h2+"]").attr("selected","selected");
			$("#a1").children("option[value="+a1+"]").attr("selected","selected");
			$("#a2").children("option[value="+a2+"]").attr("selected","selected");
			
			//Reload Function
			function reloadGraph(){
				$.ajax({
					type: 'POST',
					url: 'json.php',
					data: 'y1='+y1+'&m1='+m1+'&d1='+d1+'&a1='+a1+'&h1='+h1+'&y2='+y2+'&m2='+m2+'&d2='+d2+'&a2='+a2+'h1'+h1+'&h2='+h2,
					dataType: 'json',
					success: function(data){
						console.log("Loading");
						graph.setData(data);
					}
				});
				$.ajax({
					type: 'POST',
					url: 'current.php',
					dataType: 'html',
					success: function(data){
						$("div#current span").html(data); 
						/* Pretty Colors
						if(Math.abs($("span.theTemp").text()-72) < 6){
							$("span.currentTemp").addClass("comfortable");
							$("div#graph").addClass("comfortable");
						}
						else if($("span.theTemp").text() > 72){
							$("span.currentTemp").addClass("hot");
							$("div#graph").addClass("hot");
						}
						else if($("span.theTemp").text() < 72){
							$("span.currentTemp").addClass("cold");
							$("div#graph").addClass("cold");
						}*/
					}
				});
			}
			
			//Create the Graph
			var graph =	Morris.Area({
				  element: 'graph-area',
				  parseTime: false,
				  xlabels: "hour",
				  hideHover: true,
				  data: output,
				  xkey: 'y',
				  ykeys: ['a'],
				  labels: ['Temperature (F)']
				});
			
			//Load the Graph
			reloadGraph();
			
			//Reload on Refresh Button Click
			$("a[href=#refresh]").click(function(){
				reloadGraph();
			});
			//Reload on Change Button Click
			$("#changeRestraints").click(function(event){
				event.preventDefault();
				y1=$("#y1").children("option[selected]").val();
				y2=$("#y2").children("option[selected]").val();
				m1=$("#m1").children("option[selected]").val();
				m2=$("#m2").children("option[selected]").val();
				d1=$("#d1").children("option[selected]").val();
				d2=$("#d2").children("option[selected]").val();
				h1=$("#h1").children("option[selected]").val();
				h2=$("#h2").children("option[selected]").val();
				a1=$("#a1").children("option[selected]").val();
				a2=$("#a2").children("option[selected]").val();
				reloadGraph();
			});
			
			//Reload Automatically Every 16.66667 Minutes
			setInterval(reloadGraph, 1000000);
		</script>
	</body>
</html>
<?php mysql_close($con); ?>