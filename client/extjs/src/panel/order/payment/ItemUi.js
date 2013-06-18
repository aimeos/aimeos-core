/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemUi.js 14818 2012-01-12 09:53:56Z spopp $
 */


Ext.ns('MShop.panel.order.base.service.payment');

MShop.panel.order.base.service.payment.ItemUi = Ext.extend(Ext.Panel, {

	recordName : 'Order_Base_Service',
	idProperty : 'order.base.service.id',
	siteidProperty : 'order.base.service.siteid',

	title : _('Payment'),
	border : false,
	layout : 'hbox',
	layoutConfig : {
		align : 'stretch'
	},
	itemId : 'MShop.panel.order.base.service.payment.ItemUi',
	plugins : [ 'ux.itemregistry' ],

	initComponent : function() {

		this.initStore();

		this.items = [ {
			xtype : 'form',
			title : 'Details',
			flex : 1,
			autoScroll : true,
			items : [ {
				xtype : 'fieldset',
				style: 'padding-right: 25px;',
				border : false,
				labelAlign : 'left',
				defaults: {
					anchor : '100%'
				},
				items : [ {
					xtype : 'displayfield',
					fieldLabel : _( 'ID' ),
					name : 'order.base.service.id'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Service ID',
					name: 'order.base.service.serviceid'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Code',
					maxLength : 32,
					name: 'order.base.service.code'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Name',
					name: 'order.base.service.name'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Price',
					name: 'order.base.service.price'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Shipping',
					name: 'order.base.service.shipping'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Rebate',
					name: 'order.base.service.rebate'
				}, {
					xtype: 'displayfield',
					fieldLabel: 'Tax rate in %',
					name: 'order.base.service.taxrate'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Created'),
					name : 'order.base.service.ctime'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Last modified'),
					name : 'order.base.service.mtime'
				}, {
					xtype : 'displayfield',
					fieldLabel : _('Editor'),
					name : 'order.base.service.editor'
				} ]
			} ]
		} ];

		MShop.panel.order.base.service.payment.ItemUi.superclass.initComponent.call(this);
	},
	
	initStore : MShop.panel.ListItemListUi.prototype.initStore,
	onStoreException : MShop.panel.AbstractListUi.prototype.onStoreException,
	onBeforeLoad : MShop.panel.AbstractListUi.prototype.setSiteParam,
	onBeforeWrite : Ext.emptyFn,

	onDestroy : function() {
		this.store.un('beforeload', this.setFilters, this);
		this.store.un('beforeload', this.onBeforeLoad, this);
		this.store.un('load', this.onStoreLoad, this);
		this.store.un('beforewrite', this.onBeforeWrite, this);
		this.store.un('write', this.onStoreWrite, this);
		this.store.un('exception', this.onStoreException, this);

		MShop.panel.order.base.service.payment.ItemUi.superclass.onDestroy.apply(this, arguments);
	},

	afterRender : function() {
		// fetch ItemUI
		this.itemUi = this.findParentBy(function(c) {
			return c.isXType(MShop.panel.AbstractItemUi, false);
		});

		this.store.load({});

		MShop.panel.order.base.service.payment.ItemUi.superclass.afterRender.apply(this, arguments);
	},

	onStoreLoad : function() {
		if (this.store.getCount() === 0) {
			var recordType = MShop.Schema.getRecord(this.recordName);
			this.record = new recordType({});

			this.store.add(this.record);
		} else {
			this.record = this.store.getAt(0);
		}

		var panelForm = this.findByType('form');
		panelForm[0].getForm().loadRecord(this.record);
	},

	setFilters : function(store, options) {
		if (!this.itemUi.record || this.itemUi.record.phantom) {
			// nothing to load
			this.onStoreLoad();
			return false;
		}
	
		// filter for refid
		options.params = options.params || {};
		options.params.condition = {
			'&&' : [ {
				'==' : {
					'order.base.service.baseid' : this.itemUi.record.data['order.baseid']
				}
			}, {
				'==' : {
					'order.base.service.type' : 'payment'
				}
			} ]
		};
		
		return true;
	}
});

Ext.reg('MShop.panel.order.base.service.payment.itemui', MShop.panel.order.base.service.payment.ItemUi);

//hook order base address into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.ItemUi', 'MShop.panel.order.base.service.payment.ItemUi', MShop.panel.order.base.service.payment.ItemUi, 40);