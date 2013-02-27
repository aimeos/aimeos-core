/*
 * backgroundSize: A jQuery cssHook adding support for "cover" and "contain" to IE6,7,8
 *
 * Requirements:
 * - jQuery 1.7.0+
 *
 * Limitations:
 * - doesn't work with multiple backgrounds (use the :after trick)
 * - doesn't work with the "4 values syntax" of background-position
 * - doesn't work with lengths in background-position (only percentages and keywords)
 * - doesn't work with "background-repeat: repeat;"
 * - doesn't work with non-default values of background-clip/origin/attachment/scroll
 * - you should still test your website in IE!
 *
 * latest version and complete README available on Github:
 * https://github.com/louisremi/jquery.backgroundSize.js
 *
 * Copyright 2012 @louis_remi
 * Licensed under the MIT license.
 *
 * This saved you an hour of work?
 * Send me music http://www.amazon.co.uk/wishlist/HNTU0468LQON
 *
 */
(function($,window,document,Math,undefined) {

var div = $( "<div>" )[0],
	rsrc = /url\(["']?(.*?)["']?\)/,
	watched = [],
	positions = {
		top: 0,
		left: 0,
		bottom: 1,
		right: 1,
		center: .5
	};

// feature detection
if ( "backgroundSize" in div.style && !$.debugBGS ) { return; }

$.cssHooks.backgroundSize = {
	set: function( elem, value ) {
		var firstTime = !$.data( elem, "bgsImg" ),
			pos,
			$wrapper, $img;

		$.data( elem, "bgsValue", value );

		if ( firstTime ) {
			// add this element to the 'watched' list so that it's updated on resize
			watched.push( elem );

			$.refreshBackgroundDimensions( elem, true );

			// create wrapper and img
			$wrapper = $( "<div>" ).css({
				position: "absolute",
				zIndex: -1,
				top: 0,
				right: 0,
				left: 0,
				bottom: 0,
				overflow: "hidden"
			});

			$img = $( "<img>" ).css({
				position: "absolute"
			}).appendTo( $wrapper ),

			$wrapper.prependTo( elem );

			$.data( elem, "bgsImg", $img[0] );

			pos = ( 
				// Firefox, Chrome (for debug)
				$.css( elem, "backgroundPosition" ) ||
				// IE8
				$.css( elem, "backgroundPositionX" ) + " " + $.css( elem, "backgroundPositionY" )
			).split(" ");

			// Only compatible with 1 or 2 percentage or keyword values,
			// Not yet compatible with length values and 4 values.
			$.data( elem, "bgsPos", [ 
				positions[ pos[0] ] || parseFloat( pos[0] ) / 100, 
				positions[ pos[1] ] || parseFloat( pos[1] ) / 100
			]);

			// This is the part where we mess with the existing DOM
			// to make sure that the background image is correctly zIndexed
			$.css( elem, "zIndex" ) == "auto" && ( elem.style.zIndex = 0 );
			$.css( elem, "position" ) == "static" && ( elem.style.position = "relative" );

			$.refreshBackgroundImage( elem );

		} else {
			$.refreshBackground( elem );
		}
	},

	get: function( elem ) {
		return $.data( elem, "bgsValue" ) || "";
	}
};

// The background should refresh automatically when changing the background-image
$.cssHooks.backgroundImage = {
	set: function( elem, value ) {
		// if the element has a backgroundSize, refresh its background
		return $.data( elem, "bgsImg") ?
			$.refreshBackgroundImage( elem, value ) :
			// otherwise set the background-image normally
			value;
	}
};

$.refreshBackgroundDimensions = function( elem, noBgRefresh ) {
	var $elem = $(elem),
		currDim = {
			width: $elem.innerWidth(),
			height: $elem.innerHeight()
		},
		prevDim = $.data( elem, "bgsDim" ),
		changed = !prevDim ||
			currDim.width != prevDim.width ||
			currDim.height != prevDim.height;

	$.data( elem, "bgsDim", currDim );

	if ( changed && !noBgRefresh ) {
		$.refreshBackground( elem );
	}
};

$.refreshBackgroundImage = function( elem, value ) {
	var img = $.data( elem, "bgsImg" ),
		currSrc = ( rsrc.exec( value || $.css( elem, "backgroundImage" ) ) || [] )[1],
		prevSrc = img && img.src,
		changed = currSrc != prevSrc,
		imgWidth, imgHeight;

	if ( changed ) {
		img.style.height = img.style.width = "auto";

		img.onload = function() {
			var dim = {
				width: img.width,
				height: img.height
			};

			// ignore onload on the proxy image
			if ( dim.width == 1 && dim.height == 1 ) { return; }

			$.data( elem, "bgsImgDim", dim );
			$.data( elem, "bgsConstrain", false );

			$.refreshBackground( elem );

			img.style.visibility = "visible";

			img.onload = null;
		};

		img.style.visibility = "hidden";
		img.src = currSrc;

		if ( img.readyState || img.complete ) {
			img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
			img.src = currSrc;
		}

		elem.style.backgroundImage = "none";
	}
};

$.refreshBackground = function( elem ) {
	var value = $.data( elem, "bgsValue" ),
		elemDim = $.data( elem, "bgsDim" ),
		imgDim = $.data( elem, "bgsImgDim" ),
		$img = $( $.data( elem, "bgsImg" ) ),
		pos = $.data( elem, "bgsPos" ),
		prevConstrain = $.data( elem, "bgsConstrain" ),
		currConstrain,
		elemRatio = elemDim.width / elemDim.height,
		imgRatio = imgDim.width / imgDim.height,
		delta;

	if ( value == "contain" ) {
		if ( imgRatio > elemRatio ) {
			$.data( elem, "bgsConstrain", ( currConstrain = "width" ) );

			delta = Math.floor( ( elemDim.height - elemDim.width / imgRatio ) * pos[1] );

			$img.css({
				top: delta
			});

			// when switchin from height to with constraint,
			// make sure to release contraint on height and reset left
			if ( currConstrain != prevConstrain ) {
				$img.css({
					width: "100%",
					height: "auto",
					left: 0
				});
			}

		} else {
			$.data( elem, "bgsConstrain", ( currConstrain = "height" ) );

			delta = Math.floor( ( elemDim.width - elemDim.height * imgRatio ) * pos[0] );

			$img.css({
				left: delta
			});

			if ( currConstrain != prevConstrain ) {
				$img.css({
					height: "100%",
					width: "auto",
					top: 0
				});
			}
		}

	} else if ( value == "cover" ) {
		if ( imgRatio > elemRatio ) {
			$.data( elem, "bgsConstrain", ( currConstrain = "height" ) );

			delta = Math.floor( ( elemDim.height * imgRatio - elemDim.width ) * pos[0] );

			$img.css({
				left: -delta
			});

			if ( currConstrain != prevConstrain ) {
				$img.css({
					height:"100%",
					width: "auto",
					top: 0
				});
			}

		} else {
			$.data( elem, "bgsConstrain", ( currConstrain = "width" ) );

			delta = Math.floor( ( elemDim.width / imgRatio - elemDim.height ) * pos[1] );

			$img.css({
				top: -delta
			});

			if ( currConstrain != prevConstrain ) {
				$img.css({
					width: "100%",
					height: "auto",
					left: 0
				});
			}
		}
	}
}

// Built-in throttledresize
var $event = $.event,
	$special,
	dummy = {_:0},
	frame = 0,
	wasResized, animRunning;

$special = $event.special.throttledresize = {
	setup: function() {
		$( this ).on( "resize", $special.handler );
	},
	teardown: function() {
		$( this ).off( "resize", $special.handler );
	},
	handler: function( event, execAsap ) {
		// Save the context
		var context = this,
			args = arguments;

		wasResized = true;

        if ( !animRunning ) {
        	$(dummy).animate(dummy, { duration: Infinity, step: function() {
	        	frame++;

	        	if ( frame > $special.threshold && wasResized || execAsap ) {
	        		// set correct event type
        			event.type = "throttledresize";
	        		$event.dispatch.apply( context, args );
	        		wasResized = false;
	        		frame = 0;
	        	}
	        	if ( frame > 9 ) {
	        		$(dummy).stop();
	        		animRunning = false;
	        		frame = 0;
	        	}
	        }});
	        animRunning = true;
        }
	},
	threshold: 1
};

// All backgrounds should refresh automatically when the window is resized
$(window).on("throttledresize", function() {
	$(watched).each(function() {
		$.refreshBackgroundDimensions( this );
	});
});

})(jQuery,window,document,Math);