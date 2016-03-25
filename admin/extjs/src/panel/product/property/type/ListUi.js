/*!
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * Copyright Aimeos (aimeos.org), 2016
 */

Ext.ns('MShop.panel.product.property.type');

MShop.panel.product.property.type.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Product_Property_Type',
    idProperty : 'product.property.type.id',
    siteidProperty : 'product.property.type.siteid',

    itemUiXType : 'MShop.panel.product.property.type.itemui',

    // Sort by id ASC
    sortInfo : {
        field : 'product.property.type.id',
        direction : 'ASC'
    },

    // Create filter
    filterConfig : {
        filters : [{
            dataIndex : 'product.property.type.label',
            operator : '=~',
            value : ''
        }]
    },

    // Override initComponent to set Label of tab.
    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Product property type');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.product.property.type.ListUi.superclass.initComponent.call(this);
    },

    autoExpandColumn : 'product-property-type-label',

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'product.property.type.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.property.type.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            width : 50,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.property.type.domain',
            header : MShop.I18n.dt('admin', 'Domain'),
            sortable : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.property.type.code',
            header : MShop.I18n.dt('admin', 'Code'),
            sortable : true,
            width : 150,
            align : 'center',
            editable : false
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.property.type.label',
            id : 'product-property-type-label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            editable : false
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.property.type.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.property.type.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.property.type.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 130,
            editable : false,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.product.property.type.listui', MShop.panel.product.property.type.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.type.tabUi', 'MShop.panel.product.property.type.listui',
    MShop.panel.product.property.type.ListUi, 60);
