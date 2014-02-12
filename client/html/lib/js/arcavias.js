/* Modernizr 2.7.1 (Custom Build) | MIT & BSD
 * Build: http://modernizr.com/download/#-fontface-backgroundsize-borderimage-borderradius-boxshadow-flexbox-multiplebgs-opacity-rgba-textshadow-cssanimations-csscolumns-generatedcontent-cssgradients-cssreflections-csstransforms-csstransforms3d-csstransitions-audio-video-input-inputtypes-touch-printshiv-mq-teststyles-testprop-testallprops-prefixes-domprefixes
 */
;window.Modernizr=function(a,b,c){function A(a){i.cssText=a}function B(a,b){return A(m.join(a+";")+(b||""))}function C(a,b){return typeof a===b}function D(a,b){return!!~(""+a).indexOf(b)}function E(a,b){for(var d in a){var e=a[d];if(!D(e,"-")&&i[e]!==c)return b=="pfx"?e:!0}return!1}function F(a,b,d){for(var e in a){var f=b[a[e]];if(f!==c)return d===!1?a[e]:C(f,"function")?f.bind(d||b):f}return!1}function G(a,b,c){var d=a.charAt(0).toUpperCase()+a.slice(1),e=(a+" "+o.join(d+" ")+d).split(" ");return C(b,"string")||C(b,"undefined")?E(e,b):(e=(a+" "+p.join(d+" ")+d).split(" "),F(e,b,c))}function H(){e.input=function(c){for(var d=0,e=c.length;d<e;d++)s[c[d]]=c[d]in j;return s.list&&(s.list=!!b.createElement("datalist")&&!!a.HTMLDataListElement),s}("autocomplete autofocus list placeholder max min multiple pattern required step".split(" ")),e.inputtypes=function(a){for(var d=0,e,g,h,i=a.length;d<i;d++)j.setAttribute("type",g=a[d]),e=j.type!=="text",e&&(j.value=k,j.style.cssText="position:absolute;visibility:hidden;",/^range$/.test(g)&&j.style.WebkitAppearance!==c?(f.appendChild(j),h=b.defaultView,e=h.getComputedStyle&&h.getComputedStyle(j,null).WebkitAppearance!=="textfield"&&j.offsetHeight!==0,f.removeChild(j)):/^(search|tel)$/.test(g)||(/^(url|email)$/.test(g)?e=j.checkValidity&&j.checkValidity()===!1:e=j.value!=k)),r[a[d]]=!!e;return r}("search tel url email datetime date month week time datetime-local number range color".split(" "))}var d="2.7.1",e={},f=b.documentElement,g="modernizr",h=b.createElement(g),i=h.style,j=b.createElement("input"),k=":)",l={}.toString,m=" -webkit- -moz- -o- -ms- ".split(" "),n="Webkit Moz O ms",o=n.split(" "),p=n.toLowerCase().split(" "),q={},r={},s={},t=[],u=t.slice,v,w=function(a,c,d,e){var h,i,j,k,l=b.createElement("div"),m=b.body,n=m||b.createElement("body");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:g+(d+1),l.appendChild(j);return h=["&#173;",'<style id="s',g,'">',a,"</style>"].join(""),l.id=g,(m?l:n).innerHTML+=h,n.appendChild(l),m||(n.style.background="",n.style.overflow="hidden",k=f.style.overflow,f.style.overflow="hidden",f.appendChild(n)),i=c(l,a),m?l.parentNode.removeChild(l):(n.parentNode.removeChild(n),f.style.overflow=k),!!i},x=function(b){var c=a.matchMedia||a.msMatchMedia;if(c)return c(b).matches;var d;return w("@media "+b+" { #"+g+" { position: absolute; } }",function(b){d=(a.getComputedStyle?getComputedStyle(b,null):b.currentStyle)["position"]=="absolute"}),d},y={}.hasOwnProperty,z;!C(y,"undefined")&&!C(y.call,"undefined")?z=function(a,b){return y.call(a,b)}:z=function(a,b){return b in a&&C(a.constructor.prototype[b],"undefined")},Function.prototype.bind||(Function.prototype.bind=function(b){var c=this;if(typeof c!="function")throw new TypeError;var d=u.call(arguments,1),e=function(){if(this instanceof e){var a=function(){};a.prototype=c.prototype;var f=new a,g=c.apply(f,d.concat(u.call(arguments)));return Object(g)===g?g:f}return c.apply(b,d.concat(u.call(arguments)))};return e}),q.flexbox=function(){return G("flexWrap")},q.touch=function(){var c;return"ontouchstart"in a||a.DocumentTouch&&b instanceof DocumentTouch?c=!0:w(["@media (",m.join("touch-enabled),("),g,")","{#modernizr{top:9px;position:absolute}}"].join(""),function(a){c=a.offsetTop===9}),c},q.rgba=function(){return A("background-color:rgba(150,255,150,.5)"),D(i.backgroundColor,"rgba")},q.multiplebgs=function(){return A("background:url(https://),url(https://),red url(https://)"),/(url\s*\(.*?){3}/.test(i.background)},q.backgroundsize=function(){return G("backgroundSize")},q.borderimage=function(){return G("borderImage")},q.borderradius=function(){return G("borderRadius")},q.boxshadow=function(){return G("boxShadow")},q.textshadow=function(){return b.createElement("div").style.textShadow===""},q.opacity=function(){return B("opacity:.55"),/^0.55$/.test(i.opacity)},q.cssanimations=function(){return G("animationName")},q.csscolumns=function(){return G("columnCount")},q.cssgradients=function(){var a="background-image:",b="gradient(linear,left top,right bottom,from(#9f9),to(white));",c="linear-gradient(left top,#9f9, white);";return A((a+"-webkit- ".split(" ").join(b+a)+m.join(c+a)).slice(0,-a.length)),D(i.backgroundImage,"gradient")},q.cssreflections=function(){return G("boxReflect")},q.csstransforms=function(){return!!G("transform")},q.csstransforms3d=function(){var a=!!G("perspective");return a&&"webkitPerspective"in f.style&&w("@media (transform-3d),(-webkit-transform-3d){#modernizr{left:9px;position:absolute;height:3px;}}",function(b,c){a=b.offsetLeft===9&&b.offsetHeight===3}),a},q.csstransitions=function(){return G("transition")},q.fontface=function(){var a;return w('@font-face {font-family:"font";src:url("https://")}',function(c,d){var e=b.getElementById("smodernizr"),f=e.sheet||e.styleSheet,g=f?f.cssRules&&f.cssRules[0]?f.cssRules[0].cssText:f.cssText||"":"";a=/src/i.test(g)&&g.indexOf(d.split(" ")[0])===0}),a},q.generatedcontent=function(){var a;return w(["#",g,"{font:0/0 a}#",g,':after{content:"',k,'";visibility:hidden;font:3px/1 a}'].join(""),function(b){a=b.offsetHeight>=3}),a},q.video=function(){var a=b.createElement("video"),c=!1;try{if(c=!!a.canPlayType)c=new Boolean(c),c.ogg=a.canPlayType('video/ogg; codecs="theora"').replace(/^no$/,""),c.h264=a.canPlayType('video/mp4; codecs="avc1.42E01E"').replace(/^no$/,""),c.webm=a.canPlayType('video/webm; codecs="vp8, vorbis"').replace(/^no$/,"")}catch(d){}return c},q.audio=function(){var a=b.createElement("audio"),c=!1;try{if(c=!!a.canPlayType)c=new Boolean(c),c.ogg=a.canPlayType('audio/ogg; codecs="vorbis"').replace(/^no$/,""),c.mp3=a.canPlayType("audio/mpeg;").replace(/^no$/,""),c.wav=a.canPlayType('audio/wav; codecs="1"').replace(/^no$/,""),c.m4a=(a.canPlayType("audio/x-m4a;")||a.canPlayType("audio/aac;")).replace(/^no$/,"")}catch(d){}return c};for(var I in q)z(q,I)&&(v=I.toLowerCase(),e[v]=q[I](),t.push((e[v]?"":"no-")+v));return e.input||H(),e.addTest=function(a,b){if(typeof a=="object")for(var d in a)z(a,d)&&e.addTest(d,a[d]);else{a=a.toLowerCase();if(e[a]!==c)return e;b=typeof b=="function"?b():b,typeof enableClasses!="undefined"&&enableClasses&&(f.className+=" "+(b?"":"no-")+a),e[a]=b}return e},A(""),h=j=null,e._version=d,e._prefixes=m,e._domPrefixes=p,e._cssomPrefixes=o,e.mq=x,e.testProp=function(a){return E([a])},e.testAllProps=G,e.testStyles=w,e}(this,this.document),function(a,b){function l(a,b){var c=a.createElement("p"),d=a.getElementsByTagName("head")[0]||a.documentElement;return c.innerHTML="x<style>"+b+"</style>",d.insertBefore(c.lastChild,d.firstChild)}function m(){var a=s.elements;return typeof a=="string"?a.split(" "):a}function n(a){var b=j[a[h]];return b||(b={},i++,a[h]=i,j[i]=b),b}function o(a,c,d){c||(c=b);if(k)return c.createElement(a);d||(d=n(c));var g;return d.cache[a]?g=d.cache[a].cloneNode():f.test(a)?g=(d.cache[a]=d.createElem(a)).cloneNode():g=d.createElem(a),g.canHaveChildren&&!e.test(a)&&!g.tagUrn?d.frag.appendChild(g):g}function p(a,c){a||(a=b);if(k)return a.createDocumentFragment();c=c||n(a);var d=c.frag.cloneNode(),e=0,f=m(),g=f.length;for(;e<g;e++)d.createElement(f[e]);return d}function q(a,b){b.cache||(b.cache={},b.createElem=a.createElement,b.createFrag=a.createDocumentFragment,b.frag=b.createFrag()),a.createElement=function(c){return s.shivMethods?o(c,a,b):b.createElem(c)},a.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+m().join().replace(/\w+/g,function(a){return b.createElem(a),b.frag.createElement(a),'c("'+a+'")'})+");return n}")(s,b.frag)}function r(a){a||(a=b);var c=n(a);return s.shivCSS&&!g&&!c.hasCSS&&(c.hasCSS=!!l(a,"article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}")),k||q(a,c),a}function w(a){var b,c=a.getElementsByTagName("*"),d=c.length,e=RegExp("^(?:"+m().join("|")+")$","i"),f=[];while(d--)b=c[d],e.test(b.nodeName)&&f.push(b.applyElement(x(b)));return f}function x(a){var b,c=a.attributes,d=c.length,e=a.ownerDocument.createElement(u+":"+a.nodeName);while(d--)b=c[d],b.specified&&e.setAttribute(b.nodeName,b.nodeValue);return e.style.cssText=a.style.cssText,e}function y(a){var b,c=a.split("{"),d=c.length,e=RegExp("(^|[\\s,>+~])("+m().join("|")+")(?=[[\\s,>+~#.:]|$)","gi"),f="$1"+u+"\\:$2";while(d--)b=c[d]=c[d].split("}"),b[b.length-1]=b[b.length-1].replace(e,f),c[d]=b.join("}");return c.join("{")}function z(a){var b=a.length;while(b--)a[b].removeNode()}function A(a){function g(){clearTimeout(d._removeSheetTimer),b&&b.removeNode(!0),b=null}var b,c,d=n(a),e=a.namespaces,f=a.parentWindow;return!v||a.printShived?a:(typeof e[u]=="undefined"&&e.add(u),f.attachEvent("onbeforeprint",function(){g();var d,e,f,h=a.styleSheets,i=[],j=h.length,k=Array(j);while(j--)k[j]=h[j];while(f=k.pop())if(!f.disabled&&t.test(f.media)){try{d=f.imports,e=d.length}catch(m){e=0}for(j=0;j<e;j++)k.push(d[j]);try{i.push(f.cssText)}catch(m){}}i=y(i.reverse().join("")),c=w(a),b=l(a,i)}),f.attachEvent("onafterprint",function(){z(c),clearTimeout(d._removeSheetTimer),d._removeSheetTimer=setTimeout(g,500)}),a.printShived=!0,a)}var c="3.7.0",d=a.html5||{},e=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,f=/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,g,h="_html5shiv",i=0,j={},k;(function(){try{var a=b.createElement("a");a.innerHTML="<xyz></xyz>",g="hidden"in a,k=a.childNodes.length==1||function(){b.createElement("a");var a=b.createDocumentFragment();return typeof a.cloneNode=="undefined"||typeof a.createDocumentFragment=="undefined"||typeof a.createElement=="undefined"}()}catch(c){g=!0,k=!0}})();var s={elements:d.elements||"abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video",version:c,shivCSS:d.shivCSS!==!1,supportsUnknownElements:k,shivMethods:d.shivMethods!==!1,type:"default",shivDocument:r,createElement:o,createDocumentFragment:p};a.html5=s,r(b);var t=/^$|\b(?:all|print)\b/,u="html5shiv",v=!k&&function(){var c=b.documentElement;return typeof b.namespaces!="undefined"&&typeof b.parentWindow!="undefined"&&typeof c.applyElement!="undefined"&&typeof c.removeNode!="undefined"&&typeof a.attachEvent!="undefined"}();s.type+=" print",s.shivPrint=A,A(b)}(this,document);



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
		var input = $("input.value", this);

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

			return false;
		}

		return true;
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
			$(ui).parents(".catalog-filter form").submit();
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
