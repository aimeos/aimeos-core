/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.product');

MShop.panel.product.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Product',
    idProperty : 'product.id',
    siteidProperty : 'product.siteid',
    itemUiXType : 'MShop.panel.product.itemui',
    exportMethod : 'Product_Export_Text.createJob',

    autoExpandColumn : 'product-list-label',

    filterConfig : {
        filters : [{
            dataIndex : 'product.label',
            operator : '=~',
            value : ''
        }]
    },

    getColumns : function() {
        // make sure type store gets loaded in same batch as this grid data
        this.typeStore = MShop.GlobalStoreMgr.get('Product_Type');

        return [{
            xtype : 'gridcolumn',
            dataIndex : 'product.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            width : 50,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.typeid',
            header : MShop.I18n.dt('admin', 'Type'),
            width : 70,
            renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "product.type.label"], true)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.code',
            header : MShop.I18n.dt('admin', 'Code'),
            sortable : true,
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            id : 'product-list-label'
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.datestart',
            header : MShop.I18n.dt('admin', 'Start date'),
            sortable : true,
            width : 120,
            hidden : true,
            format : 'Y-m-d H:i:s'
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.dateend',
            header : MShop.I18n.dt('admin', 'End date'),
            sortable : true,
            width : 120,
            hidden : true,
            format : 'Y-m-d H:i:s'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 120,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 120,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 120,
            editable : false,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.product.listuismall', MShop.panel.product.ListUiSmall);
