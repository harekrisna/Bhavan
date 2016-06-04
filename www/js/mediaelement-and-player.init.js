
$(function(){
  	var me = $('audio').mediaelementplayer({
	    alwaysShowControls: true,
	    features: ['playpause', 'progress', 'current', 'duration', 'volume', 'fullscreen'],
	    alwaysShowHours: true,
	    audioWidth: 730,
	    audioHeight: 25,
	    iPadUseNativeControls: true,
	    iPhoneUseNativeControls: true,
	    AndroidUseNativeControls: true,
	    success: function (me) {
			me.addEventListener('playing', function () {
				var hidder = $(me).closest('.hidder');
				hidder.addClass("wide");
				
			});
			me.addEventListener('pause', function () {
				var hidder = $(me).closest('.hidder');
				hidder.removeClass("wide");
			});
		}
	});
});