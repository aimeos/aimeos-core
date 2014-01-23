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
MultiLangDemo = function() {
    // get the selected language code parameter from url (if exists)
    var params = Ext.urlDecode(window.location.search.substring(1));
    Ext.form.Field.prototype.msgTarget = 'side';
                
    return {
        init: function() {
            Ext.QuickTips.init();
            
            /* Language chooser combobox  */
            var store = new Ext.data.ArrayStore({
                fields: ['code', 'language', 'charset'],
                data : Ext.exampledata.languages // from languages.js
            });
            var combo = new Ext.form.ComboBox({
                renderTo: 'languages',
                store: store,
                displayField:'language',
                typeAhead: true,
                mode: 'local',
                triggerAction: 'all',
                emptyText: 'Select a language...',
                selectOnFocus: true,
                onSelect: function(record) {
                    window.location.search = Ext.urlEncode({"lang":record.get("code"),"charset":record.get("charset")});
                }
            });
        
            if (params.lang) {
                // check if there's really a language with that language code
                record = store.data.find(function(item, key){
                    if (item.data.code == params.lang) {
                        return true;
                    }
                    return false;
                });
                // if language was found in store assign it as current value in combobox
                if (record) {
                    combo.setValue(record.data.language);
                }
            }            
            
            if (params.lang) {
                var url = String.format("../../src/locale/ext-lang-{0}.js", params.lang);
                
                Ext.Ajax.request({
                    url: url,
                    success: this.onSuccess,
                    failure: this.onFailure,
                    scope: this 
                });
            } else {
                this.setupDemo();
            }
        },
        onSuccess: function(response, opts) {
            eval(response.responseText);
            this.setupDemo();
        },
        onFailure: function() {
            Ext.Msg.alert('Failure', 'Failed to load locale file.');
            this.setupDemo();
        },
        setupDemo: function() {
            /* Email field */
            var emailfield = new Ext.FormPanel({
                renderTo: 'emailfield',
                labelWidth: 100, // label settings here cascade unless overridden
                frame: true,
                title: 'Email Field',
                bodyStyle: 'padding:5px 5px 0',
                width: 360,
                defaults: {width: 220},
                defaultType: 'textfield',
        
                items: [{
                    fieldLabel: 'Email',
                    name: 'email',
                    vtype: 'email'
                }]
            });
            
            /* Datepicker */
            var datefield = new Ext.FormPanel({
                renderTo: 'datefield',
                labelWidth: 100, // label settings here cascade unless overridden
                frame: true,
                title: 'Datepicker',
                bodyStyle: 'padding:5px 5px 0',
                width: 360,
                defaults: {width: 220},
                defaultType: 'datefield',
                items: [{
                    fieldLabel: 'Date',
                    name: 'date'
                }]
            });
            
            // shorthand alias
            var fm = Ext.form, Ed = Ext.grid.GridEditor;
        
            var monthArray = Date.monthNames.map(function (e) { return [e]; });
        
            var ds = new Ext.data.Store({
                proxy: new Ext.ux.data.PagingMemoryProxy(monthArray),
                reader: new Ext.data.ArrayReader({}, [
                    {name: 'month'}
                ])
            });
        
            var cm = new Ext.grid.ColumnModel([{
                header: "Months of the year",
                dataIndex: 'month',
                editor: new Ed(new fm.TextField({
                    allowBlank: false
                })),
                width: 240,
                defaultSortable: true
            }]);
        
            var grid = new Ext.grid.GridPanel({
                renderTo: 'grid',
                width: 360,
                height: 203,
                title:'Month Browser',
                store: ds,
                cm: cm,
                sm: new Ext.grid.RowSelectionModel({selectRow:Ext.emptyFn}),
            
                bbar: new Ext.PagingToolbar({
                        pageSize: 6,
                        store: ds,
                        displayInfo: true
                })
            });
            
            // trigger the data store load
            ds.load({params:{start:0, limit:6}});            
        }
    };
    
}();
Ext.onReady(MultiLangDemo.init, MultiLangDemo);