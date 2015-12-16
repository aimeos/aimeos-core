/*!
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * Copyright (c) Aimeos (aimeos.org), 2015
 */

Ext.ns('MShop.panel.product');

MShop.panel.product.ItemUi = Ext.extend(MShop.panel.AbstractListItemUi, {

    siteidProperty : 'product.siteid',

    initComponent : function() {

        MShop.panel.AbstractListItemUi.prototype.setSiteCheck(this);

        this.items = [{
            xtype : 'tabpanel',
            activeTab : 0,
            border : false,
            itemId : 'MShop.panel.product.ItemUi',
            plugins : ['ux.itemregistry'],
            items : [{
                xtype : 'panel',
                title : MShop.I18n.dt('admin', 'Basic'),
                border : false,
                layout : 'hbox',
                layoutConfig : {
                    align : 'stretch'
                },
                itemId : 'MShop.panel.product.ItemUi.BasicPanel',
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
                            name : 'product.id'
                        }, {
                            xtype : 'MShop.elements.status.combo',
                            name : 'product.status'
                        }, {
                            xtype : 'combo',
                            fieldLabel : MShop.I18n.dt('admin', 'Type'),
                            name : 'product.typeid',
                            mode : 'local',
                            store : MShop.GlobalStoreMgr.get('Product_Type'),
                            displayField : 'product.type.label',
                            valueField : 'product.type.id',
                            forceSelection : true,
                            triggerAction : 'all',
                            allowBlank : false,
                            typeAhead : true,
                            listeners : {
                                'render' : {
                                    fn : function() {
                                        var record, index = this.store.find('product.type.code', 'default');
                                        if((record = this.store.getAt(index))) {
                                            this.setValue(record.id);
                                        }
                                    }
                                }
                            }
                        }, {
                            xtype : 'textfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Code'),
                            name : 'product.code',
                            allowBlank : false,
                            maxLength : 32,
                            regex : /^[^ \v\t\r\n\f]+$/,
                            emptyText : MShop.I18n.dt('admin', 'EAN, SKU or article number (required)')
                        }, {
                            xtype : 'textarea',
                            fieldLabel : MShop.I18n.dt('admin', 'Label'),
                            name : 'product.label',
                            allowBlank : false,
                            maxLength : 255,
                            emptyText : MShop.I18n.dt('admin', 'Internal name (required)')
                        }, {
                            xtype : 'datefield',
                            fieldLabel : MShop.I18n.dt('admin', 'Start date'),
                            name : 'product.datestart',
                            format : 'Y-m-d H:i:s',
                            emptyText : MShop.I18n.dt('admin', 'YYYY-MM-DD hh:mm:ss (optional)')
                        }, {
                            xtype : 'datefield',
                            fieldLabel : MShop.I18n.dt('admin', 'End date'),
                            name : 'product.dateend',
                            format : 'Y-m-d H:i:s',
                            emptyText : MShop.I18n.dt('admin', 'YYYY-MM-DD hh:mm:ss (optional)')
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Created'),
                            name : 'product.ctime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Last modified'),
                            name : 'product.mtime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Editor'),
                            name : 'product.editor'
                        }]
                    }]
                }, {
                    xtype : 'MShop.panel.configui',
                    layout : 'fit',
                    flex : 1,
                    data : (this.record ? this.record.get('product.config') : {})
                }]
            }]
        }];

        this.store.on('beforesave', this.onBeforeSave, this);

        MShop.panel.product.ItemUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        var label = this.record ? this.record.data['product.label'] : MShop.I18n.dt('admin', 'new');
        //#: Product item panel title with product label ({0}) and site code ({1)}
        var string = MShop.I18n.dt('admin', 'Product: {0} ({1})');
        this.setTitle(String.format(string, label, MShop.config.site["locale.site.label"]));

        MShop.panel.product.ItemUi.superclass.afterRender.apply(this, arguments);
    },

    onBeforeSave : function(store, data) {
        MShop.panel.product.ItemUi.superclass.onBeforeSave.call(this, store, data, {
            configname : 'product.config'
        });
    },

    onStoreWrite : function(store, action, result, transaction, rs) {

        var records = Ext.isArray(rs) ? rs : [rs];
        var ids = [];

        MShop.panel.product.ItemUi.superclass.onStoreWrite.apply(this, arguments);

        for( var i = 0; i < records.length; i++) {
            ids.push(records[i].id);
        }

        MShop.API.Product.finish(MShop.config.site["locale.site.code"], ids);
    }
});

Ext.reg('MShop.panel.product.itemui', MShop.panel.product.ItemUi);
