/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.product');

MShop.panel.product.UsedByCatalogListUi = Ext.extend(MShop.panel.AbstractUsedByListUi, {

    recordName : 'Catalog_Lists',
    idProperty : 'catalog.lists.id',
    siteidProperty : 'catalog.lists.siteid',
    itemUiXType : 'MShop.panel.catalog.itemui',

    autoExpandColumn : 'catalog-list-autoexpand-column',

    sortInfo : {
        field : 'catalog.lists.type.code',
        direction : 'ASC'
    },

    parentIdProperty : 'catalog.lists.parentid',
    parentDomainPorperty : 'catalog.lists.domain',
    parentRefIdProperty : 'catalog.lists.refid',

    initComponent : function() {
        MShop.panel.product.UsedByCatalogListUi.superclass.initComponent.call(this);

        this.title = MShop.I18n.dt('admin', 'Catalog');

        this.catalogStore = MShop.GlobalStoreMgr.get('Catalog');
    },

    onOpenEditWindow : function(action) {
        var record = this.grid.getSelectionModel().getSelected();
        var parentRecord = this.catalogStore.getById(record.data[this.parentIdProperty]);

        parentRecord.data['status'] = parentRecord.data['catalog.status'];
        parentRecord.data['label'] = parentRecord.data['catalog.label'];
        parentRecord.data['code'] = parentRecord.data['catalog.code'];

        var itemUi = Ext.ComponentMgr.create({
            xtype : this.itemUiXType,
            domain : this.domain,
            record : action === 'add' ? null : parentRecord,
            store : this.catalogStore,
            listUI : this
        });

        itemUi.show();
    },

    getColumns : function() {
        return [
            {
                xtype : 'gridcolumn',
                dataIndex : 'catalog.lists.id',
                header : MShop.I18n.dt('admin', 'List ID'),
                sortable : true,
                width : 50,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'catalog.lists.typeid',
                header : MShop.I18n.dt('admin', 'List type'),
                sortable : true,
                width : 100,
                renderer : this.listTypeColumnRenderer.createDelegate(this, [
                    this.listTypeStore,
                    "catalog.lists.type.label"], true)
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'catalog.lists.datestart',
                header : MShop.I18n.dt('admin', 'List start date'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 120
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'catalog.lists.dateend',
                header : MShop.I18n.dt('admin', 'List end date'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 120
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'catalog.lists.position',
                header : MShop.I18n.dt('admin', 'List position'),
                sortable : true,
                width : 70,
                hidden : true
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'catalog.lists.ctime',
                header : MShop.I18n.dt('admin', 'List creation time'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 120,
                hidden : true
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'catalog.lists.mtime',
                header : MShop.I18n.dt('admin', 'List modification time'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 120,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'catalog.lists.editor',
                header : MShop.I18n.dt('admin', 'List editor'),
                sortable : true,
                width : 120,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'catalog.lists.parentid',
                header : MShop.I18n.dt('admin', 'ID'),
                sortable : true,
                width : 100
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'catalog.lists.parentid',
                header : MShop.I18n.dt('admin', 'Status'),
                sortable : false,
                width : 50,
                renderer : this.statusColumnRenderer.createDelegate(this, [this.catalogStore, "catalog.status"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'catalog.lists.parentid',
                header : MShop.I18n.dt('admin', 'Code'),
                sortable : false,
                width : 100,
                renderer : this.listTypeColumnRenderer.createDelegate(this, [this.catalogStore, "catalog.code"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'catalog.lists.parentid',
                header : MShop.I18n.dt('admin', 'Label'),
                sortable : false,
                width : 100,
                id : 'catalog-list-autoexpand-column',
                renderer : this.listTypeColumnRenderer.createDelegate(this, [this.catalogStore, "catalog.label"], true)
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'catalog.lists.parentid',
                header : MShop.I18n.dt('admin', 'Created'),
                format : 'Y-m-d H:i:s',
                sortable : false,
                width : 120,
                hidden : true,
                renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [
                    this.catalogStore,
                    "catalog.ctime"], true)
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'catalog.lists.parentid',
                header : MShop.I18n.dt('admin', 'Last modified'),
                format : 'Y-m-d H:i:s',
                sortable : false,
                width : 120,
                hidden : true,
                renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [
                    this.catalogStore,
                    "catalog.mtime"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'catalog.lists.parentid',
                header : MShop.I18n.dt('admin', 'Editor'),
                sortable : false,
                width : 100,
                hidden : true,
                renderer : MShop.panel.AbstractListUi.prototype.typeColumnRenderer.createDelegate(this, [
                    this.catalogStore,
                    "catalog.editor"], true)
            }];
    }
});

Ext.reg('MShop.panel.product.usedbycataloglistui', MShop.panel.product.UsedByCatalogListUi);

//hook parent product list into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.UsedByCatalogListUi',
    MShop.panel.product.UsedByCatalogListUi, 100);
