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
    var form = new Ext.form.FormPanel({
        width : 400,
        height: 160,
        title : 'Sound Settings',
        
        bodyStyle  : 'padding: 10px;',
        renderTo   : 'container',
        defaultType: 'sliderfield',
        buttonAlign: 'left',
        
        defaults: {
            anchor: '95%',
            tipText: function(thumb){
                return String(thumb.value) + '%';
            } 
        },
        items: [{
            fieldLabel: 'Sound Effects',
            value: 50,
            name: 'fx'
        },{
            fieldLabel: 'Ambient Sounds',
            value: 80,
            name: 'ambient'
        },{
            fieldLabel: 'Interface Sounds',
            value: 25,
            name: 'iface'
        }],
        fbar: {
            xtype: 'toolbar',
            items: [{
                text: 'Max All',
                handler: function(){
                    form.items.each(function(c){
                        c.setValue(100);
                    });
                }
            }, '->', {
                text: 'Save',
                handler: function(){
                    var values = form.getForm().getValues(),
                        s = ['Sounds Effects: <b>{0}%</b>',
                            'Ambient Sounds: <b>{1}%</b>',
                            'Interface Sounds: <b>{2}%</b>'];
                            
                    Ext.example.msg('Settings Saved', s.join('<br />'), values.fx, values.ambient, values.iface);
                }
            },{
                text: 'Reset',
                handler: function(){
                    form.getForm().reset();
                }
            }]
        }
    });
});
