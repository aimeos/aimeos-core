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
 * @class Ext.form.DisplayField
 * @extends Ext.form.Field
 * A display-only text field which is not validated and not submitted.
 * @constructor
 * Creates a new DisplayField.
 * @param {Object} config Configuration options
 * @xtype displayfield
 */
Ext.form.DisplayField = Ext.extend(Ext.form.Field,  {
    validationEvent : false,
    validateOnBlur : false,
    defaultAutoCreate : {tag: "div"},
    /**
     * @cfg {String} fieldClass The default CSS class for the field (defaults to <tt>"x-form-display-field"</tt>)
     */
    fieldClass : "x-form-display-field",
    /**
     * @cfg {Boolean} htmlEncode <tt>false</tt> to skip HTML-encoding the text when rendering it (defaults to
     * <tt>false</tt>). This might be useful if you want to include tags in the field's innerHTML rather than
     * rendering them as string literals per the default logic.
     */
    htmlEncode: false,

    // private
    initEvents : Ext.emptyFn,

    isValid : function(){
        return true;
    },

    validate : function(){
        return true;
    },

    getRawValue : function(){
        var v = this.rendered ? this.el.dom.innerHTML : Ext.value(this.value, '');
        if(v === this.emptyText){
            v = '';
        }
        if(this.htmlEncode){
            v = Ext.util.Format.htmlDecode(v);
        }
        return v;
    },

    getValue : function(){
        return this.getRawValue();
    },
    
    getName: function() {
        return this.name;
    },

    setRawValue : function(v){
        if(this.htmlEncode){
            v = Ext.util.Format.htmlEncode(v);
        }
        return this.rendered ? (this.el.dom.innerHTML = (Ext.isEmpty(v) ? '' : v)) : (this.value = v);
    },

    setValue : function(v){
        this.setRawValue(v);
        return this;
    }
    /** 
     * @cfg {String} inputType 
     * @hide
     */
    /** 
     * @cfg {Boolean} disabled 
     * @hide
     */
    /** 
     * @cfg {Boolean} readOnly 
     * @hide
     */
    /** 
     * @cfg {Boolean} validateOnBlur 
     * @hide
     */
    /** 
     * @cfg {Number} validationDelay 
     * @hide
     */
    /** 
     * @cfg {String/Boolean} validationEvent 
     * @hide
     */
});

Ext.reg('displayfield', Ext.form.DisplayField);
