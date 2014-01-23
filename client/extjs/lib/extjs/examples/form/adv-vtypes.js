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
// Add the additional 'advanced' VTypes
Ext.apply(Ext.form.VTypes, {
    daterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
            return false;
        }
        if (field.startDateField) {
            var start = Ext.getCmp(field.startDateField);
            if (!start.maxValue || (date.getTime() != start.maxValue.getTime())) {
                start.setMaxValue(date);
                start.validate();
            }
        }
        else if (field.endDateField) {
            var end = Ext.getCmp(field.endDateField);
            if (!end.minValue || (date.getTime() != end.minValue.getTime())) {
                end.setMinValue(date);
                end.validate();
            }
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
        return true;
    },

    password : function(val, field) {
        if (field.initialPassField) {
            var pwd = Ext.getCmp(field.initialPassField);
            return (val == pwd.getValue());
        }
        return true;
    },

    passwordText : 'Passwords do not match'
});


Ext.onReady(function(){

    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    var bd = Ext.getBody();

    /*
     * ================  Date Range  =======================
     */

    var dr = new Ext.FormPanel({
      labelWidth: 125,
      frame: true,
      title: 'Date Range',
      bodyStyle:'padding:5px 5px 0',
      width: 350,
      defaults: {width: 175},
      defaultType: 'datefield',
      items: [{
        fieldLabel: 'Start Date',
        name: 'startdt',
        id: 'startdt',
        vtype: 'daterange',
        endDateField: 'enddt' // id of the end date field
      },{
        fieldLabel: 'End Date',
        name: 'enddt',
        id: 'enddt',
        vtype: 'daterange',
        startDateField: 'startdt' // id of the start date field
      }]
    });

    dr.render('dr');

    /*
     * ================  Password Verification =======================
     */

    var pwd = new Ext.FormPanel({
      labelWidth: 125,
      frame: true,
      title: 'Password Verification',
      bodyStyle:'padding:5px 5px 0',
      width: 350,
      defaults: {
        width: 175,
        inputType: 'password'
      },
      defaultType: 'textfield',
      items: [{
        fieldLabel: 'Password',
        name: 'pass',
        id: 'pass'
      },{
        fieldLabel: 'Confirm Password',
        name: 'pass-cfrm',
        vtype: 'password',
        initialPassField: 'pass' // id of the initial password field
      }]
    });

    pwd.render('pw');
});
