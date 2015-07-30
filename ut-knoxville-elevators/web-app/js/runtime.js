$("div#faq").hide();
//Update Dorm Info
function updateInfo(){
	$("div.dorm").each(function(index){
		var parent = this;
		$.ajax({
			type: "POST",
			url: "get-info.php",
			data: 'id='+$(this).attr('data-id'),
			dataType: "html",
			success: function(response) {
					$(parent).children("div.info").html(response);
					
					var oldStatus = ""+$(parent).attr('class').split(' ').slice(-1);
					var newStatus = ""+$(parent).children("div.info").children("div.status").children("span").attr("data-status");
					
					var select = $(parent).children("div.available-statuses").children("select");
					select.children("option[selected=selected]").removeAttr("selected");
					select.children("option[class="+newStatus+"]").attr("selected","selected");

					if(oldStatus != newStatus) $(parent).switchClass(oldStatus,newStatus,1000);
				}
			});
		});
}

//Update Info Upon Certain Conditions
setInterval(function(){
	updateInfo();
}, 10000);

$("#refresh a").click(function(){
	updateInfo();
});

//Show / Hide the faq
if(window.location.hash == "#faq"){
	$("div#elevators").fadeToggle("medium",function(){
			$("div#refresh").fadeToggle("medium");
			$("div#faq").fadeToggle("medium");
			$("#float").css({marginBottom: -455});
		});
	}
	if(window.location.hash == "#iphone-faq"){
	$("div#elevators").fadeToggle("medium",function(){
			$("div#refresh").fadeToggle("medium");
			$("div#faq").fadeToggle("medium");
			$("a.cancel").hide();
		});
	}	
	$("div#infoline a").click(function(){
		$("div#elevators").fadeToggle("medium",function(){
			$("div#refresh").fadeToggle("medium");
			$("div#faq").fadeToggle("medium");
			$("#float").css({marginBottom: -455});
		});
	});
	
	$("div#faq a.cancel").click(function(){
		$("div#faq").fadeToggle("medium",function(){
			$("div#refresh").fadeToggle("medium");
			$("div#elevators").fadeToggle("medium");
			$("#float").css({marginBottom: -305});
		});
	});
	
	//Show & Setup the Back of the Dorm Circle
$("div.dorm").click(function(){
	$(this).children("div.available-statuses").fadeToggle("medium");
});
$("div.dorm select").click(function(e){
	e.stopPropagation();
});

//Handle the Changing of the Available Statuses Dropdown
$("div.available-statuses select").change(function(){
	var newStatus = $(this).find(":selected").attr("class");
	var parent = this;
	
	$.ajax({
		type: "POST",
			url: "change-status.php",
			data: 'id='+$(this).parent().parent().attr('data-id')+'&status='+newStatus,
			dataType: "html",
			success: function(response){
				$(parent).parent().fadeOut("slow");
				updateInfo();
			}
		});
});
