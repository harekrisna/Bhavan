
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
		    // přičtení počtu přehrání
			me.addEventListener('playing', function () {
				var hidder = $(me).closest('.hidder');
				hidder.addClass('wide');
				var audio_id = $(me).data('id');
				$.get('increase-mp3-playcount/' + audio_id, 
                    function(payload) {
	                    redraw_playcount_title(me, payload.playcount)
                    }
                );				
			});
			me.addEventListener('pause', function () {
				var hidder = $(me).closest('.hidder');
				hidder.removeClass("wide");
				
				redraw_playcount_title(me, $(me).data('playcount'));
			});
		}
	});
	$('section.lectures-group audio').each(function(index) {
		redraw_playcount_title(this, $(this).data('playcount'));
	});
	
	// přičtení počtu stažení
	$('div.audio-player .download-link').click(function() {
		var audio_player = $(this).closest('div.audio-player');
		var	audio = $(audio_player).find('audio');
		var audio_id = $(audio).data('id');
		var download_link = $(this);	
		
		$.get('increase-mp3-download/' + audio_id, 
            function(payload) {
	            var downloadcount = payload.downloadcount;
	            if(downloadcount == "") downloadcount = 0;
				$(download_link).attr('title', 'Staženo: ' + downloadcount);
	            window.location.href = $(download_link).data('href');
            }
        );
        
        
	});
});

function redraw_playcount_title(me, playcount) {
	if(playcount == "") playcount = 0;
	var player_bar = $(me).closest('div.mejs-inner');
	var playpause_button = $(player_bar).find('div.mejs-playpause-button button');
	$(playpause_button).attr('title', 'Přehráno: ' + playcount);	
}


