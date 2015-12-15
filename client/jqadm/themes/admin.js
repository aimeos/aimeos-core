Aimeos = {};

Aimeos.Common = {

	init : function() {
		
		Aimeos.Common.askDelete();
		Aimeos.Common.confirmDelete();
	},

	
	askDelete : function() {
		
		$(".glyphicon-trash").on("click", function(e) {
			$("#confirm-delete").modal('show', $(this));
			return false;
		});
	},
	
	
	confirmDelete : function() {
		
		$('#confirm-delete').on('show.bs.modal', function(e) {
			$('.btn-danger', this).attr('href', $(e.relatedTarget).attr('href'));
		});
	}
};



Aimeos.Filter = {

	promise : null,


	init : function() {

		this.promise = $.ajax($("body").data("url"), {
			"method": "OPTIONS",
			"dataType": "json"
		});
		
		Aimeos.Filter.addFilterKeys();
		Aimeos.Filter.addFilterItem();
		Aimeos.Filter.removeFilterItem();
		Aimeos.Filter.toggleSearchItems();
	},
		

	addKeys : function(e) {
		var that = $(this);
		var opitem = that.parents(".filter-item").find(".filter-operator");
		
		if( $("option", that).length != 0 ) {
			return;
		}
		
		Aimeos.Filter.promise.done(function(data) {
			var code = that.data("selected");
			var type = data.meta.attributes[code].type;

			$.each(data.meta && data.meta.attributes || {}, function(key, attr) {
				if( attr.public && attr.code === code ) {
					that.append('<option value=' + attr.code + ' selected="selected">' + attr.label + '</option>');
				} else if( attr.public ) {
					that.append('<option value=' + attr.code + '>' + attr.label + '</option>');
				}
			});
			
			$("option." + type, opitem).show();
			that.selectmenu("refresh");
		});
	},
	
	
	selectKeys : function(e, ui) {

		var opitem = $(this).parents(".filter-item").find(".filter-operator");
		$("option", opitem).hide().removeProp("selected");

		Aimeos.Filter.promise.done(function(data) {

			if( data.meta && data.meta.attributes && data.meta.attributes[ui.item.value] && data.meta.attributes[ui.item.value].type ) {
				$("option." + data.meta.attributes[ui.item.value].type, opitem).show();
			}
		});
	},

	
	addFilterKeys : function() {

		$( ".aimeos .filter-item .filter-key" ).selectmenu({
			select: Aimeos.Filter.selectKeys,
			create: Aimeos.Filter.addKeys
		});
	},

	
	addFilterItem : function() {

		$(".aimeos .filter-items").on("click", ".glyphicon-plus", function(e) {
			var proto = $(".prototype", e.delegateTarget);
			var clone = proto.clone().insertBefore(proto);
	
			clone.removeClass("prototype").addClass("filter-item");
			$(this).removeClass("glyphicon-plus").addClass("glyphicon-minus");
	
			$(":disabled", clone).removeProp("disabled");
			$(".filter-key", clone).selectmenu({
				select: Aimeos.Filter.selectKeys,
				create: Aimeos.Filter.addKeys
			});
		});
	},


	removeFilterItem : function() {

		$(".aimeos .list-filter .filter-items").on("click", ".glyphicon-minus", function(e) {
			var item = $(this).parents(".filter-item");
			
			item.find(".filter-key").selectmenu("destroy");
			item.remove();
		});
	},
	
	
	toggleSearchItems : function() {
		
		$(".aimeos .list-filter, .aimeos .list-fields").on("click", ".action", function(e) {

			$(".filter-items, .fields-items", e.delegateTarget).toggle();
			$(this).toggleClass("action-close");

			if( $(".aimeos .list-search .search-item:visible").length > 0 ) {
				$(".aimeos .list-search .search-actions").show();
			} else {
				$(".aimeos .list-search .search-actions").hide();
			}
		});
	},
};



Aimeos.Item = {

	init : function() {
		
		Aimeos.Item.addConfigLine();
		Aimeos.Item.deleteConfigLine();
		Aimeos.Item.setupConfigComplete();
		Aimeos.Item.createDatePicker();
		Aimeos.Item.checkMandatory();
	},
	
	
	addConfigLine : function() {
		
		$(".aimeos .item-config").on("click", ".glyphicon-plus", function(ev) {
			var line = $(".prototype", ev.delegateTarget);
			var clone = line.clone();
			
			$("input", clone).removeProp("disabled");

			clone.insertBefore(line).removeClass("prototype");
			$(".config-key", clone).autocomplete({
				source: ['css-class'],
				minLength: 0,
				delay: 0
			});
		});
	},
	
	
	deleteConfigLine : function() {
		
		$(".aimeos .item-config .glyphicon-trash").on("click", function(ev) {
			$(this).parents("tr").remove();
		});

		$(".aimeos .item-config").on("click", ".glyphicon-trash", function(ev) {
			$(this).parents("tr").remove();
		});
	},
	
	
	setupConfigComplete : function() {
		
		$(".aimeos .config-item .config-key").autocomplete({
			source: ['css-class'],
			minLength: 0,
			delay: 0
		});

		$(".aimeos .item").on("click", " .config-key", function(ev) {
			$(this).autocomplete("search", "");
		});
	},
	

	checkMandatory : function() {
		
		$(".aimeos .item .mandatory").on("blur", "input,select", function(ev) {

			if($(this).val() != '') {
				$(ev.delegateTarget).removeClass("has-error").addClass("has-success");
			} else {
				$(ev.delegateTarget).removeClass("has-success").addClass("has-error");
			}
		});


		$(".aimeos form").on("submit", function(ev) {
			var retval = true;
			var nodes = [];

			$(".mandatory", this).each(function(idx, element) {

				var elem = $(element);
				var value = elem.find("input,select").val();

				if(value === null || value.trim() === "") {
					elem.addClass("has-error");
					nodes.push(element);
					retval = false;
				} else {
					elem.removeClass("has-error");
				}
			});

			$(".panel", this).removeClass("has-error");
			$.each(nodes, function(idx, node) {
				$(node).parents(".panel").find(".panel-heading").addClass("has-error");
			});

			if( nodes.length !== 0 ) {
				$('html, body').animate({
					scrollTop: $(nodes).first().offset().top + 'px'
				});
			}

			return retval;
		});
	},
	
	
	createDatePicker : function() {

		$(".aimeos .date").each(function(idx, elem) {
			
			$(elem).datepicker({
				dateFormat: $(elem).data("format"),
				constrainInput: false
			});
		});
	}
};



$(function() {
	
	Aimeos.Common.init();
	Aimeos.Filter.init();
	Aimeos.Item.init();



	$( ".aimeos .combobox" ).selectmenu({width: '100%'});
	$( ".aimeos .selectmenu" ).selectmenu({width: '100%'});
	$( ".aimeos .product-text .selectmenu" ).selectmenu({width: '8em'});

	$("#product-type-id").on( "selectmenuchange", function(ev, ui) {
		if( ui.item.element[0] ) {
			$('.product-type').hide();
			$('.product-' + $(ui.item.element[0]).data("code")).show();
		}
	});
	  
	$(".aimeos .product-stock").on("click", ".glyphicon-plus", function(ev) {
		var line = $(".prototype", ev.delegateTarget);
		var clone = line.clone();
		clone.insertBefore(line).removeClass("prototype");
		$(".combobox-prototype", clone).selectmenu({width: '100%'});
		$(".date-prototype", clone).datepicker({dateFormat: "yy-mm-dd"});
	});

	$(".aimeos .product-stock").on("click", ".glyphicon-trash", function() {
		var elem = $(this);
		$("#confirm-delete").modal();
		$("#confirm-delete").on('click', ".btn-danger", function(e) {
			$(e.delegateTarget).modal('hide');
			elem.parents("tr").remove();
		});
	});

	$(".aimeos .product-selection").on("click", ".panel-heading .glyphicon-duplicate", function(ev) {
		var panel = $(this).parents(".product-selection-item");
		var clone = panel.clone().insertAfter(panel);
		$(".product-id", clone).text('');
		$(".panel-name", clone).text('New');

		$('.product-selection-attributes .combobox', clone).attr('style', '').next('span').remove();
		$('.product-selection-attributes .combobox', clone).selectmenu({width: '100%'});
	});
	  
	$(".aimeos .product-selection").on("click", ".panel-heading .glyphicon-trash", function() {
		var elem = $(this);
		$("#confirm-delete").modal();
		$("#confirm-delete").on('click', ".btn-danger", function(e) {
			$(e.delegateTarget).modal('hide');
			elem.parents(".product-selection-item").remove();
		});
	});

	$(".aimeos .product-selection").on("click", ".product-selection-attributes .glyphicon-plus", function(ev) {
		var line = $(".prototype", $(this).parents(".product-selection-attributes"));
		var clone = line.clone();
		clone.insertBefore(line).removeClass("prototype");
		$(".combobox-prototype", clone).selectmenu().removeClass("combobox-prototype").addClass("combobox");
	});
	  
	$(".aimeos .product-selection").on("click", ".product-selection-attributes .glyphicon-trash", function() {
		var elem = $(this);
		$("#confirm-delete").modal();
		$("#confirm-delete").on('click', ".btn-danger", function(e) {
			$(e.delegateTarget).modal('hide');
			elem.parents("tr").remove();
		});
	});

	$(".aimeos .product-bundles").on("click", ".glyphicon-plus", function(ev) {
		var line = $(".prototype", ev.delegateTarget);
		var clone = line.clone();
		clone.insertBefore(line).removeClass("prototype");
		$(".combobox-prototype", clone).selectmenu();
	});
	  
	$(".aimeos .product-bundles").on("click", ".glyphicon-trash", function() {
		var elem = $(this);
		$("#confirm-delete").modal();
		$("#confirm-delete").on('click', ".btn-danger", function(e) {
			$(e.delegateTarget).modal('hide');
			elem.parents("tr").remove();
		});
	});
	  
	$("#product-text-data").on("click", ".glyphicon-duplicate", function(ev) {
		var panel = $(this).parents(".product-text-language");
		var clone = panel.clone();
		$('.selectmenu', clone).attr('style', '').next('span').remove();
		$('.selectmenu', clone).selectmenu({width: '8em'});
		clone.insertAfter(panel);
	});
	  
	$("#product-text-data").on("click", ".glyphicon-trash", function() {
		var elem = $(this);
		$("#confirm-delete").modal();
		$("#confirm-delete").on('click', ".btn-danger", function(e) {
			$(e.delegateTarget).modal('hide');
			elem.parents(".product-text-language").remove();
		});
	});
	  
/*	  $("textarea.htmleditor").ckeditor({
		toolbar: [
			{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'insert', items: [ 'Image', 'SpecialChar' ] },
			{ name: 'tools', items: [ 'Maximize' ] },
			{ name: 'document', items: [ 'Source' ] },
			'/',
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] }
		]
	  });
*/  
	$(".aimeos .product-image").on("change", ".fileupload", function(ev) {
		$(this).each( function(idx, el) {
			$(".upload", ev.delegateTarget).remove();
		    var line = $(".prototype", ev.delegateTarget);
			
		    for(i=0; i<el.files.length; i++) {
		    	var file = el.files[i];
		    	var clone = line.clone();
		    	clone.insertBefore(line).removeClass("prototype").addClass("upload");
			  
		    	$(".selectmenu-prototype", clone).selectmenu({width: '100%'});
		    	$(".product-image-label input", clone).val(el.files[i].name);
		    	var img = $(".product-image-preview img", clone).get(0);
		    	img.file = file;

		    	var reader = new FileReader();
		    	reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
		    	reader.readAsDataURL(file);
		    }
		});
	});
	  
	$(".aimeos .product-image").on("click", ".glyphicon-trash", function() {
		var elem = $(this);
		$("#confirm-delete").modal();

		$("#confirm-delete").on('click', ".btn-danger", function(e) {
			$(e.delegateTarget).modal('hide');
		  	elem.parents("tr").remove();
		});
	});

});
