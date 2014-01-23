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
Ext.QuickTips.init();

Ext.onReady(function(){

    var fp = new Ext.FormPanel({
        id: 'status-form',
        renderTo: Ext.getBody(),
        labelWidth: 75,
        width: 350,
        buttonAlign: 'right',
        border: false,
        bodyStyle: 'padding:10px 10px 0;',
        defaults: {
            anchor: '95%',
            allowBlank: false,
            selectOnFocus: true,
            msgTarget: 'side'
        },
        items:[{
            xtype: 'textfield',
            fieldLabel: 'Name',
            blankText: 'Name is required'
        },{
            xtype: 'datefield',
            fieldLabel: 'Birthdate',
            blankText: 'Birthdate is required'
        }],
        buttons: [{
            text: 'Save',
            handler: function(){
                if(fp.getForm().isValid()){
                    var sb = Ext.getCmp('form-statusbar');
                    sb.showBusy('Saving form...');
                    fp.getEl().mask();
                    fp.getForm().submit({
                        url: 'fake.php',
                        success: function(){
                            sb.setStatus({
                                text:'Form saved!',
                                iconCls:'',
                                clear: true
                            });
                            fp.getEl().unmask();
                        }
                    });
                }
            }
        }]
    });

    new Ext.Panel({
        title: 'StatusBar with Integrated Form Validation',
        renderTo: Ext.getBody(),
        width: 350,
        autoHeight: true,
        layout: 'fit',
        items: fp,
        bbar: new Ext.ux.StatusBar({
            id: 'form-statusbar',
            defaultText: 'Ready',
            plugins: new Ext.ux.ValidationStatus({form:'status-form'})
        })
    });

});