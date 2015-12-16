/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.text.type');

MShop.panel.text.type.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Text_Type',
    idProperty : 'text.type.id',
    siteidProperty : 'text.type.siteid',
    itemUiXType : 'MShop.panel.text.type.itemui',

    // Sort by id ASC
    sortInfo : {
        field : 'text.type.id',
        direction : 'ASC'
    },

    // Create filter
    filterConfig : {
        filters : [{
            dataIndex : 'text.type.label',
            operator : '=~',
            value : ''
        }]
    },

    // Override initComponent to set Label of tab.
    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Text type');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.text.type.ListUi.superclass.initComponent.call(this);
    },

    autoExpandColumn : 'text-type-label',

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'text.type.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.type.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            width : 50,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.type.domain',
            header : MShop.I18n.dt('admin', 'Domain'),
            sortable : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.type.code',
            header : MShop.I18n.dt('admin', 'Code'),
            sortable : true,
            width : 150,
            align : 'center',
            editable : false
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.type.label',
            id : 'text-type-label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            editable : false
        }, {
            xtype : 'datecolumn',
            dataIndex : 'text.type.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'text.type.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.type.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 130,
            editable : false,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.text.type.listui', MShop.panel.text.type.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.type.tabUi', 'MShop.panel.text.type.listui',
    MShop.panel.text.type.ListUi, 70);
