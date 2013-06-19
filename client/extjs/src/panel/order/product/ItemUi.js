/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.order.product' );

MShop.panel.order.product.ItemUi = Ext.extend( MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'order.base.product.siteid',

	initComponent : function() {

		this.title = _('Product item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.order.product.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _( 'Product' ),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.order.product.ItemUi.BasicPanel',
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
						labelAlign : 'left',
						defaults: {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'order.base.product.id'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Product ID' ),
							name : 'order.base.product.productid'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Order Product ID' ),
							name : 'order.base.product.orderproductid'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Type' ),
							name : 'order.base.product.type'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Code' ),
							name : 'order.base.product.prodcode'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Name' ),
							name : 'order.base.product.name'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Quantity' ),
							name : 'order.base.product.quantity'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Price' ),
							name : 'order.base.product.price'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Shipping' ),
							name : 'order.base.product.shipping'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Rebate' ),
							name : 'order.base.product.rebate'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'Tax rate in %' ),
							name : 'order.base.product.taxrate'
						}, {
							xtype : 'ux.formattabledisplayfield',
							fieldLabel : _( 'Status' ),
							name : 'order.base.product.status',
							renderer : MShop.elements.deliverystatus.renderer
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'order.base.product.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'order.base.product.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'order.base.product.editor'
						} ]
					} ]
				},{
					xtype: 'MShop.panel.order.base.product.attribute.listuismall',
					layout: 'fit',
					flex: 1,
					onOpenEditWindow: function(){}
				} ]
			} ]
		} ];

		MShop.panel.order.product.ItemUi.superclass.initComponent.call( this );
	}
});

Ext.reg( 'MShop.panel.order.product.itemui', MShop.panel.order.product.ItemUi );
