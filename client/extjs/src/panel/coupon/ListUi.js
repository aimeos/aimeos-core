/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 */


Ext.ns('MShop.panel.coupon');

MShop.panel.coupon.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Coupon',
	idProperty : 'coupon.id',
	siteidProperty : 'coupon.siteid',
	itemUiXType : 'MShop.panel.coupon.itemui',

	autoExpandColumn : 'coupon-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'coupon.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = _('Coupon');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.coupon.ListUi.superclass.initComponent.call(this);
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'coupon.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'coupon.status',
				header : _('Status'),
				sortable : true,
				width : 70,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'coupon.provider',
				header : _('Provider'),
				id : 'coupon-list-provider',
				sortable : true,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'coupon.label',
				header : _('Label'),
				sortable : true,
				width : 100,
				editable : false,
				id : 'coupon-list-label'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'coupon.datestart',
				header : _('Start Date'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'coupon.dateend',
				header : _('End Date'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'coupon.config',
				header : _('Configuration'),
				width : 200,
				editable : false,
				renderer: function (value) {
					var s = "";
					Ext.iterate(value, function (key, value, object) {
						s = s + String.format('<div>{0}: {1}</div>', key, value);
					}, this);
					return s;
				}
			}, {
				xtype : 'datecolumn',
				dataIndex : 'coupon.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'coupon.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'coupon.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				hidden : true
			}
		];
	}

} );

Ext.reg('MShop.panel.coupon.listui', MShop.panel.coupon.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.coupon.listui', MShop.panel.coupon.ListUi, 120);
