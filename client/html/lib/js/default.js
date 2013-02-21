$(function() {
	
	/**
	 * Catalog list: lazy image loading
	 */
	var loadImages = (function() { 
		var elements = $(".catalog-list-items .lazy-image");
		for( var i = 0; i < elements.length; i++ ) {
			var element = $(elements[i]);
	        if( $(window).scrollTop() + $(window).height() + 2 * element.height() >= element.offset().top ) {
	        	element.attr( 'style', 'background-image: url(\'' + element.data( 'src' ) + '\')' );
	        	element.removeClass( 'lazy-image' );
	        }
		}
	});
	
	$(window).bind( 'resize', loadImages );
	$(window).bind( 'scroll', loadImages );
	$(window).scroll();

	

	/**
	 * Catalog detail: image slider
	 */

	$(".catalog-detail-image .carousel").carouFredSel({
		responsive: false,
		circular: false,
		infinite: false,
		auto: false,
		prev: '#prev-image',
		next: '#next-image',
		items: {
			visible: 1
		},
		scroll: {
			fx: 'none'
		},
		swipe: true,
		mousewheel: true
	});

	$('.catalog-detail-image .thumbs').carouFredSel({
		responsive: false,
		circular: false,
		infinite: false,
		auto: false,
		prev: '#prev-thumbs',
		next: '#next-thumbs',
		items: {
			visible: {
				min: 2,
				max: 3
			}
		},
		swipe: true,
		mousewheel: true
	});

	$('.catalog-detail-image .thumbs a').mouseenter(function() {
		$('.catalog-detail-image .carousel').trigger('slideTo', '#' + this.href.split('#').pop() );
		$('.catalog-detail-image .thumbs a').removeClass('selected');
		$(this).addClass('selected');
		return false;
	});

});
