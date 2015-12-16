/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.product.type');

MShop.panel.product.type.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Product_Type',
    idProperty : 'product.type.id',
    siteidProperty : 'product.type.siteid',

    itemUiXType : 'MShop.panel.product.type.itemui',

    // Sort by id ASC
    sortInfo : {
        field : 'product.type.id',
        direction : 'ASC'
    },

    // Create filter
    filterConfig : {
        filters : [{
            dataIndex : 'product.type.label',
            operator : '=~',
            value : ''
        }]
    },

    // Override initComponent to set Label of tab.
    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Product type');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.product.type.ListUi.superclass.initComponent.call(this);
    },

    autoExpandColumn : 'product-type-label',

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'product.type.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.type.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            width : 50,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.type.domain',
            header : MShop.I18n.dt('admin', 'Domain'),
            sortable : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.type.code',
            header : MShop.I18n.dt('admin', 'Code'),
            sortable : true,
            width : 150,
            align : 'center',
            editable : false
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.type.label',
            id : 'product-type-label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            editable : false
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.type.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.type.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.type.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 130,
            editable : false,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.product.type.listui', MShop.panel.product.type.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.type.tabUi', 'MShop.panel.product.type.listui',
    MShop.panel.product.type.ListUi, 50);
