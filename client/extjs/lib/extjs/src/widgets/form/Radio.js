/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.form.Radio
 * @extends Ext.form.Checkbox
 * Single radio field.  Same as Checkbox, but provided as a convenience for automatically setting the input type.
 * Radio grouping is handled automatically by the browser if you give each radio in a group the same name.
 * @constructor
 * Creates a new Radio
 * @param {Object} config Configuration options
 * @xtype radio
 */
Ext.form.Radio = Ext.extend(Ext.form.Checkbox, {
    inputType: 'radio',

    /**
     * Overridden and disabled. The editor element does not support standard valid/invalid marking. @hide
     * @method
     */
    markInvalid : Ext.emptyFn,
    /**
     * Overridden and disabled. The editor element does not support standard valid/invalid marking. @hide
     * @method
     */
    clearInvalid : Ext.emptyFn,

    /**
     * If this radio is part of a group, it will return the selected value
     * @return {String}
     */
    getGroupValue : function(){
    	var p = this.el.up('form') || Ext.getBody();
        var c = p.child('input[name='+this.el.dom.name+']:checked', true);
        return c ? c.value : null;
    },

    // private
    onClick : function(){
    	if(this.el.dom.checked != this.checked){
			var els = this.getCheckEl().select('input[name=' + this.el.dom.name + ']');
			els.each(function(el){
				if(el.dom.id == this.id){
					this.setValue(true);
				}else{
					Ext.getCmp(el.dom.id).setValue(false);
				}
			}, this);
		}
    },

    /**
     * Sets either the checked/unchecked status of this Radio, or, if a string value
     * is passed, checks a sibling Radio of the same name whose value is the value specified.
     * @param value {String/Boolean} Checked value, or the value of the sibling radio button to check.
     * @return {Ext.form.Field} this
     */
    setValue : function(v){
    	if (typeof v == 'boolean') {
            Ext.form.Radio.superclass.setValue.call(this, v);
        } else if (this.rendered) {
            var r = this.getCheckEl().child('input[name=' + this.el.dom.name + '][value=' + v + ']', true);
            if(r){
                Ext.getCmp(r.id).setValue(true);
            }
        }
        return this;
    },

    // private
    getCheckEl: function(){
        if(this.inGroup){
            return this.el.up('.x-form-radio-group')
        }
        return this.el.up('form') || Ext.getBody();
    }
});
Ext.reg('radio', Ext.form.Radio);
