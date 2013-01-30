/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.product.stock' );

MShop.panel.product.stock.ItemUi = Ext.extend( MShop.panel.AbstractItemUi, {

	recordName : 'Product_Stock',
	idProperty : 'product.stock.id',
	siteidProperty : 'product.stock.siteid',

	initComponent : function() {

		this.title = _( 'Stock & warehouse' );
		
		if(this.copyActive){
			this.record.data['product.stock.id'] = null;
		}

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.product.stock.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _( 'Basic' ),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.product.stock.ItemUi.BasicPanel',
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
					id: 'MShop.panel.product.stock.ItemUi.BasicPanel.Title',
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
							xtype : 'hidden',
							name : 'product.stock.productid'
						}, {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'product.stock.id'
						}, {
							xtype : 'combo',
							fieldLabel : 'Warehouse',
							name : 'product.stock.warehouseid',
							mode : 'local',
							store : MShop.GlobalStoreMgr.get( 'Product_Stock_Warehouse', this.domain ),
							displayField : 'product.stock.warehouse.label',
							valueField : 'product.stock.warehouse.id',
							forceSelection : true,
							triggerAction : 'all',
							typeAhead : true,
							emptyText : _( 'Product repository (required)' ),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'product.stock.warehouse.code', 'default' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'numberfield',
							fieldLabel : 'Stock level',
							name : 'product.stock.stocklevel',
							emptyText : _( 'Quantity or empty if unlimited (optional)' )
						}, {
							xtype : 'datefield',
							fieldLabel : 'Back in stock',
							name : 'product.stock.dateback',
							format : 'Y-m-d H:i:s',
							emptyText : _( 'YYYY-MM-DD hh:mm:ss (optional)' )
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'product.stock.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'product.stock.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'product.stock.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.product.stock.ItemUi.superclass.initComponent.call( this );
	},
	
	afterRender: function()
	{
		MShop.panel.product.stock.ItemUi.superclass.afterRender.apply( this, arguments );
		
		var oldTitle = Ext.getCmp('MShop.panel.product.stock.ItemUi.BasicPanel.Title').title;
		Ext.getCmp('MShop.panel.product.stock.ItemUi.BasicPanel.Title').setTitle( this.listUI.itemUi.record.data['product.label'] + ' - ' + oldTitle );
	},


	onSaveItem : function()
	{
		// validate data
		if( !this.mainForm.getForm().isValid() && this.fireEvent( 'validate', this ) !== false ) {
			Ext.Msg.alert( _( 'Invalid Data' ), _( 'Please recheck you data' ) );
			return;
		}

		this.saveMask.show();
		this.isSaveing = true;

		// force record to be saved!
		this.record.dirty = true;

		if( this.fireEvent( 'beforesave', this, this.record ) === false ) {
			this.isSaveing = false;
			this.saveMask.hide();
		}

		this.mainForm.getForm().updateRecord( this.record );
		this.record.data['product.stock.productid'] = this.listUI.itemUi.record.id;

		if( this.isNewRecord ) {
			this.store.add( this.record );
		}

		// store async action is triggered. {@see onStoreWrite/onStoreException}
		if( !this.store.autoSave ) {
			this.onAfterSave();
		}
	},


	onStoreException: function()
	{
		this.store.remove( this.record );
		MShop.panel.product.stock.ItemUi.superclass.onStoreException.apply( this, arguments );
	}
});

Ext.reg( 'MShop.panel.product.stock.itemui', MShop.panel.product.stock.ItemUi );