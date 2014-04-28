/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 */


Ext.ns('MShop.panel.coupon');

MShop.panel.coupon.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,


	initComponent : function() {
		this.title = _('Coupon item details');

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.coupon.ItemUi',
			coupons : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.coupon.ItemUi.BasicPanel',
				coupons : [ 'ux.itemregistry' ],
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
						defaults: {
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'coupon.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'coupon.status'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Provider'),
							name : 'coupon.provider',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Name of the coupon provider class (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'coupon.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Internal coupon name (required)')
						}, {
							xtype : 'datefield',
							fieldLabel : _('Available from'),
							name : 'coupon.datestart',
							format : 'Y-m-d H:i:s',
							emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
						}, {
							xtype : 'datefield',
							fieldLabel : _('Available until'),
							name : 'coupon.dateend',
							format : 'Y-m-d H:i:s',
							emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'coupon.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'coupon.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'coupon.editor'
						} ]
					} ]
				}, {
					xtype: 'MShop.panel.coupon.configui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get('coupon.config') : {} )
				}]
			} ]
		} ];

		this.store.on('beforesave', this.onBeforeSave, this);

		MShop.panel.coupon.ItemUi.superclass.initComponent.call(this);
	},


	afterRender : function()
	{
		var label = this.record ? this.record.data['coupon.label'] : 'new';

		this.setTitle( 'Coupon: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.product.ItemUi.superclass.afterRender.apply( this, arguments );
	},


	onBeforeSave: function( store, data ) {
		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.coupon.configui' );
		var first = editorGrid.shift();

		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( ( key = key.trim() ) !== '' ) {
						config[key] = value;
					}
				}, this);
			});
		}

		if( data.create && data.create[0] ) {
			data.create[0].data['coupon.config'] = config;
		} else if( data.update && data.update[0] ) {
			data.update[0].data['coupon.config'] = config;
		}
	}
});

Ext.reg('MShop.panel.coupon.itemui', MShop.panel.coupon.ItemUi);