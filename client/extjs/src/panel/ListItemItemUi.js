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
			flex : 1,
			layout: 'hbox',
			layoutConfig : {
				align : 'stretch'
			},
			ref : 'mainForm',
			autoScroll : true,
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
			}, {
					xtype: 'MShop.panel.listconfigui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get(this.listUI.listNamePrefix +'config') : {} )
			} ]
		}];
		
		MShop.panel.ListItemItemUi.superclass.initComponent.call(this);
	},
	
	onSaveItem: function() {
		// validate data
		if (! this.mainForm.getForm().isValid() && this.fireEvent('validate', this) !== false) {
			Ext.Msg.alert(_('Invalid Data'), _('Please recheck you data'));
			return;
		}

		this.saveMask.show();
		this.isSaveing = true;

		// force record to be saved!
		this.record.dirty = true;

		this.getConfigRecords(this.store, this.record);
		
		if (this.fireEvent('beforesave', this, this.record) === false) {
			this.isSaveing = false;
			this.saveMask.hide();
		}

		console.log("died?");
		
		var recordRefIdProperty = this.listUI.listNamePrefix + "refid";
		var recordTypeIdProperty = this.listUI.listNamePrefix + "typeid";
		
		var index = this.store.findBy(function (item, index) {
			var recordRefId = this.record.get(recordRefIdProperty);
			var recordTypeId = this.mainForm.getForm().getFieldValues()[recordTypeIdProperty];
	
			var itemRefId = item.get(recordRefIdProperty);
			var itemTypeId = item.get(recordTypeIdProperty);
			
			var recordId = this.record.id;
			var itemId = index;
			
			if (! recordRefId || ! recordTypeId || ! itemRefId || ! itemTypeId)
				return false;
			
			return ( recordRefId == itemRefId && recordTypeId == itemTypeId && recordId != itemId );
		}, this);
		
		if (index != -1) {
			this.isSaveing = false;
			this.saveMask.hide();
			Ext.Msg.alert(_('Invalid Data'), _('This combination does already exist.'));
			return;
		}
		
		this.mainForm.getForm().updateRecord(this.record);
		console.log(this.record);
		
		if (this.isNewRecord) {
			this.store.add(this.record);
		}

		// store async action is triggered. {@see onStoreWrite/onStoreException}
		if (! this.store.autoSave) {
			this.onAfterSave();
		}
	},
	
	getConfigRecords: function( store, record ) {
		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.listconfigui' );
		var first = editorGrid.shift();
		
		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( key.trim() !== '' ) {
						config[key] = value.trim();
					}
				}, this);
			});
		}
		record.data[this.listUI.listNamePrefix + 'config'] = config;
	}
	
});

Ext.reg('MShop.panel.listitemitemui', MShop.panel.ListItemItemUi);
