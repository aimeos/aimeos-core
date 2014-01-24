/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product');

MShop.panel.product.ItemUi = Ext.extend(MShop.panel.AbstractListItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'product.siteid',

	initComponent : function() {

		this.title = _('Product item details');

		MShop.panel.AbstractListItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.product.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.product.ItemUi.BasicPanel',
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
						defaults: {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'product.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'product.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'product.typeid',
							mode : 'local',
							store : MShop.GlobalStoreMgr.get('Product_Type'),
							displayField : 'product.type.label',
							valueField : 'product.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Product bundle, selection or article (required)'),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'product.type.code', 'default' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'product.code',
							allowBlank : false,
							maxLength : 32,
							emptyText : _('EAN, SKU or article numer (required)')
						}, {
							xtype : 'textarea',
							fieldLabel : _('Label'),
							name : 'product.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Internal product name (required)')
						}, {
							xtype : 'combo',
							fieldLabel : _('Supplier'),
							name : 'product.suppliercode',
							store : MShop.GlobalStoreMgr.createStore('Supplier'),
							displayField : 'supplier.label',
							valueField : 'supplier.label',
							forceSelection : true,
							triggerAction : 'all',
							submitValue : true,
							typeAhead : true,
							emptyText : _('Product supplier (optional)')
						}, {
							xtype : 'datefield',
							fieldLabel : _('Available from'),
							name : 'product.datestart',
							format : 'Y-m-d H:i:s',
							emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
						}, {
							xtype : 'datefield',
							fieldLabel : _('Available until'),
							name : 'product.dateend',
							format : 'Y-m-d H:i:s',
							emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'product.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'product.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'product.editor'
						} ]
					} ]
				},{
					xtype: 'MShop.panel.product.stock.listuismall',
					layout: 'fit',
					flex: 1
				} ]
			} ]
		} ];

		MShop.panel.product.ItemUi.superclass.initComponent.call(this);
	},
	

	afterRender : function() {

		var label = this.record ? this.record.data['product.label'] : 'new';

		this.setTitle( 'Product: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		
		MShop.panel.product.ItemUi.superclass.afterRender.apply( this, arguments );
	},
	
	
	onStoreWrite : function(store, action, result, transaction, rs) {

        var records = Ext.isArray(rs) ? rs : [rs];
        var ids = [];

        MShop.panel.product.ItemUi.superclass.onStoreWrite.apply( this, arguments );
        
        for( var i = 0; i < records.length; i++ ) {
        	ids.push( records[i].id );
        }
         
        MShop.API.Product.finish( MShop.config.site["locale.site.code"], ids );
	}
});

Ext.reg('MShop.panel.product.itemui', MShop.panel.product.ItemUi);
