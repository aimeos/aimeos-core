/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.coupon.code');

MShop.panel.coupon.code.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Coupon_Code',
    idProperty : 'coupon.code.id',
    siteidProperty : 'coupon.code.siteid',
    itemUiXType : 'MShop.panel.coupon.code.itemui',
    importMethod : 'Coupon_Code.uploadFile',

    ParentItemUi : null,

    autoExpandColumn : 'coupon-code-list-code',

    filterConfig : {
        filters : [{
            dataIndex : 'coupon.code.code',
            operator : 'startswith',
            value : ''
        }]
    },


    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Codes');

        MShop.panel.coupon.code.ListUi.superclass.initComponent.call(this);
    },


    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'coupon.code.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'coupon.code.code',
            header : MShop.I18n.dt('admin', 'Code'),
            id : 'coupon-code-list-code',
            sortable : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'coupon.code.count',
            header : MShop.I18n.dt('admin', 'Count'),
            sortable : true,
            width : 100
        }, {
            xtype : 'datecolumn',
            dataIndex : 'coupon.code.datestart',
            header : MShop.I18n.dt('admin', 'Start date'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s'
        }, {
            xtype : 'datecolumn',
            dataIndex : 'coupon.code.dateend',
            header : MShop.I18n.dt('admin', 'End date'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s'
        }, {
            xtype : 'datecolumn',
            dataIndex : 'coupon.code.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'coupon.code.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'coupon.code.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 130,
            hidden : true
        }];
    },


    onBeforeLoad : function(store, options) {

        MShop.panel.coupon.code.ListUi.superclass.onBeforeLoad.apply(this, arguments);

        if(!this.ParentItemUi.record.data['coupon.id']) {
            Ext.Msg.alert(MShop.I18n.dt('admin', 'Notice'), MShop.I18n.dt('admin',
                'Please save the coupon first before you can add codes'));

            this.actionAdd.setDisabled(true);
            this.actionImport.setDisabled(true);

            return false;
        }

        // filter for refid
        options.params = options.params || {};
        options.params.condition = {
            '&&' : [{
                '==' : {
                    'coupon.code.parentid' : this.ParentItemUi.record.data['coupon.id']
                }
            }]
        };

        return true;
    },


    afterRender : function() {

        MShop.panel.coupon.code.ListUi.superclass.afterRender.apply(this, arguments);

        this.ParentItemUi = this.findParentBy(function(c) {
            return c.isXType(MShop.panel.AbstractItemUi, false);
        });
    },


    onFileSelect : function(fileSelector) {
        this.actionImport.onFileSelect(fileSelector, {
            parentid : this.ParentItemUi.record.id
        });
    }

});

Ext.reg('MShop.panel.coupon.code.listui', MShop.panel.coupon.code.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.coupon.ItemUi', 'MShop.panel.coupon.code.listui',
    MShop.panel.coupon.code.ListUi, 10);
