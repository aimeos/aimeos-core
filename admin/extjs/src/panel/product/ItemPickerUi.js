/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.product');

MShop.panel.product.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

    title : MShop.I18n.dt('admin', 'Product'),

    initComponent : function() {

        Ext.apply(this.itemConfig, {
            title : MShop.I18n.dt('admin', 'Associated products'),
            xtype : 'MShop.panel.listitemlistui',
            domain : 'product',
            getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
        });

        Ext.apply(this.listConfig, {
            title : MShop.I18n.dt('admin', 'Available products'),
            xtype : 'MShop.panel.product.listuismall'
        });

        MShop.panel.product.ItemPickerUi.superclass.initComponent.call(this);
    },

    getAdditionalColumns : function() {

        var conf = this.itemConfig;
        this.typeStore = MShop.GlobalStoreMgr.get('Product_Type', conf.domain);
        this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

        return [
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'typeid',
                header : MShop.I18n.dt('admin', 'List type'),
                id : 'listtype',
                width : 70,
                renderer : this.typeColumnRenderer.createDelegate(this,
                    [this.listTypeStore, conf.listTypeLabelProperty], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Status'),
                id : 'refstatus',
                width : 50,
                align : 'center',
                renderer : this.refStatusColumnRenderer.createDelegate(this, ['product.status'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Type'),
                id : 'reftype',
                width : 70,
                renderer : this.refTypeColumnRenderer.createDelegate(this, [
                    this.typeStore,
                    'product.typeid',
                    'product.type.label'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Code'),
                id : 'refcode',
                width : 100,
                renderer : this.refColumnRenderer.createDelegate(this, ['product.code'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Label'),
                id : 'refcontent',
                renderer : this.refColumnRenderer.createDelegate(this, ['product.label'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Start date'),
                id : 'refprodstart',
                width : 120,
                hidden : true,
                renderer : this.refDateColumnRenderer.createDelegate(this, ['product.datestart'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'End date'),
                id : 'refprodend',
                width : 120,
                hidden : true,
                renderer : this.refDateColumnRenderer.createDelegate(this, ['product.dateend'], true)
            }];
    }
});

Ext.reg('MShop.panel.product.itempickerui', MShop.panel.product.ItemPickerUi);
