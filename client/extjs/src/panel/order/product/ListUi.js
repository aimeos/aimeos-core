/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order.base.product');

MShop.panel.order.base.product.ListUi = Ext.extend(Ext.Panel, {
	layout: 'fit',

	title : MShop.I18n.dt( 'client/extjs', 'Products' ),

	recordName : 'Order_Base_Product',

	idProperty : 'order.base.product.id',
	siteidProperty : 'order.base.product.siteid',
	itemUiXType : 'MShop.panel.order.product.itemui',

	autoExpandColumn : 'order-base-product-Label',

	gridConfig : null,

	storeConfig : null,

	/**
	 * @cfg {Object} rowCssClass (inherited)
	 */
	rowCssClass: 'site-mismatch',


	initComponent : function()
	{
		this.initStore();

		this.grid = new Ext.grid.GridPanel(Ext.apply({
			border: false,
			loadMask: true,
			store: this.store,
			autoExpandColumn: this.autoExpandColumn,
			columns: this.getColumns()
		}, this.gridConfig));

		this.items = [this.grid];
		this.grid.on('rowdblclick', this.onOpenEditWindow.createDelegate(this, ['edit']), this);

		MShop.panel.order.base.product.ListUi.superclass.initComponent.call(this);

		Ext.apply(this.grid, {
			viewConfig: {
				emptyText: MShop.I18n.dt( 'client/extjs', 'No Items' ),
				getRowClass: function (record, index){
					if (record.phantom === true) {
						return '';
					}

					var siteId = record.get(this.siteidProperty);

					if (siteId != MShop.config.site['locale.site.id']) {
						return this.rowCssClass;
					}

					return '';
				}.createDelegate(this)
			}
		});
	},

	initStore: function() {
		this.store = new Ext.data.DirectStore(Ext.apply({
			autoLoad: false,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord(this.recordName),
			api: {
				read	: MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].saveItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter({
				writeAllFields: true,
				encode: false
			}),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			sortInfo: this.sortInfo
		}, this.storeConfig));

		// make sure site param gets set for read/write actions
		this.store.on('beforeload', this.onBeforeLoad, this);
		this.store.on('exception', this.onStoreException, this);
		this.store.on('beforewrite', this.onBeforeWrite, this);
	},

	afterRender: function() {
		MShop.panel.order.base.product.ListUi.superclass.afterRender.apply(this, arguments);

		this.ParentItemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});

		if (! this.store.autoLoad) {
			this.store.load.defer(50, this.store);
		}
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
					'order.base.product.baseid' : this.ParentItemUi.record.data['order.baseid']
				}
			} ]
		};
	},

	onBeforeWrite: function(store, action, records, options) {
		this.setSiteParam(store);

		if (this.domain) {
			this.setDomainProperty(store, action, records, options);
		}
	},

	onDestroy: function() {
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('exception', this.onStoreException, this);
		this.grid.un('rowdblclick', this.onOpenEditWindow.createDelegate(this, ['edit']), this);

		MShop.panel.order.base.product.ListUi.superclass.onDestroy.apply(this, arguments);
	},

	onStoreException: function(proxy, type, action, options, response) {
		var title = MShop.I18n.dt( 'client/extjs', 'Error' );
		var errmsg = MShop.I18n.dt( 'client/extjs', 'No error information available' );
		var msg = response && response.error ? response.error.message : errmsg;
		var code = response && response.error ? response.error.code : 0;

		Ext.Msg.alert([title, ' (', code, ')'].join(''), msg);
	},

	setSiteParam: function(store) {
		store.baseParams = store.baseParams || {};
		store.baseParams.site = MShop.config.site["locale.site.code"];
	},

	setDomainFilter: function(store, options) {
		options.params = options.params || {};
		options.params.condition = options.params.condition || {};
		options.params.condition['&&'] = options.params.condition['&&'] || [];

		if (! this.domainProperty) {
			this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
		}

		var condition = {};
		condition[this.domainProperty] = this.domain;

		options.params.condition['&&'].push({'==': condition});
	},

	setDomainProperty: function(store, action, records, options) {
		var rs = [].concat(records);

		Ext.each(rs, function(record) {
			if (! this.domainProperty) {
				this.domainProperty = this.idProperty.replace(/\..*$/, '.domain');
			}
			record.data[this.domainProperty] = this.domain;
		}, this);
	},

	onOpenEditWindow: function(action) {
		var itemUi = Ext.ComponentMgr.create({
			xtype: this.itemUiXType,
			domain: this.domain,
			record: action === 'add' ? null : this.grid.getSelectionModel().getSelected(),
			store: this.store,
			listUI: this
		});

		itemUi.show();
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.id',
				header : MShop.I18n.dt( 'client/extjs', 'ID' ),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.baseid',
				header : MShop.I18n.dt( 'client/extjs', 'Base ID' ),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.productid',
				header : MShop.I18n.dt( 'client/extjs', 'Product ID' ),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.orderproductid',
				header : MShop.I18n.dt( 'client/extjs', 'Order product ID' ),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.type',
				header : MShop.I18n.dt( 'client/extjs', 'Type' ),
				width : 50,
				hidden: true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.prodcode',
				header : MShop.I18n.dt( 'client/extjs', 'Code' )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.name',
				header : MShop.I18n.dt( 'client/extjs', 'Name' ),
				id: 'order-base-product-Label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.quantity',
				header : MShop.I18n.dt( 'client/extjs', 'Quantity' )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.price',
				header : MShop.I18n.dt( 'client/extjs', 'Price' )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.costs',
				header : MShop.I18n.dt( 'client/extjs', 'Costs' )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.rebate',
				header : MShop.I18n.dt( 'client/extjs', 'Rebate' )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.taxrate',
				header : MShop.I18n.dt( 'client/extjs', 'Tax rate' )
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.status',
				header : MShop.I18n.dt( 'client/extjs', 'Status' ),
				renderer: MShop.elements.deliverystatus.renderer
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.product.ctime',
				header : MShop.I18n.dt( 'client/extjs', 'Created' ),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.product.mtime',
				header : MShop.I18n.dt( 'client/extjs', 'Last modified' ),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.product.editor',
				header : MShop.I18n.dt( 'client/extjs', 'Editor' ),
				sortable : true,
				width : 130,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.order.base.product.listui', MShop.panel.order.base.product.ListUi);

//hook order base product into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.ItemUi', 'MShop.panel.order.base.product.ListUi', MShop.panel.order.base.product.ListUi, 10);
