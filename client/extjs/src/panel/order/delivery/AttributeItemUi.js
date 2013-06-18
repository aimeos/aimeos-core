/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order.base.service.delivery.attribute');

MShop.panel.order.base.service.delivery.attribute.ItemUi = Ext.extend(Ext.Panel, {

	title : _('Attributes'),
	flex : 1,
	layout: 'fit',

	recordName : 'Order_Base_Service_Attribute',

	idProperty : 'order.base.service.attribute.id',
	siteidProperty : 'order.base.service.attribute.siteid',

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
			autoExpandColumn: 'order-base-service-attribute-delivery-name-id',
			columns: this.getColumns()
		}, this.gridConfig));

		this.items = [this.grid];

		MShop.panel.order.base.service.delivery.attribute.ItemUi.superclass.initComponent.call(this);

		Ext.apply(this.grid, {
			viewConfig: {
				emptyText: _('No Items'),
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
		this.ParentItemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.order.base.service.delivery.ItemUi, false);
		});

		this.initRecord();

		MShop.panel.order.base.service.delivery.attribute.ItemUi.superclass.afterRender.apply(this, arguments);
	},

	initRecord: function() {
		if (! this.ParentItemUi.record) {
			// wait till ref if here
			return this.initRecord.defer(50, this, arguments);
		}

		if (! this.store.autoLoad) {
			this.store.load();
		}
		return true;
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
					'order.base.service.attribute.serviceid' : this.ParentItemUi.record.phantom ? null : this.ParentItemUi.record.data['order.base.service.id']
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

		MShop.panel.order.base.service.delivery.attribute.ItemUi.superclass.onDestroy.apply(this, arguments);
	},

	onStoreException: function(proxy, type, action, options, response) {
		var title = _('Error');
		var msg = response && response.error ? response.error.message : _('No error information available');
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

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.id',
				header : _('ID'),
				width : 55,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.type',
				header : _('Type'),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.name',
				header : _('Name'),
				id : 'order-base-service-attribute-delivery-name-id'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.code',
				header : _('Code'),
				width : 150
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.value',
				header : _('Value'),
				width : 150
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.service.attribute.ctime',
				header : _('Created'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'order.base.service.attribute.mtime',
				header : _('Last modified'),
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'order.base.service.attribute.editor',
				header : _('Editor'),
				width : 130,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.order.base.service.delivery.attribute.itemui', MShop.panel.order.base.service.delivery.attribute.ItemUi);

//hook order base address into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.base.service.delivery.ItemUi', 'MShop.panel.order.base.service.delivery.attribute.ItemUi', MShop.panel.order.base.service.delivery.attribute.ItemUi, 20);
