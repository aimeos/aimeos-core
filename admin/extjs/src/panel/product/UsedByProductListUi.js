/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.product');

MShop.panel.product.UsedByProductListUi = Ext.extend(MShop.panel.AbstractUsedByListUi, {

    recordName : 'Product_Lists',
    idProperty : 'product.lists.id',
    siteidProperty : 'product.lists.siteid',
    itemUiXType : 'MShop.panel.product.itemui',

    autoExpandColumn : 'product-list-autoexpand-column',

    sortInfo : {
        field : 'product.lists.parentid',
        direction : 'ASC'
    },

    parentIdProperty : 'product.lists.parentid',
    parentDomainPorperty : 'product.lists.domain',
    parentRefIdProperty : 'product.lists.refid',

    initComponent : function() {
        MShop.panel.product.UsedByProductListUi.superclass.initComponent.call(this);

        this.title = MShop.I18n.dt('admin', 'Used by');
    },

    getColumns : function() {
        return [
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.id',
                header : MShop.I18n.dt('admin', 'List ID'),
                sortable : true,
                width : 50,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.typeid',
                header : MShop.I18n.dt('admin', 'List type'),
                sortable : true,
                width : 100,
                renderer : this.listTypeColumnRenderer.createDelegate(this, [
                    this.listTypeStore,
                    "product.lists.type.label"], true)
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.lists.datestart',
                header : MShop.I18n.dt('admin', 'List start date'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 120
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.lists.dateend',
                header : MShop.I18n.dt('admin', 'List end date'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 120
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.position',
                header : MShop.I18n.dt('admin', 'List position'),
                sortable : true,
                width : 70,
                hidden : true
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.lists.mtime',
                header : MShop.I18n.dt('admin', 'List modification time'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 120,
                hidden : true
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.lists.ctime',
                header : MShop.I18n.dt('admin', 'List creation time'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 120,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.editor',
                header : MShop.I18n.dt('admin', 'List editor'),
                sortable : true,
                width : 120,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'ID'),
                sortable : true,
                width : 100
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'Status'),
                sortable : false,
                width : 50,
                renderer : this.statusColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.status"],
                    true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'Type'),
                sortable : false,
                width : 100,
                renderer : this.productTypeColumnRenderer.createDelegate(this, [
                    this.ParentItemUi.store,
                    this.productTypeStore,
                    "product.typeid",
                    "product.type.label"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'Code'),
                sortable : false,
                width : 100,
                renderer : this.listTypeColumnRenderer.createDelegate(this, [this.ParentItemUi.store, "product.code"],
                    true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'Label'),
                sortable : false,
                id : 'product-list-autoexpand-column',
                renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [
                    this.ParentItemUi.store,
                    "product.label"], true)
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'Start date'),
                format : 'Y-m-d H:i:s',
                sortable : false,
                width : 120,
                hidden : true,
                renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [
                    this.ParentItemUi.store,
                    "product.datestart"], true)
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'End date'),
                format : 'Y-m-d H:i:s',
                sortable : false,
                width : 120,
                hidden : true,
                renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [
                    this.ParentItemUi.store,
                    "product.dateend"], true)
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'Created'),
                format : 'Y-m-d H:i:s',
                sortable : false,
                width : 120,
                hidden : true,
                renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [
                    this.ParentItemUi.store,
                    "product.ctime"], true)
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'Last modified'),
                format : 'Y-m-d H:i:s',
                sortable : false,
                width : 120,
                hidden : true,
                renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [
                    this.ParentItemUi.store,
                    "product.mtime"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.lists.parentid',
                header : MShop.I18n.dt('admin', 'Editor'),
                sortable : false,
                width : 100,
                hidden : true,
                renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [
                    this.ParentItemUi.store,
                    "product.editor"], true)
            }];
    }
});

Ext.reg('MShop.panel.product.usedbyproductlistui', MShop.panel.product.UsedByProductListUi);

//hook parent product list into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.UsedByProductListUi',
    MShop.panel.product.UsedByProductListUi, 110);
