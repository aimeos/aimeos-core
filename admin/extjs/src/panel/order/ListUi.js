/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.order');

MShop.panel.order.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Order',
    idProperty : 'order.id',
    siteidProperty : 'order.siteid',
    itemUiXType : 'MShop.panel.order.itemui',

    sortInfo : {
        field : 'order.datepayment',
        direction : 'DESC'
    },

    autoExpandColumn : 'order-relatedid',

    filterConfig : {
        filters : [{
            dataIndex : 'order.datepayment',
            operator : '>',
            value : Ext.util.Format.date(new Date(new Date().valueOf() - 7 * 86400 * 1000), 'Y-m-d H:i:s')
        }]
    },

    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Order');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.order.ListUi.superclass.initComponent.call(this);
    },

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'order.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            width : 55,
            id : 'order-list-id'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.type',
            header : MShop.I18n.dt('admin', 'Source'),
            sortable : true,
            width : 85,
            align : 'center'
        }, {
            xtype : 'datecolumn',
            dataIndex : 'order.datepayment',
            header : MShop.I18n.dt('admin', 'Purchase date'),
            sortable : true,
            width : 180,
            format : 'Y-m-d H:i:s'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.statuspayment',
            header : MShop.I18n.dt('admin', 'Payment status'),
            sortable : true,
            renderer : MShop.elements.paymentstatus.renderer
        }, {
            xtype : 'datecolumn',
            dataIndex : 'order.datedelivery',
            header : MShop.I18n.dt('admin', 'Delivery date'),
            sortable : true,
            width : 180,
            format : 'Y-m-d H:i:s'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.statusdelivery',
            header : MShop.I18n.dt('admin', 'Delivery status'),
            sortable : true,
            renderer : MShop.elements.deliverystatus.renderer
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.relatedid',
            header : MShop.I18n.dt('admin', 'Related order ID'),
            id : 'order-relatedid'
        }, {
            xtype : 'datecolumn',
            dataIndex : 'order.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            width : 130,
            format : 'Y-m-d H:i:s',
            sortable : true,
            editable : false,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'order.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            width : 130,
            format : 'Y-m-d H:i:s',
            sortable : true,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            width : 130,
            sortable : true,
            editable : false,
            hidden : true
        }];
    },


    onOpenEditWindow : function(action) {
        if(action === 'add') {
            return Ext.Msg.alert(MShop.I18n.dt('admin', 'Not implemented'), MShop.I18n.dt('admin',
                'Sorry, adding orders manually is currently not implemented'));
        }

        return MShop.panel.order.ListUi.superclass.onOpenEditWindow.apply(this, arguments);
    }
});

Ext.reg('MShop.panel.order.listui', MShop.panel.order.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.order.listui', MShop.panel.order.ListUi, 40);
