//Requires jQuery
//Function to Display a Modal Box.  Created by Brandon Storz.
function modalBox(link,header,message){
	$(link).click(function(event) {

		$("body").append('<div class="darkenPage"></div>');
		$("div.darkenPage").css({"display":"none"});
		$("div.darkenPage").append('<div class="modalBox"><div class="alert">'+header+'</div>'+message+'</div>');
		$("div.darkenPage").fadeIn(300);
		$("a.cancel,div.darkenPage").click(function(event){if(event.target == this) $("div.darkenPage").fadeOut(300).remove();});
	
		event.preventDefault();
		return false;
		event.stopPropagation();
	});
}