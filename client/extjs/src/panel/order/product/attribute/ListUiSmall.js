/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.order.base.product.attribute' );

MShop.panel.order.base.product.attribute.ListUiSmall = Ext.extend( MShop.panel.AbstractListUi, {

	title: _( 'Attribute' ),
	recordName : 'Order_Base_Product_Attribute',
	idProperty : 'order.base.product.attribute.id',
	siteidProperty : 'order.base.product.attribute.siteid',
	itemUiXType : 'MShop.panel.order.product.itemui',

	sortInfo : {
		field : 'order.base.product.attribute.id',
		direction : 'ASC'
	},

	autoExpandColumn : 'order-base-product-attribute-name',

	filterConfig : {
		filters : [ {
			dataIndex : 'order.base.product.attribute.code',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function() {
		MShop.panel.order.base.product.attribute.ListUiSmall.superclass.initComponent.apply( this, arguments );

		this.grid.un('rowcontextmenu', this.onGridContextMenu, this);
		this.grid.un('rowdblclick', this.onOpenEditWindow.createDelegate(this, ['edit']), this);
	},

	initToolbar: function() {
		MShop.panel.order.base.product.attribute.ListUiSmall.superclass.initToolbar.apply( this, arguments );
		this.tbar = [];
	},

	afterRender : function() {
		this.itemUi = this.findParentBy( function( c ) {
			return c.isXType( MShop.panel.AbstractItemUi, false );
		});

		MShop.panel.order.base.product.attribute.ListUiSmall.superclass.afterRender.apply( this, arguments );
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
					'order.base.product.attribute.productid' : this.itemUi.record ? this.itemUi.record.id : null
				}
			} ]
		};

	},

	getColumns : function()
	{
		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.id',
				header : _( 'ID' ),
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.type',
				header : _('Type'),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.name',
				header : _('Name'),
				id : 'order-base-product-attribute-name'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.code',
				header : _( 'Code' ),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.value',
				header : _( 'Value' ),
				width : 150
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.product.attribute.ctime',
				header : _('Created'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.product.attribute.mtime',
				header : _('Last modified'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.attribute.editor',
				header : _('Editor'),
				width : 130,
				hidden : true
			}
		];
	}
} );

Ext.reg( 'MShop.panel.order.base.product.attribute.listuismall', MShop.panel.order.base.product.attribute.ListUiSmall );