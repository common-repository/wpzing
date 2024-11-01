
zSticker = function(){

	return {

		init: function (minimized, autohide){
			var container = jQuery('#zWallStickerContent');

			jQuery('#zWallStickerHeader').click(
				function(event){
					event.preventDefault();
					if (container.is(':visible')){
						jQuery('#zWallStickerContent').css({
							'display':'none'
						});
						jQuery('#zWallStickerHeader a').removeClass('active zStickerHeaderArrowDown');
            jQuery('#zWallStickerHeader a').addClass('zStickerHeaderArrowUp');
						jQuery.cookie('zStickerEnable', false);
					} else {
						jQuery('#zWallStickerContent').css({
							'display':'block'
						});
            jQuery('#zWallStickerHeader a').removeClass('zStickerHeaderArrowUp');
						jQuery('#zWallStickerHeader a').addClass('active zStickerHeaderArrowDown');
            
						jQuery.cookie('zStickerEnable', true);
					}
				});
				
			if(minimized){
				if(container.is(':visible')){
					container.css({
						'display':'none'
					});
					jQuery('#zWallSticker #zWallStickerHeader a').removeClass('active zStickerHeaderArrowDown');
          jQuery('#zWallStickerHeader a').addClass('zStickerHeaderArrowUp');
				}
			}

			if(autohide){
				jQuery(window).resize(function(){
					if (zSticker.overlap()) {
						if(container.is(':visible')){
							container.css({
								'display':'none'
							});
							jQuery('#zWallSticker #zWallStickerHeader a').removeClass('active zStickerHeaderArrowDown');
              jQuery('#zWallStickerHeader a').addClass('zStickerHeaderArrowUp');
						}
					} else {
						if(!container.is(':visible')){
							container.css({
								'display':'block'
							});
              jQuery('#zWallStickerHeader a').removeClass('zStickerHeaderArrowUp')
							jQuery('#zWallSticker #zWallStickerHeader a').addClass('active zStickerHeaderArrowDown'); 
						}
					}
				});
			}

		},

		overlap: function(){
			var pos = jQuery('#primary').position()['left'];
			var width = jQuery('#primary').width();
			var spos = jQuery('#zWallSticker').position()['left'];
			var overlap = spos - pos - width;
			return overlap < 0;
		},

		follow: function(b){
			var s = document.createElement('script');
			s.src = api+'/followblog.js?b='+b;
			document.getElementsByTagName('head')[0].appendChild(s);
		}
	}
	
}();
