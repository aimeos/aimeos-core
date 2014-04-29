/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.coupon.code');

MShop.panel.coupon.code.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Coupon_Code',
	idProperty : 'coupon.code.id',
	siteidProperty : 'coupon.code.siteid',
	itemUiXType : 'MShop.panel.coupon.code.itemui',

	autoExpandColumn : 'coupon-code-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'coupon.code.code',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = MShop.I18n.dt( 'client/extjs', 'Coupon code' );

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.coupon.code.ListUi.superclass.initComponent.call(this);
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'coupon.code.id',
				header : MShop.I18n.dt( 'client/extjs', 'ID' ),
				sortable : true,
				width : 50,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'coupon.code.code',
				header : MShop.I18n.dt( 'client/extjs', 'Code' ),
				id : 'coupon-code-list-provider',
				sortable : true,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'coupon.code.count',
				header : MShop.I18n.dt( 'client/extjs', 'Count' ),
				sortable : true,
				width : 100,
				editable : false,
				id : 'coupon-list-label'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'coupon.code.datestart',
				header : MShop.I18n.dt( 'client/extjs', 'Start date' ),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'coupon.code.dateend',
				header : MShop.I18n.dt( 'client/extjs', 'End date' ),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'coupon.code.ctime',
				header : MShop.I18n.dt( 'client/extjs', 'Created' ),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'coupon.code.mtime',
				header : MShop.I18n.dt( 'client/extjs', 'Last modified' ),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'coupon.code.editor',
				header : MShop.I18n.dt( 'client/extjs', 'Editor' ),
				sortable : true,
				width : 130,
				hidden : true
			}
		];
	}

} );

Ext.reg('MShop.panel.coupon.code.listui', MShop.panel.coupon.code.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.coupon.ItemUi', 'MShop.panel.coupon.code.ListUi', MShop.panel.coupon.code.ListUi, 10);
