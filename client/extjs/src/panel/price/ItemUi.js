/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.price');

MShop.panel.price.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'price.siteid',

	initComponent : function() {

		this.title = _('Price item details');

		if(this.copyActive){
			this.record.data['price.id'] = null;
		}
		
		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.price.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.price.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'price.id'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'price.label',
							allowBlank : true,
							emptyText : _('Label of the price')
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'price.status'
						}, {
							xtype : 'combo',
							fieldLabel : 'Type',
							name : 'price.typeid',
							mode : 'local',
							store : this.listUI.ItemTypeStore,
							displayField : 'price.type.label',
							valueField : 'price.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Default, special prices (required)'),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'price.type.code', 'default' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'MShop.elements.currency.combo',
							name : 'price.currencyid',
							emptyText : _('Currency (required)')
						}, {
							xtype : 'numberfield',
							name : 'price.quantity',
							fieldLabel : 'Minimum quantity',
							allowNegative : false,
							allowDecimals : false,
							allowBlank : false,
							value : 1
						}, {
							xtype : 'ux.decimalfield',
							fieldLabel : 'Actual price',
							name : 'price.value',
							allowBlank : false,
							value : '0.00'
						}, {
							xtype : 'ux.decimalfield',
							fieldLabel : 'Given rebate amount',
							name : 'price.rebate',
							allowBlank : false,
							value : '0.00'
						}, {
							xtype : 'ux.decimalfield',
							fieldLabel : 'Shipping costs per item',
							name : 'price.shipping',
							allowBlank : false,
							value : '0.00'
						}, {
							xtype : 'ux.decimalfield',
							fieldLabel : 'Tax rate in percent',
							name : 'price.taxrate',
							allowBlank : false,
							value : '0.00'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'price.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'price.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'price.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.price.ItemUi.superclass.initComponent.call(this);
	},

	afterRender : function()
	{
		var label = this.record ? this.record.data['price.price'] : 'new';

		this.setTitle( 'Price: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.price.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.price.itemui', MShop.panel.price.ItemUi);
