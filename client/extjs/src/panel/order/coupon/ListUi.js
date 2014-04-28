Ext.ns('MShop.panel.order.base.coupon');

MShop.panel.order.base.coupon.ListUi = Ext.extend(MShop.panel.AbstractListUi, {
	layout: 'fit',
	title : _('Coupons'),
	recordName : 'Order_Base_Coupon',
	idProperty : 'order.base.coupon.id',
	siteidProperty : 'order.base.coupon.siteid',
	itemUiXType : 'MShop.panel.order.product.itemui',
	autoExpandColumn : 'order-base-coupon-name',
	filterConfig : {
		filters : [ {
			dataIndex : 'order.base.coupon.code',
			operator : 'contains',
			value : ''
		} ]
	},

	initComponent : function() {
		MShop.panel.order.base.coupon.ListUi.superclass.initComponent.apply(this, arguments);
	},

	initToolbar: function() {
		MShop.panel.order.base.coupon.ListUi.superclass.initToolbar.apply(this, arguments);
		this.tbar = [];
	},

	afterRender: function() {

		this.ParentItemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});

		if (!this.store.autoLoad) {
			this.store.load();
		}

		MShop.panel.order.base.coupon.ListUi.superclass.afterRender.apply(this, arguments);
	},

	onBeforeLoad: function(store, options) {

		this.setSiteParam(store);

		if (this.domain) {
			this.setDomainFilter(store, options);
		}

		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.coupon.baseid' : this.ParentItemUi.record.data['order.baseid']
				}
			} ]
		};
	},

	onGridContextMenu: function(){},

	onOpenEditWindow: function(action) {
		var selectedData = this.grid.getSelectionModel().getSelected();
		
		if( selectedData.data['order.base.coupon.productid'] !== null ) {
			
			var orderProductStore = MShop.GlobalStoreMgr.get( 'Order_Base_Product' );
			var orderProduct = orderProductStore.getById( selectedData.data['order.base.coupon.productid'] );
	
			var itemUi = Ext.ComponentMgr.create( {
				xtype: this.itemUiXType,
				domain: this.domain,
				record: orderProduct,
				store: orderProductStore,
				listUI: MShop.panel.order.base.product.ListUi
			} );
	
			itemUi.show();
		}
	},

	getColumns : function()
	{
		this.productStore = MShop.GlobalStoreMgr.get( 'Order_Base_Product' );

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.id',
				header : _('Id'),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.baseid',
				header : _('Base Id'),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.code',
				header : _('Coupon code')
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.productid',
				header : _('Product Id'),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.productid',
				header : _('Product code'),
				renderer : this.typeColumnRenderer.createDelegate( this, [this.productStore, "order.base.product.prodcode" ], true )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.productid',
				header : _('Product name'),
				renderer : this.typeColumnRenderer.createDelegate( this, [this.productStore, "order.base.product.name" ], true ),
				id: 'order-base-coupon-name'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.productid',
				header : _('Product quantity'),
				renderer : this.typeColumnRenderer.createDelegate( this, [this.productStore, "order.base.product.quantity" ], true )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.productid',
				header : _('Product price'),
				renderer : this.typeColumnRenderer.createDelegate( this, [this.productStore, "order.base.product.price" ], true )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.productid',
				header : _('Product shipping'),
				renderer : this.typeColumnRenderer.createDelegate( this, [this.productStore, "order.base.product.shipping" ], true )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.productid',
				header : _('Product rebate'),
				renderer : this.typeColumnRenderer.createDelegate( this, [this.productStore, "order.base.product.rebate" ], true )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.productid',
				header : _('Product taxrate'),
				renderer : this.typeColumnRenderer.createDelegate( this, [this.productStore, "order.base.product.taxrate" ], true )
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.coupon.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.coupon.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.coupon.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.order.base.coupon.listui', MShop.panel.order.base.coupon.ListUi);

//hook order base product into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.ItemUi', 'MShop.panel.order.base.coupon.ListUi', MShop.panel.order.base.coupon.ListUi, 50);
