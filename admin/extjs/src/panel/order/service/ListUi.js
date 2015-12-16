/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.order.base.service');

MShop.panel.order.base.service.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    title : MShop.I18n.dt('admin', 'Services'),
    recordName : 'Order_Base_Service',
    idProperty : 'order.base.service.id',
    siteidProperty : 'order.base.service.siteid',
    itemUiXType : 'MShop.panel.order.service.itemui',
    autoExpandColumn : 'order-base-service-label',

    sortInfo : {
        field : 'order.base.service.type',
        direction : 'ASC'
    },

    filterConfig : {
        filters : [{
            dataIndex : 'order.base.service.type',
            operator : '=~',
            value : ''
        }]
    },


    initToolbar : function() {},


    afterRender : function() {
        MShop.panel.order.base.service.ListUi.superclass.afterRender.apply(this, arguments);

        this.ParentItemUi = this.findParentBy(function(c) {
            return c.isXType(MShop.panel.AbstractItemUi, false);
        });
    },


    onBeforeLoad : function(store, options) {
        MShop.panel.order.base.service.ListUi.superclass.onBeforeLoad.apply(this, arguments);

        options.params = options.params || {};
        options.params.condition = {
            '&&' : [{
                '==' : {
                    'order.base.service.baseid' : this.ParentItemUi.record.data['order.baseid']
                }
            }]
        };
    },


    onGridContextMenu : function() {},


    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.id',
            header : MShop.I18n.dt('admin', 'ID'),
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.baseid',
            header : MShop.I18n.dt('admin', 'Base ID'),
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.serviceid',
            header : MShop.I18n.dt('admin', 'Service ID'),
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.type',
            header : MShop.I18n.dt('admin', 'Type')
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.code',
            header : MShop.I18n.dt('admin', 'Code')
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.name',
            header : MShop.I18n.dt('admin', 'Name'),
            id : 'order-base-service-label'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.price',
            header : MShop.I18n.dt('admin', 'Price')
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.costs',
            header : MShop.I18n.dt('admin', 'Costs')
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.rebate',
            header : MShop.I18n.dt('admin', 'Rebate')
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.taxrate',
            header : MShop.I18n.dt('admin', 'Tax rate')
        }, {
            xtype : 'datecolumn',
            dataIndex : 'order.base.service.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'order.base.service.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 130,
            hidden : true
        }];
    }
});


Ext.reg('MShop.panel.order.base.service.listui', MShop.panel.order.base.service.ListUi);

//hook order base service into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.ItemUi', 'MShop.panel.order.base.service.ListUi',
    MShop.panel.order.base.service.ListUi, 30);
