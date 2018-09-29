/* DOKUWIKI:include_once vendor/slick/slick.min.js */
/* DOKUWIKI:include_once vendor/swipebox/js/jquery.swipebox.js */

jQuery(function(){
	jQuery(".slick").slick();
	
	jQuery.expr[':'].onlydirect = function(elem, index, meta) {
		 return (
				  new RegExp('fetch.php', 'i').test(jQuery(elem).attr('href')) ||
				  new RegExp('_media', 'i').test(jQuery(elem).attr('href'))
		);
	};

		
	jQuery(".slick a.media:onlydirect()").swipebox({
        loopAtEnd: true
    });
});
