/*
 * backgroundSize: A jQuery cssHook adding support for "cover" and "contain" to IE6,7,8
 *
 * Requirements:
 * - jQuery 1.7.0+
 *
 * latest version and complete README available on Github:
 * https://github.com/louisremi/jquery.backgroundSize.js
 *
 * Copyright 2012 @louis_remi
 * Licensed under the MIT license.
 *
 * This saved you an hour of work?
 * Send me music http://www.amazon.co.uk/wishlist/HNTU0468LQON
 */
(function(e,t,n,r,i){var s=e("<div>")[0],o=/url\(["']?(.*?)["']?\)/,u=[],a={top:0,left:0,bottom:1,right:1,center:.5};if("backgroundSize"in s.style&&!e.debugBGS){return}e.cssHooks.backgroundSize={set:function(t,n){var r=!e.data(t,"bgsImg"),i,s,o;e.data(t,"bgsValue",n);if(r){u.push(t);e.refreshBackgroundDimensions(t,true);s=e("<div>").css({position:"absolute",zIndex:-1,top:0,right:0,left:0,bottom:0,overflow:"hidden"});o=e("<img>").css({position:"absolute"}).appendTo(s),s.prependTo(t);e.data(t,"bgsImg",o[0]);i=(e.css(t,"backgroundPosition")||e.css(t,"backgroundPositionX")+" "+e.css(t,"backgroundPositionY")).split(" ");e.data(t,"bgsPos",[a[i[0]]||parseFloat(i[0])/100,a[i[1]]||parseFloat(i[1])/100]);e.css(t,"zIndex")=="auto"&&(t.style.zIndex=0);e.css(t,"position")=="static"&&(t.style.position="relative");e.refreshBackgroundImage(t)}else{e.refreshBackground(t)}},get:function(t){return e.data(t,"bgsValue")||""}};e.cssHooks.backgroundImage={set:function(t,n){return e.data(t,"bgsImg")?e.refreshBackgroundImage(t,n):n}};e.refreshBackgroundDimensions=function(t,n){var r=e(t),i={width:r.innerWidth(),height:r.innerHeight()},s=e.data(t,"bgsDim"),o=!s||i.width!=s.width||i.height!=s.height;e.data(t,"bgsDim",i);if(o&&!n){e.refreshBackground(t)}};e.refreshBackgroundImage=function(t,n){var r=e.data(t,"bgsImg"),i=(o.exec(n||e.css(t,"backgroundImage"))||[])[1],s=r&&r.src,u=i!=s,a,f;if(u){r.style.height=r.style.width="auto";r.onload=function(){var n={width:r.width,height:r.height};if(n.width==1&&n.height==1){return}e.data(t,"bgsImgDim",n);e.data(t,"bgsConstrain",false);e.refreshBackground(t);r.style.visibility="visible";r.onload=null};r.style.visibility="hidden";r.src=i;if(r.readyState||r.complete){r.src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";r.src=i}t.style.backgroundImage="none"}};e.refreshBackground=function(t){var n=e.data(t,"bgsValue"),i=e.data(t,"bgsDim"),s=e.data(t,"bgsImgDim"),o=e(e.data(t,"bgsImg")),u=e.data(t,"bgsPos"),a=e.data(t,"bgsConstrain"),f,l=i.width/i.height,c=s.width/s.height,h;if(n=="contain"){if(c>l){e.data(t,"bgsConstrain",f="width");h=r.floor((i.height-i.width/c)*u[1]);o.css({top:h});if(f!=a){o.css({width:"100%",height:"auto",left:0})}}else{e.data(t,"bgsConstrain",f="height");h=r.floor((i.width-i.height*c)*u[0]);o.css({left:h});if(f!=a){o.css({height:"100%",width:"auto",top:0})}}}else if(n=="cover"){if(c>l){e.data(t,"bgsConstrain",f="height");h=r.floor((i.height*c-i.width)*u[0]);o.css({left:-h});if(f!=a){o.css({height:"100%",width:"auto",top:0})}}else{e.data(t,"bgsConstrain",f="width");h=r.floor((i.width/c-i.height)*u[1]);o.css({top:-h});if(f!=a){o.css({width:"100%",height:"auto",left:0})}}}};var f=e.event,l,c={_:0},h=0,p,d;l=f.special.throttledresize={setup:function(){e(this).on("resize",l.handler)},teardown:function(){e(this).off("resize",l.handler)},handler:function(t,n){var r=this,i=arguments;p=true;if(!d){e(c).animate(c,{duration:Infinity,step:function(){h++;if(h>l.threshold&&p||n){t.type="throttledresize";f.dispatch.apply(r,i);p=false;h=0}if(h>9){e(c).stop();d=false;h=0}}});d=true}},threshold:1};e(t).on("throttledresize",function(){e(u).each(function(){e.refreshBackgroundDimensions(this)})})})(jQuery,window,document,Math);



/*
 * Arcavias related Javascript code
 * 
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/*
 * CSS3 support for IE8
 */
document.createElement("nav");
document.createElement("section");
document.createElement("article");


/* Lazy product image loading in list view */
var arcaviasLazyLoader = (function() {
	var elements = $(".catalog-list-items .lazy-image, .catalog-list-promo .lazy-image");
	for( var i = 0; i < elements.length; i++ ) {
		var element = $(elements[i]);
        if( $(window).scrollTop() + $(window).height() + 2 * element.height() >= element.offset().top ) {
        	element.css( "background-image", "url('" + element.data( "src" ) + "')" );
        	element.removeClass( "lazy-image" );
        }
	}
});

arcaviasLazyLoader();


jQuery(document).ready( function($) {

	/*
	 * Catalog clients
	 */

	/* CSS3 "background-size: contain" support for IE8 */
	$(".catalog-list-items .media-item").css("background-size", "contain");
	$(".catalog-detail-image .item").css("background-size", "contain");

	
	/* Catalog filter */
	$(".catalog-filter form").on("submit", function( event ) {
		
		var result = true;

		$("input.value", this).each(function(i, input) {

			if( input.val() != '' && input.val().length < 3 ) {
	
				if( $(this).has(".search-hint").length === 0 ) {
	
					var node = $('<div/>', {html: input.data("hint"), class: "search-hint"});
					$(".catalog-filter-search", this).after(node);
	
					var pos = node.position();
					node.css("left", pos.left).css("top", pos.top);
					node.delay(3000).fadeOut(1000, function() {
						node.remove();
					});
				}
	
				result = false;
			}
		});

		return result;
	});

	$(".catalog-filter-tree li.cat-item").on("click", function() {
		$(".catalog-list").fadeTo( 1000, 0.5 );
	});

	$(".catalog-filter-attribute input.attr-item").on("click", function() {
		$(this).parents(".catalog-filter form").submit();
		$(".catalog-list").fadeTo( 1000, 0.5 );
	});

	/* Autocompleter for quick search */
	var arcaviasInputComplete = $( ".catalog-filter-search .value" );
	arcaviasInputComplete.autocomplete( {
		minLength: 3,
		delay: 200,
		source: function( req, add ) {
			var nameTerm = {};
			nameTerm[arcaviasInputComplete.attr( "name" )] = req.term;

			$.getJSON(
				arcaviasInputComplete.data( "url" ),
				nameTerm,
				function( data ) {
					var suggestions = [];

					$.each( data, function( i, val ) {
						suggestions.push( val.name );
					} );
			
					add( suggestions );
				}
			);
		},
		select: function( event, ui ) {
			arcaviasInputComplete.val( ui.item.value );
			$(event.target).parents(".catalog-filter form").submit();
		}
	} );


	/* Lazy product image loading in list view */
	arcaviasLazyLoader();
	$(window).bind("resize", arcaviasLazyLoader);
	$(window).bind("scroll", arcaviasLazyLoader);
	

	/* Add to basket without page reload */
	$(".catalog-detail-basket form").on("submit", function(event) {

		var overlay = $(document.createElement("div"));
		overlay.addClass("arcavias-overlay");
		overlay.fadeTo(1000, 0.5);
		$("body").append(overlay);

		$.post($(this).attr("action"), $(this).serialize(), function(data) {
			var doc = document.createElement("html");
			doc.innerHTML = data;
			
			var basket = $(document.createElement("div"));
			basket.addClass("arcavias-container");
			basket.append( $(".basket-standard", doc) );
			basket.fadeTo(400, 1.0);
			$("body").append(basket);
			
			var resize = function() {
				var jqwin = $(window);
				var left = (jqwin.width() - basket.outerWidth()) / 2;
				var top = (jqwin.height() - basket.outerHeight()) / 2;

				basket.css("left", (left>0 ? left : 0 ));
				basket.css("top", (top>0 ? top : 0 ));
			};
			
			$(window).on("resize", resize);
			resize();
		});

		return false;
	});

	
	/*
	 * Basket clients
	 */
	
	/* Update without page reload */
	$("body").on("submit", ".basket-standard form", function(event) {
		var form = $(this);

		$.post(form.attr("action"), form.serialize(), function(data) {
			var doc = document.createElement("html");
			doc.innerHTML = data;
			$(".basket-standard").html( $(".basket-standard", doc).html() );
		});

		return false;
	});

	/* Update quantity and delete without page reload */
	$("body").on("click", ".basket-standard .change", function(event) {

		$.post($(this).attr("href"), function(data) {
			var doc = document.createElement("html");
			doc.innerHTML = data;
			$(".basket-standard").html( $(".basket-standard", doc).html() );
		});

		return false;
	});

	/* Go back to underlying page */
	$("body").on("click", ".basket-standard .btn-back", function(event) {
		var container = $(".arcavias-container");
		var overlay = $(".arcavias-overlay");
		
		if( container.size() + overlay.size() > 0 ) {
			container.remove();
			overlay.remove();
			return false;
		}
	});
	
	
	/*
	 * Checkout clients
	 */

	/* Initial state: Hide form for new address if not selected */
	$(".checkout-standard-address .item-new[data-option!='null'] .form-list").hide();

	/* Initial state: Hide form fields if not delivery/payment option is not selected */
	$( ".checkout-standard-delivery,.checkout-standard-payment" ).find( ".form-list" ).hide();
	$( ".checkout-standard-delivery,.checkout-standard-payment" ).find( ".item-service" ).has( "input:checked" ).find( ".form-list" ).show();

	/* Address form slide up/down when selected */
	$( ".checkout-standard-address-billing .header input" ).bind( "click",
		function( event ) {
			$( ".checkout-standard-address-billing .form-list" ).slideUp( 400 );
			$( ".checkout-standard-address-billing .item-address" ).has( this ).find( ".form-list" ).slideDown( 400 );
		}
	);

	/* Address form slide up/down when selected */
	$( ".checkout-standard-address-delivery .header input" ).bind( "click",
		function( event ) {
			$( ".checkout-standard-address-delivery .form-list" ).slideUp( 400 );
			$( ".checkout-standard-address-delivery .item-address" ).has( this ).find( ".form-list" ).slideDown( 400 );
		}
	);

	/* Delivery/payment form slide up/down when selected */
	$( ".checkout-standard-delivery, .checkout-standard-payment .option" ).bind( "click",
		function( event ) {
			$( ".checkout-standard .form-list" ).slideUp( 400 );
			$( ".checkout-standard .item-service" ).has( this ).find( ".form-list" ).slideDown( 400 );
		}
	);
	
	/* Check for mandatory fields in all forms */
	$( ".checkout-standard form" ).on( "submit", function( event ) {
			var retval = true;
			$( ".checkout-standard .item-new, .item-service" )
				.has( ".header,label" ).has( "input:checked" ) // combining in one has() doesn't work
				.find( ".form-list .mandatory" )
				.each( function() {
					var value = $(this).find( "input,select" ).val();
					if( value == null || value.trim() === "" ) {
						$(this).addClass( "error" );
						retval = false;
					} else {
						$(this).removeClass( "error" );
					}
				} );
			return retval;
		}
	);
	
	/* Redirect to payment provider / confirm page when order has been created successfully */
	$( ".checkout-standard-order-payment > form" ).first().submit();
	$( ".checkout-standard-order-payment" ).first().each( function( index, element ) {
		var url = $(element).data( "url" );
		if( url ) { window.location = url; }
	});

});
