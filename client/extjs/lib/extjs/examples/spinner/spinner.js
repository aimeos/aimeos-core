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
Ext.onReady(function(){
    var simple = new Ext.FormPanel({
        labelWidth: 40, // label settings here cascade unless overridden
        frame: true,
        title: 'Simple Form',
        bodyStyle: 'padding:5px 5px 0',
        width: 210,
        defaults: {width: 135},
        defaultType: 'textfield',

        items: [
            new Ext.ux.form.SpinnerField({
                fieldLabel: 'Age',
                name: 'age'
            }),
            {
            	xtype: 'spinnerfield',
            	fieldLabel: 'Test',
            	name: 'test',
            	minValue: 0,
            	maxValue: 100,
            	allowDecimals: true,
            	decimalPrecision: 1,
            	incrementValue: 0.4,
            	alternateIncrementValue: 2.1,
            	accelerate: true
            }
        ]
    });

    simple.render('form-ct');
});
