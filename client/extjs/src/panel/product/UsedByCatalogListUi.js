/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: UsedByCatalogListUi.js 14413 2011-12-17 14:26:46Z nsendetzky $
 */


Ext.ns( 'MShop.panel.product' );

MShop.panel.product.UsedByCatalogListUi = Ext.extend( MShop.panel.AbstractUsedByListUi, {

	recordName : 'Catalog_List',
	idProperty : 'catalog.list.id',
	siteidProperty : 'catalog.list.siteid',
	itemUiXType : 'MShop.panel.catalog.itemui',

	autoExpandColumn : 'catalog-list-autoexpand-column',

	sortInfo : {
		field : 'catalog.list.type.code',
		direction : 'ASC'
	},

	parentIdProperty : 'catalog.list.parentid',
	parentDomainPorperty : 'catalog.list.domain',
	parentRefIdProperty : 'catalog.list.refid',

	initComponent : function()
	{
		MShop.panel.product.UsedByCatalogListUi.superclass.initComponent.call( this );

		this.title = _( 'Category' );

		this.catalogStore = MShop.GlobalStoreMgr.get( 'Catalog' );
	},

	onOpenEditWindow: function( action ) {
		var record = this.grid.getSelectionModel().getSelected();
		var parentRecord = this.catalogStore.getById( record.data[this.parentIdProperty] );

		parentRecord.data['status'] = parentRecord.data['catalog.status'];
		parentRecord.data['label'] = parentRecord.data['catalog.label'];
		parentRecord.data['code'] = parentRecord.data['catalog.code'];

		var itemUi = Ext.ComponentMgr.create( {
			xtype: this.itemUiXType,
			domain: this.domain,
			record: action === 'add' ? null : parentRecord,
			store: this.catalogStore,
			listUI: this
		} );

		itemUi.show();
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.id',
				header : _( 'List ID' ),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.typeid',
				header : _( 'List type' ),
				sortable : true,
				width : 100,
				renderer : this.listTypeColumnRenderer.createDelegate(this, [this.listTypeStore, "catalog.list.type.label" ], true)
			}, {
				xtype : 'datecolumn',
				dataIndex : 'catalog.list.datestart',
				header : _( 'List start date' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120
			}, {
				xtype : 'datecolumn',
				dataIndex : 'catalog.list.dateend',
				header : _( 'List end date' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.position',
				header : _( 'List position' ),
				sortable : true,
				width : 70,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'catalog.list.mtime',
				header : _( 'Modification time' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'catalog.list.ctime',
				header : _( 'Creation time' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 120,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.editor',
				header : _( 'Editor' ),
				sortable : true,
				width : 120,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.parentid',
				header : _( 'Category ID' ),
				sortable : true,
				width : 100
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.parentid',
				header : _( 'Status' ),
				sortable : false,
				width : 50,
				renderer : this.statusColumnRenderer.createDelegate(this, [this.catalogStore, "catalog.status" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.parentid',
				header : _( 'Category code' ),
				sortable : false,
				width : 100,
				renderer : this.listTypeColumnRenderer.createDelegate(this, [this.catalogStore, "catalog.code" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'catalog.list.parentid',
				header : _( 'Category label' ),
				sortable : false,
				width : 100,
				id : 'catalog-list-autoexpand-column',
				renderer : this.listTypeColumnRenderer.createDelegate(this, [this.catalogStore, "catalog.label" ], true)
			}
		];
	}
});

Ext.reg( 'MShop.panel.product.usedbycataloglistui', MShop.panel.product.UsedByCatalogListUi );

//hook parent product list into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', MShop.panel.product.UsedByCatalogListUi, 100);
