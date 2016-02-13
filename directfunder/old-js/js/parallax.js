$(document).ready(function() {
	$(window).bind('scroll', function() {
		parallax();
	});
	$(".cur").click(function(){
		//$(this).parent().css("background-color","#FFFFFF");
		var fli;
		$(this).parent().find(".describe").toggle();
		if($(this).parent().find(".describe").css("display") == "block") {
			$(this).parent().addClass("expand");
			$(this).parent().find(".down").addClass("up");
		}
		else {
			$(this).parent().removeClass("expand");
			$(this).parent().find(".down").removeClass("up");
		}
	});
});

function parallax() {
	/*var scrollPos = $(window).scrollTop();//alert(scrollPos);
	if (parseInt(scrollPos) ==0) {
		$(".header").css("position","");
	} else {
		$(".header").css("position","fixed");
	}
	*/
	
}