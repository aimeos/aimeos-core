/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.job');

MShop.panel.job.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {
    initComponent : function() {

        this.title = MShop.I18n.dt('admin', 'Job item details');

        this.items = [{
            xtype : 'tabpanel',
            activeTab : 0,
            border : false,
            itemId : 'MShop.panel.job.ItemUi',
            plugins : ['ux.itemregistry'],
            items : [{
                xtype : 'panel',
                title : MShop.I18n.dt('admin', 'Basic'),
                border : false,
                layout : 'hbox',
                layoutConfig : {
                    align : 'stretch'
                },
                itemId : 'MShop.panel.job.ItemUi.BasicPanel',
                plugins : ['ux.itemregistry'],
                defaults : {
                    bodyCssClass : this.readOnlyClass
                },
                items : [{
                    xtype : 'form',
                    title : MShop.I18n.dt('admin', 'Details'),
                    flex : 1,
                    ref : '../../mainForm',
                    autoScroll : true,
                    items : [{
                        xtype : 'fieldset',
                        style : 'padding-right: 25px;',
                        border : false,
                        labelAlign : 'top',
                        defaults : {
                            readOnly : this.fieldsReadOnly,
                            anchor : '100%'
                        },
                        items : [{
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'ID'),
                            name : 'job.id'
                        }, {
                            xtype : 'MShop.elements.status.combo',
                            name : 'job.status'
                        }, {
                            xtype : 'textfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Label'),
                            name : 'job.label',
                            allowBlank : false,
                            maxLength : 255,
                            emptyText : MShop.I18n.dt('admin', 'Internal name (required)')
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Method'),
                            name : 'job.method'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Created'),
                            name : 'job.ctime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Last modified'),
                            name : 'job.mtime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Editor'),
                            name : 'job.editor'
                        }]
                    }]
                }]
            }]
        }];

        MShop.panel.job.ItemUi.superclass.initComponent.call(this);
    }
});

Ext.reg('MShop.panel.job.itemui', MShop.panel.job.ItemUi);
