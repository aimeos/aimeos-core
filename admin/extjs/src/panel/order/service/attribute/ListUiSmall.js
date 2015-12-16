/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.order.base.service.attribute');

MShop.panel.order.base.service.attribute.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

    title : MShop.I18n.dt('admin', 'Attribute'),
    recordName : 'Order_Base_Service_Attribute',
    idProperty : 'order.base.service.attribute.id',
    siteidProperty : 'order.base.service.attribute.siteid',
    itemUiXType : 'MShop.panel.order.service.itemui',
    autoExpandColumn : 'order-base-service-attribute-name',

    sortInfo : {
        field : 'order.base.service.attribute.id',
        direction : 'ASC'
    },

    filterConfig : {
        filters : [{
            dataIndex : 'order.base.service.attribute.code',
            operator : '=~',
            value : ''
        }]
    },


    initComponent : function() {
        MShop.panel.order.base.service.attribute.ListUiSmall.superclass.initComponent.apply(this, arguments);

        this.grid.un('rowcontextmenu', this.onGridContextMenu, this);
        this.grid.un('rowdblclick', this.onOpenEditWindow.createDelegate(this, ['edit']), this);
    },


    initToolbar : function() {},


    afterRender : function() {
        this.itemUi = this.findParentBy(function(c) {
            return c.isXType(MShop.panel.AbstractItemUi, false);
        });

        MShop.panel.order.base.service.attribute.ListUiSmall.superclass.afterRender.apply(this, arguments);
    },


    onBeforeLoad : function(store, options) {
        MShop.panel.order.base.service.attribute.ListUiSmall.superclass.onBeforeLoad.apply(this, arguments);

        options.params = options.params || {};
        options.params.condition = {
            '&&' : [{
                '==' : {
                    'order.base.service.attribute.parentid' : this.itemUi.record ? this.itemUi.record.id : null
                }
            }]
        };

    },


    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.attribute.id',
            header : MShop.I18n.dt('admin', 'ID'),
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.attribute.type',
            header : MShop.I18n.dt('admin', 'Type'),
            width : 150
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.attribute.name',
            header : MShop.I18n.dt('admin', 'Name'),
            id : 'order-base-service-attribute-name'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.attribute.code',
            header : MShop.I18n.dt('admin', 'Code'),
            width : 150
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.attribute.value',
            header : MShop.I18n.dt('admin', 'Value'),
            width : 150
        }, {
            xtype : 'datecolumn',
            dataIndex : 'order.base.service.attribute.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'order.base.service.attribute.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'order.base.service.attribute.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            width : 130,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.order.base.service.attribute.listuismall', MShop.panel.order.base.service.attribute.ListUiSmall);
