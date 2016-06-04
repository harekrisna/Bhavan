$(function(){	
	var status = "hidden";

	$(window).on('load',function () {
		if($('#side_controls #back').length) {
			$('#side_controls').fadeIn();
			$('#side_controls #up').css('opacity', 0);
		}
		else {
			$('#side_controls #up').css('borderRadius', 8);
		}
	});	

	
	$(window).on('scroll load',function () {
		var scrollPosY = $(window).scrollTop();

		if($('#side_controls #back').length) {		
			if(scrollPosY > 50) {
				if(status == "hidden") {
					status = "visible";
					$('#side_controls, #back, #up').clearQueue();
					$('#side_controls #up').animate({ opacity: "1"});
					$('#side_controls #back').animate({ top: "45px", borderTopLeftRadius: "0", borderTopRightRadius: "0"});
					$('#side_controls').animate({ height: "88px" });
				}
			}
			else { 
				if(status == "visible") {
					status = "hidden";
					$('#side_controls, #back').clearQueue();
					$('#side_controls #up').animate({ opacity: "0" });
					$('#side_controls').animate({ height: "45px" });
					$('#side_controls #back').animate({ top: "4px", borderTopLeftRadius: "8", borderTopRightRadius: "8" });				
				}
			}
		}
		else {
			if(scrollPosY > 50) {
				if(status == "hidden") {
					status = "visible";
					$('#side_controls').clearQueue();
					$('#side_controls').fadeIn();
				}
			}
			else { 
				if(status == "visible") {
					status = "hidden";
					$('#side_controls, #back').clearQueue();
					$('#side_controls').fadeOut();				
				}
			}
		}
	});	
	
	$("a#up").click(function() {
		$("html, body").animate({ scrollTop: 0 }, "slow");
		return false;
	});	
});
