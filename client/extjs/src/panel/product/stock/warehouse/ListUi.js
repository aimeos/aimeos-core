/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ListUi.js 14347 2011-12-15 08:47:00Z nsendetzky $
 */


Ext.ns( 'MShop.panel.stock.warehouse' );

MShop.panel.stock.warehouse.ListUi = Ext.extend( MShop.panel.AbstractListUi, {

	recordName : 'Product_Stock_Warehouse',
	idProperty : 'product.stock.warehouse.id',
	siteidProperty : 'product.stock.warehouse.siteid',
	itemUiXType : 'MShop.panel.stock.warehouse.itemui',

	autoExpandColumn : 'product-warehouse-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'product.stock.warehouse.code',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = _( 'Warehouse' );

		MShop.panel.AbstractListUi.prototype.initActions.call( this );
		MShop.panel.AbstractListUi.prototype.initToolbar.call( this );

		MShop.panel.stock.warehouse.ListUi.superclass.initComponent.call( this );
	},

	getColumns : function()
	{
		this.typeStore = MShop.GlobalStoreMgr.get( 'Product_Stock_Warehouse' );

		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.id',
				header : _( 'Id' ),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.status',
				header : _( 'Status' ),
				sortable : true,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate( this )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.code',
				header : _( 'Code' ),
				sortable : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.label',
				header : _( 'Label' ),
				sortable : true,
				id : 'product-warehouse-list-label'
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.warehouse.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.warehouse.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouse.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				hidden : true
			}
		];
	}
} );

Ext.reg( 'MShop.panel.stock.warehouse.listui', MShop.panel.stock.warehouse.ListUi );

Ext.ux.ItemRegistry.registerItem( 'MShop.MainTabPanel', 'MShop.panel.stock.warehouse.listui', MShop.panel.stock.warehouse.ListUi, 90 );