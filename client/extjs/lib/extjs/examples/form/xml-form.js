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

    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    var fs = new Ext.FormPanel({
        frame: true,
        title:'XML Form',
        labelAlign: 'right',
        labelWidth: 85,
        width:340,
        waitMsgTarget: true,

        // configure how to read the XML Data
        reader : new Ext.data.XmlReader({
            record : 'contact',
            success: '@success'
        }, [
            {name: 'first', mapping:'name/first'}, // custom mapping
            {name: 'last', mapping:'name/last'},
            'company', 'email', 'state',
            {name: 'dob', type:'date', dateFormat:'m/d/Y'} // custom data types
        ]),

        // reusable eror reader class defined at the end of this file
        errorReader: new Ext.form.XmlErrorReader(),

        items: [
            new Ext.form.FieldSet({
                title: 'Contact Information',
                autoHeight: true,
                defaultType: 'textfield',
                items: [{
                        fieldLabel: 'First Name',
                        emptyText: 'First Name',
                        name: 'first',
                        width:190
                    }, {
                        fieldLabel: 'Last Name',
                        emptyText: 'Last Name',
                        name: 'last',
                        width:190
                    }, {
                        fieldLabel: 'Company',
                        name: 'company',
                        width:190
                    }, {
                        fieldLabel: 'Email',
                        name: 'email',
                        vtype:'email',
                        width:190
                    },

                    new Ext.form.ComboBox({
                        fieldLabel: 'State',
                        hiddenName:'state',
                        store: new Ext.data.ArrayStore({
                            fields: ['abbr', 'state'],
                            data : Ext.exampledata.states // from states.js
                        }),
                        valueField:'abbr',
                        displayField:'state',
                        typeAhead: true,
                        mode: 'local',
                        triggerAction: 'all',
                        emptyText:'Select a state...',
                        selectOnFocus:true,
                        width:190
                    }),

                    new Ext.form.DateField({
                        fieldLabel: 'Date of Birth',
                        name: 'dob',
                        width:190,
                        allowBlank:false
                    })
                ]
            })
        ]
    });

    // simple button add
    fs.addButton('Load', function(){
        fs.getForm().load({url:'xml-form.xml', waitMsg:'Loading'});
    });

    // explicit add
    var submit = fs.addButton({
        text: 'Submit',
        disabled:true,
        handler: function(){
            fs.getForm().submit({url:'xml-errors.xml', waitMsg:'Saving Data...', submitEmptyText: false});
        }
    });

    fs.render('form-ct');

    fs.on({
        actioncomplete: function(form, action){
            if(action.type == 'load'){
                submit.enable();
            }
        }
    });

});

// A reusable error reader class for XML forms
Ext.form.XmlErrorReader = function(){
    Ext.form.XmlErrorReader.superclass.constructor.call(this, {
            record : 'field',
            success: '@success'
        }, [
            'id', 'msg'
        ]
    );
};
Ext.extend(Ext.form.XmlErrorReader, Ext.data.XmlReader);