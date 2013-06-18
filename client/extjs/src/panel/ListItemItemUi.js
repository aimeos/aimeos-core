/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 *
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

MShop.panel.ListItemItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	layout : 'fit',
	modal : true,
	getAdditionalFields : Ext.emptyFn,

	initComponent : function() {

		this.title = _('List item details');

		this.items = [{
			xtype : 'form',
			border : false,
			layout : 'fit',
			flex : 1,
			ref : 'mainForm',
			items : [{
				xtype : 'fieldset',
				border : false,
				flex : 1,
				labelAlign : 'top',
				items : [{
					xtype : 'combo',
					fieldLabel : _('List type'),
					name : this.listUI.listNamePrefix + 'typeid',
					mode : 'local',
					store : this.listUI.itemTypeStore,
					valueField : this.listUI.listTypeIdProperty,
					displayField : this.listUI.listTypeLabelProperty,
					forceSelection : false,
					triggerAction : 'all',
					allowBlank : false,
					typeAhead : true,
					anchor : '100%',
					emptyText : _('List type')
				}, {
					xtype : 'datefield',
					fieldLabel : _('Available from'),
					name : this.listUI.listNamePrefix + 'datestart',
					format : 'Y-m-d H:i:s',
					anchor : '100%',
					emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
				}, {
					xtype : 'datefield',
					fieldLabel : _('Available until'),
					name : this.listUI.listNamePrefix + 'dateend',
					format : 'Y-m-d H:i:s',
					anchor : '100%',
					emptyText : _('YYYY-MM-DD hh:mm:ss (optional)')
				}].concat( this.getAdditionalFields() || [] )
			}]
		}];

		MShop.panel.ListItemItemUi.superclass.initComponent.call(this);
	}
});

Ext.reg('MShop.panel.listitemitemui', MShop.panel.ListItemItemUi);
