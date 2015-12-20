(function( $ ) {

    $.widget( "ai.combobox", {

        _create: function() {
            this.wrapper = $( "<span>" )
                .addClass( "ai-combobox" )
                .insertAfter( this.element );

            this.element.hide();
            this._createAutocomplete();
            this._createShowAll();
        },


        _createAutocomplete: function() {
            var selected = this.element.children( ":selected" ),
              value = selected.val() ? selected.text() : "",
              self = this;

            this.input = $( "<input>" )
                .appendTo( this.wrapper )
                .val( value )
                .attr( "title", "" )
                .addClass( "ai-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
                .autocomplete({
                    delay: 0,
                    minLength: 0,
                    source: $.proxy( this, "_source" ),
                    select: function(ev, ui) {
                        self.element.val(ui.item.value).find("input").val(ui.item.label);
                        ev.preventDefault();
                    },
                    focus: function(ev, ui) {
                        self.element.val(ui.item.value).next().find("input").val(ui.item.label);
                        ev.preventDefault();
                    }
                })
                .tooltip({
                    tooltipClass: "ui-state-highlight"
                });

            this._on( this.input, {
                autocompleteselect: function( event, ui ) {
                    ui.item.option.selected = true;
                    this._trigger( "select", event, {
                        item: ui.item.option
                    });
                },

                autocompletechange: "_removeInvalid"
            });
        },


        _createShowAll: function() {
            var input = this.input,
                wasOpen = false;

            $( '<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icons-only"><span class="ui-button-icon-primary ui-icon ui-icon-triangle-1-s"></span></button>' )
                .attr( "tabIndex", -1 )
                .appendTo( this.wrapper )
                .button()
                .removeClass( "ui-corner-all" )
                .addClass( "ai-combobox-toggle ui-corner-right" )
                .mousedown(function() {
                    wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                })
               .click(function(ev) {
                   ev.preventDefault();
                   input.focus();

                   // Close if already visible
                   if ( wasOpen ) {
                       return;
                   }

                   // Pass empty string as value to search for, displaying all results
                   input.autocomplete( "search", "" );
               });
        },


        _source: function( request, response ) {
            this.options.getfcn( request, response, this.element );
        },


        _removeInvalid: function( event, ui ) {

            // Selected an item, nothing to do
            if ( ui.item ) {
              return;
            }

            // Search for a match (case-insensitive)
		    var valueLowerCase = this.input.val().toLowerCase();
            var valid = false;

            this.element.children( "option" ).each(function() {
                if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                    this.selected = valid = true;
                    return false;
                }
            });

           // Found a match, nothing to do
           if ( valid ) {
              return;
           }

          // Remove invalid value
          this.input.val( "" );
          this.element.val( "" );
          this.input.autocomplete( "instance" ).term = "";
        },

        _destroy: function() {
            this.wrapper.remove();
            this.element.show();
        }
    });

})( jQuery );



Aimeos = {

	options : null,


	init : function() {

		this.options = $.ajax($("body").data("url"), {
			"method": "OPTIONS",
			"dataType": "json"
		});
	},


	getProducts : function( request, response, element ) {

        Aimeos.options.done(function(data) {

            $.ajax({
                dataType: "json",
                url: data.meta.resources['product'] || null,
                data: {
                    filter: {'&&': [{'=~': {'product.label': request.term}}]},
                    fields: {'product': 'product.label'},
                    sort: 'product.label'
                },
                success: function(result) {
                    var list = result.data || [];

                    $("option", element).remove();

                    response( list.map(function(obj) {

                        var opt = $("<option/>").attr("value", obj.id)
                            .text(obj.attributes['product.label'])
                            .appendTo(element);

                        return {
                            label: obj.attributes['product.label'] || null,
                            value: obj.id,
                            option: opt
                        };
                    }));
                }
            });
        });
    }
};



Aimeos.Filter = {

	init : function() {
		
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
		
		Aimeos.options.done(function(data) {
			var code = that.data("selected");

			$.each(data.meta && data.meta.attributes || {}, function(key, attr) {
				if( attr.public && attr.code === code ) {
					that.append('<option value=' + attr.code + ' selected="selected">' + attr.label + '</option>');
				} else if( attr.public ) {
					that.append('<option value=' + attr.code + '>' + attr.label + '</option>');
				}
			});

			if( code && data.meta && data.meta.attributes && data.meta.attributes[code] ) {
				$("option." + data.meta.attributes[code].type, opitem).show();
			}
			
			that.selectmenu("refresh");
		});
	},
	
	
	selectKeys : function(e, ui) {

		var opitem = $(this).parents(".filter-item").find(".filter-operator");
		$("option", opitem).hide().removeProp("selected");

		Aimeos.options.done(function(data) {

			if( data.meta && data.meta.attributes && data.meta.attributes[ui.item.value] && data.meta.attributes[ui.item.value].type ) {
				var options = $("option." + data.meta.attributes[ui.item.value].type, opitem)
				options.first().prop("selected", "selected");
				options.show();
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

		$(".aimeos .filter-items").on("click", ".fa-plus", function(e) {
			var proto = $(".prototype", e.delegateTarget);
			var clone = proto.clone().insertBefore(proto);
	
			$("input,select", clone).prop("disabled", false);
			clone.removeClass("prototype").addClass("filter-item");
			$(this).removeClass("fa-plus").addClass("fa-minus");
	
			$(".filter-key", clone).selectmenu({
				select: Aimeos.Filter.selectKeys,
				create: Aimeos.Filter.addKeys
			});
		});
	},


	removeFilterItem : function() {

		$(".aimeos .list-filter .filter-items").on("click", ".fa-minus", function(e) {
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
				$(".aimeos .list-search .actions-group").show();
			} else {
				$(".aimeos .list-search .actions-group").hide();
			}
		});
	},
};



Aimeos.List = {
		
	element : null,


	init : function() {

		Aimeos.List.askDelete();
		Aimeos.List.confirmDelete();
	},


	askDelete : function() {
		var self = this;

		$(".list-items").on("click", ".fa-trash", function(e) {
			$("#confirm-delete").modal("show", $(this));
			self.element = this;
			return false;
		});
	},


	confirmDelete : function() {
		var self = this;

		$("#confirm-delete").on("hide.bs.modal", function(e) {
			window.location.href = $(self.element).attr("href");
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
		
		$(".aimeos .item-config").on("click", ".fa-plus", function(ev) {
			var line = $(".prototype", ev.delegateTarget);
			var clone = line.clone();
			
			clone.insertBefore(line).removeClass("prototype");
			$("input", clone).prop("disabled", false);
			$(".config-key", clone).autocomplete({
				source: ['css-class'],
				minLength: 0,
				delay: 0
			});
		});
	},
	
	
	deleteConfigLine : function() {
		
		$(".aimeos .item-config .fa-trash").on("click", function(ev) {
			$(this).parents("tr").remove();
		});

		$(".aimeos .item-config").on("click", ".fa-trash", function(ev) {
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
				$(ev.delegateTarget).removeClass("has-danger").addClass("has-success");
			} else {
				$(ev.delegateTarget).removeClass("has-success").addClass("has-danger");
			}
		});


		$(".aimeos form").on("submit", function(ev) {
			var retval = true;
			var nodes = [];

			$(".card-header", this).removeClass("has-danger");

			$(".mandatory", this).each(function(idx, element) {
				var elem = $(element);
				var value = elem.find("input,select").val();

				if(value === null || value.trim() === "") {
					elem.addClass("has-danger");
					nodes.push(element);
					retval = false;
				} else {
					elem.removeClass("has-danger");
				}
			});

			$.each(nodes, function(idx, node) {
				$(node).parents(".card").find(".card-header").addClass("has-danger");
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



Aimeos.Item.Bundle = {

	init : function() {

		$(".product-item-bundle .combobox").combobox({getfcn: Aimeos.getProducts});

		Aimeos.Item.Bundle.addLine();
		Aimeos.Item.Bundle.removeLine();
	},


	addLine : function() {

		$(".product-item-bundle").on("click", ".fa-plus", function(ev) {
			var line = $(".prototype", ev.delegateTarget);
			var clone = line.clone();

			clone.insertBefore(line).removeClass("prototype");
			$(".combobox-prototype", clone)
				.removeClass("combobox-prototype")
				.addClass("combobox")
				.prop("disabled", false)
				.combobox({getfcn: Aimeos.getProducts});
		});
	},


	removeLine : function() {

		$(".product-item-bundle").on("click", ".fa-trash", function() {
			$(this).parents("tr").remove();
		});
	}
};



$(function() {
	
	Aimeos.init();
	Aimeos.List.init();
	Aimeos.Filter.init();

	Aimeos.Item.init();
	Aimeos.Item.Bundle.init();
});
