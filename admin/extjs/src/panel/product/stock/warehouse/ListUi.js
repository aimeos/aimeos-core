/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.stock.warehouse');

MShop.panel.stock.warehouse.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Product_Stock_Warehouse',
    idProperty : 'product.stock.warehouse.id',
    siteidProperty : 'product.stock.warehouse.siteid',
    itemUiXType : 'MShop.panel.stock.warehouse.itemui',

    autoExpandColumn : 'product-warehouse-list-label',

    filterConfig : {
        filters : [{
            dataIndex : 'product.stock.warehouse.code',
            operator : '=~',
            value : ''
        }]
    },

    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Warehouse');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.stock.warehouse.ListUi.superclass.initComponent.call(this);
    },

    getColumns : function() {
        this.typeStore = MShop.GlobalStoreMgr.get('Product_Stock_Warehouse');

        return [{
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.warehouse.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.warehouse.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.warehouse.code',
            header : MShop.I18n.dt('admin', 'Code'),
            sortable : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.warehouse.label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            id : 'product-warehouse-list-label'
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.stock.warehouse.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.stock.warehouse.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.warehouse.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 130,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.stock.warehouse.listui', MShop.panel.stock.warehouse.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.stock.warehouse.listui',
    MShop.panel.stock.warehouse.ListUi, 90);
