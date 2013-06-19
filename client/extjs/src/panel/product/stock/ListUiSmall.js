/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.product.stock' );

MShop.panel.product.stock.ListUiSmall = Ext.extend( MShop.panel.AbstractListUi, {

	recordName : 'Product_Stock',
	idProperty : 'product.stock.id',
	siteidProperty : 'product.stock.siteid',
	itemUiXType : 'MShop.panel.product.stock.itemui',

	autoExpandColumn : 'product-stock-warehouse',

	filterConfig : {
		filters : [ {
			dataIndex : 'product.stock.warehouse.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = _('Stock');

		MShop.panel.AbstractListUi.prototype.initActions.call( this );
		MShop.panel.AbstractListUi.prototype.initToolbar.call( this );

		MShop.panel.product.stock.ListUiSmall.superclass.initComponent.call( this );
	},

	afterRender : function() {
		this.itemUi = this.findParentBy( function( c ) {
			return c.isXType( MShop.panel.AbstractItemUi, false );
		});

		MShop.panel.product.stock.ListUiSmall.superclass.afterRender.apply( this, arguments );
	},

	onBeforeLoad: function( store, options ) {
		this.setSiteParam( store );

		if( this.domain ) {
			this.setDomainFilter( store, options );
		}

		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'product.stock.productid' : this.itemUi.record ? this.itemUi.record.id : null
				}
			} ]
		};

	},

	getColumns : function()
	{
		this.typeStore = MShop.GlobalStoreMgr.get( 'Product_Stock_Warehouse' );

		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.id',
				header : _( 'Id' ),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.productid',
				header : _( 'Product Id' ),
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.warehouseid',
				header : _( 'Warehouse' ),
				align: 'center',
				id : 'product-stock-warehouse',
				renderer : this.typeColumnRenderer.createDelegate( this, [this.typeStore, "product.stock.warehouse.label" ], true )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.stocklevel',
				header : _( 'Quantity' ),
				sortable : true,
				align: 'center',
				width : 80
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.dateback',
				header : _( 'Dateback' ),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 130
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.ctime',
				header : _('Created'),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'product.stock.mtime',
				header : _('Last modified'),
				format : 'Y-m-d H:i:s',
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.stock.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg( 'MShop.panel.product.stock.listuismall', MShop.panel.product.stock.ListUiSmall );