/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ListUiSmall.js 14341 2011-12-14 16:00:50Z nsendetzky $
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
		filters : [ {
			dataIndex : 'product.label',
			operator : 'startswith',
			value : ''
		} ]
	},


	getColumns : function()
	{
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Product_Type');

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'product.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				hidden : true
			},
			{
				xtype : 'gridcolumn',
				dataIndex : 'product.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.typeid',
				header : _('Type'),
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "product.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.code',
				header : _('Code'),
				sortable : true,
				width : 100
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.label',
				header : _('Label'),
				sortable : true,
				id : 'product-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.suppliercode',
				header : _('Supplier'),
				sortable : true,
				width : 100,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.datestart',
				header : _('Start date'),
				sortable : true,
				width : 120,
				hidden : true,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.dateend',
				header : _('End date'),
				sortable : true,
				width : 120,
				hidden : true,
				format : 'Y-m-d H:i:s'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.ctime',
				header : _('Created'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.editor',
				header : _('Editor'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg('MShop.panel.product.listuismall', MShop.panel.product.ListUiSmall);
