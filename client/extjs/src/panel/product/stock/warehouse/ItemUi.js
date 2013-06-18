/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemUi.js 14341 2011-12-14 16:00:50Z nsendetzky $
 */


Ext.ns( 'MShop.panel.stock.warehouse' );

MShop.panel.stock.warehouse.ItemUi = Ext.extend( MShop.panel.AbstractItemUi, {
	
	recordName : 'Product_Stock_Warehouse',
	idProperty : 'product.stock.warehouse.id',
	siteidProperty : 'product.stock.warehouse.siteid',

	initComponent : function() {

		this.title = _( 'Warehouse' );
				
		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.stock.warehouse.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _( 'Basic' ),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.stock.warehouse.ItemUi.BasicPanel',
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
							name : 'product.stock.warehouse.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'product.stock.warehouse.status'
						}, {
							xtype : 'textfield',
							fieldLabel : 'Warehouse code',
							name : 'product.stock.warehouse.code',
							allowBlank : false,
							maxLength : 32,
							emptyText : _( 'Warehouse code (required)' )
						}, {
							xtype : 'textfield',
							fieldLabel : 'Warehouse label',
							name : 'product.stock.warehouse.label',
							allowBlank : false,
							emptyText : _( 'Warehouse label (required)' )
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'product.stock.warehouse.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'product.stock.warehouse.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'product.stock.warehouse.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.stock.warehouse.ItemUi.superclass.initComponent.call( this );
	},


	afterRender : function()
	{
		var label = this.record ? this.record.data['product.stock.warehouse.label'] : 'new';
		this.setTitle( 'Warehouse: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.stock.warehouse.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg( 'MShop.panel.stock.warehouse.itemui', MShop.panel.stock.warehouse.ItemUi );