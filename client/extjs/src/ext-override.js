/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ext-override.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


/**
 * don't evaluate to dot (.) as obj seperator when creating accessors, 
 * to support dot's in fieldnames
 */
Ext.apply(Ext.data.JsonReader.prototype, {
    createAccessor : function(){
        var re = /[\[]/;
        return function(expr) {
            if(Ext.isEmpty(expr)){
                return Ext.emptyFn;
            }
            if(Ext.isFunction(expr)){
                return expr;
            }
            var i = String(expr).search(re);
            if(i >= 0){
                return new Function('obj', 'return obj' + (i > 0 ? '.' : '') + expr);
            }
            return function(obj){
                return obj[expr];
            };

        };
    }()
});

/**
 * - support dot's in fieldnames
 * - generic status renderer
 */
Ext.form.ComboBox.prototype.initList = Ext.form.ComboBox.prototype.initList.createInterceptor(function() {
    // autodetect status fieldname
    if (! this.statusField) {
        this.store.fields.each(function(field) {
            if (field.name.match(/\.status$/)) {
                this.statusField = field.name;
            }
        }, this);
    }

    this.tpl = '<tpl for="."><div class="x-combo-list-item statustext-{[values["' + this.statusField + '"]]}">{[values["' + this.displayField + '"]]}</div></tpl>';
});


Ext.util.JSON.encodeDate = function( o ) {

	pad = function( n ) {
		return n < 10 ? "0" + n : n;
	};

	return '"' + o.getFullYear() + "-" +
    	pad(o.getMonth() + 1) + "-" +
    	pad(o.getDate()) + " " +
    	pad(o.getHours()) + ":" +
    	pad(o.getMinutes()) + ":" +
    	pad(o.getSeconds()) + '"';
};
