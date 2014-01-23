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
 * @class Ext.form.RadioGroup
 * @extends Ext.form.CheckboxGroup
 * A grouping container for {@link Ext.form.Radio} controls.
 * @constructor
 * Creates a new RadioGroup
 * @param {Object} config Configuration options
 * @xtype radiogroup
 */
Ext.form.RadioGroup = Ext.extend(Ext.form.CheckboxGroup, {
    /**
     * @cfg {Array} items An Array of {@link Ext.form.Radio Radio}s or Radio config objects
     * to arrange in the group.
     */
    /**
     * @cfg {Boolean} allowBlank True to allow every item in the group to be blank (defaults to true).
     * If allowBlank = false and no items are selected at validation time, {@link @blankText} will
     * be used as the error text.
     */
    allowBlank : true,
    /**
     * @cfg {String} blankText Error text to display if the {@link #allowBlank} validation fails
     * (defaults to 'You must select one item in this group')
     */
    blankText : 'You must select one item in this group',
    
    // private
    defaultType : 'radio',
    
    // private
    groupCls : 'x-form-radio-group',
    
    /**
     * @event change
     * Fires when the state of a child radio changes.
     * @param {Ext.form.RadioGroup} this
     * @param {Ext.form.Radio} checked The checked radio
     */
    
    /**
     * Gets the selected {@link Ext.form.Radio} in the group, if it exists.
     * @return {Ext.form.Radio} The selected radio.
     */
    getValue : function(){
        var out = null;
        this.eachItem(function(item){
            if(item.checked){
                out = item;
                return false;
            }
        });
        return out;
    },
    
    /**
     * Sets the checked radio in the group.
     * @param {String/Ext.form.Radio} id The radio to check.
     * @param {Boolean} value The value to set the radio.
     * @return {Ext.form.RadioGroup} this
     */
    onSetValue : function(id, value){
        if(arguments.length > 1){
            var f = this.getBox(id);
            if(f){
                f.setValue(value);
                if(f.checked){
                    this.eachItem(function(item){
                        if (item !== f){
                            item.setValue(false);
                        }
                    });
                }
            }
        }else{
            this.setValueForItem(id);
        }
    },
    
    setValueForItem : function(val){
        val = String(val).split(',')[0];
        this.eachItem(function(item){
            item.setValue(val == item.inputValue);
        });
    },
    
    // private
    fireChecked : function(){
        if(!this.checkTask){
            this.checkTask = new Ext.util.DelayedTask(this.bufferChecked, this);
        }
        this.checkTask.delay(10);
    },
    
    // private
    bufferChecked : function(){
        var out = null;
        this.eachItem(function(item){
            if(item.checked){
                out = item;
                return false;
            }
        });
        this.fireEvent('change', this, out);
    },
    
    onDestroy : function(){
        if(this.checkTask){
            this.checkTask.cancel();
            this.checkTask = null;
        }
        Ext.form.RadioGroup.superclass.onDestroy.call(this);
    }

});

Ext.reg('radiogroup', Ext.form.RadioGroup);
