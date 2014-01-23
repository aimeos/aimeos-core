/*
This file is part of Ext JS 3.4

Copyright (c) 2011-2013 Sencha Inc

Contact:  http://www.sencha.com/contact

GNU General Public License Usage
This file may be used under the terms of the GNU General Public License version 3.0 as
published by the Free Software Foundation and appearing in the file LICENSE included in the
packaging of this file.

Please review the following information to ensure the GNU General Public License version 3.0
requirements will be met: http://www.gnu.org/copyleft/gpl.html.

If you are unsure which license is appropriate for your use, please contact the sales department
at http://www.sencha.com/contact.

Build date: 2013-04-03 15:07:25
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
        var c = p.child('input[name="'+this.el.dom.name+'"]:checked', true);
        return c ? c.value : null;
    },

    /**
     * Sets either the checked/unchecked status of this Radio, or, if a string value
     * is passed, checks a sibling Radio of the same name whose value is the value specified.
     * @param value {String/Boolean} Checked value, or the value of the sibling radio button to check.
     * @return {Ext.form.Field} this
     */
    setValue : function(v){
    	var checkEl,
            els,
            radio;
    	if (typeof v == 'boolean') {
            Ext.form.Radio.superclass.setValue.call(this, v);
        } else if (this.rendered) {
            checkEl = this.getCheckEl();
            radio = checkEl.child('input[name="' + this.el.dom.name + '"][value="' + v + '"]', true);
            if(radio){
                Ext.getCmp(radio.id).setValue(true);
            }
        }
        if(this.rendered && this.checked){
            checkEl = checkEl || this.getCheckEl();
            els = this.getCheckEl().select('input[name="' + this.el.dom.name + '"]');
			els.each(function(el){
				if(el.dom.id != this.id){
					Ext.getCmp(el.dom.id).setValue(false);
				}
			}, this);
        }
        return this;
    },

    // private
    getCheckEl: function(){
        if(this.inGroup){
            return this.el.up('.x-form-radio-group');
        }
        return this.el.up('form') || Ext.getBody();
    }
});
Ext.reg('radio', Ext.form.Radio);
