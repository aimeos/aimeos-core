/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.order');

MShop.panel.order.ItemUi = Ext.extend(MShop.panel.AbstractListItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	idProperty : 'id',
	siteidProperty : 'order.siteid',

	initComponent : function() {
		this.title = _('Order item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.order.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.order.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Invoice',
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
							fieldLabel : _('ID'),
							name : 'order.id'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Source'),
							name : 'order.type'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Purchase date'),
							name : 'order.datepayment'
						}, {
							xtype : 'combo',
							fieldLabel : _('Payment status'),
							name: 'order.statuspayment',
							mode : 'local',
							store : MShop.elements.paymentstatus._store,
							displayField : 'label',
							valueField : 'value',
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Status (required)')
						}, {
							xtype : 'datefield',
							fieldLabel : 'Delivery date',
							name : 'order.datedelivery',
							format : 'Y-m-d H:i:s',
							emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
						}, {
							xtype : 'combo',
							fieldLabel : _('Delivery status'),
							name: 'order.statusdelivery',
							mode : 'local',
							store : MShop.elements.deliverystatus._store,
							displayField : 'label',
							valueField : 'value',
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Status (required)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'order.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'order.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'order.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.order.ItemUi.superclass.initComponent.call(this);
	}
});

Ext.reg('MShop.panel.order.itemui', MShop.panel.order.ItemUi);