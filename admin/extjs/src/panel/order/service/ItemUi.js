/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.order.service');

MShop.panel.order.service.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

    maximized : true,
    layout : 'fit',
    modal : true,
    siteidProperty : 'order.base.service.siteid',


    initComponent : function() {

        this.title = MShop.I18n.dt('admin', 'Service item details');

        MShop.panel.AbstractItemUi.prototype.setSiteCheck(this);

        this.items = [{
            xtype : 'tabpanel',
            activeTab : 0,
            border : false,
            itemId : 'MShop.panel.order.service.ItemUi',
            plugins : ['ux.itemregistry'],
            items : [{
                xtype : 'panel',
                title : MShop.I18n.dt('admin', 'Service'),
                border : false,
                layout : 'hbox',
                layoutConfig : {
                    align : 'stretch'
                },
                itemId : 'MShop.panel.order.service.ItemUi.BasicPanel',
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
                        labelAlign : 'left',
                        defaults : {
                            readOnly : this.fieldsReadOnly,
                            anchor : '100%'
                        },
                        items : [{
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'ID'),
                            name : 'order.base.service.id'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Service ID'),
                            name : 'order.base.service.serviceid'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Type'),
                            name : 'order.base.service.type'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Code'),
                            name : 'order.base.service.code'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Name'),
                            name : 'order.base.service.name'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Price'),
                            name : 'order.base.service.price'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Costs'),
                            name : 'order.base.service.costs'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Rebate'),
                            name : 'order.base.service.rebate'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Tax rate in %'),
                            name : 'order.base.service.taxrate'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Created'),
                            name : 'order.base.service.ctime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Last modified'),
                            name : 'order.base.service.mtime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Editor'),
                            name : 'order.base.service.editor'
                        }]
                    }]
                }, {
                    xtype : 'MShop.panel.order.base.service.attribute.listuismall',
                    layout : 'fit',
                    flex : 1,
                    onOpenEditWindow : function() {}
                }]
            }]
        }];

        MShop.panel.order.service.ItemUi.superclass.initComponent.call(this);
    }
});

Ext.reg('MShop.panel.order.service.itemui', MShop.panel.order.service.ItemUi);
