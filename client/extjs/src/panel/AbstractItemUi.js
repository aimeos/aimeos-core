/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

/**
 * Abtract ItemUi
 *
 * subclasses need to provide
 * - this.items
 * - this.mainForm reference
 *
 * @namespace   MShop
 * @class       MShop.panel.AbstractItemUi
 * @extends     Ext.Window
 */
MShop.panel.AbstractItemUi = Ext.extend(Ext.Window, {
	/**
	 * @cfg {Ext.data.Store} store (required)
	 */
	store: null,
	/**
	 * @cfg  {Ext.data.Record} record (optional)
	 */
	record: null,
	/**
	 * @cfg {MShop.panel.AbstractListUi}
	 */
	listUI: null,
	/**
	 * @cfg {Ext.from.FormPanel} mainForm
	 */
	mainForm: null,

	/**
	 * @type Boolean isSaveing
	 */
	isSaveing: false,

	maximized : true,
	layout: 'fit',
	modal: true,

	initComponent: function() {
		this.addEvents(
			/**
			 * @event beforesave
			 * Fired before record gets saved
			 * @param {MShop.panel.AbstractItemUi} this
			 * @param {Ext.data.Record} record
			 */
			'beforesave',
			/**
			 * @event save
			 * Fired after record got saved
			 * @param {MShop.panel.AbstractItemUi} this
			 * @param {Ext.data.Record} record
			 * @param {Function} ticketFn
			 */
			'save',
			/**
			 * @event validate
			 * Fired when validating user data. return false to signal invalid data
			 * @param {MShop.panel.AbstractItemUi} this
			 * @param {Ext.data.Record} record
			 */
			'validate' );

		this.recordType = this.store.recordType;
		this.idProperty = this.idProperty || this.store.reader.meta.idProperty;

		this.initFbar();
		this.initRecord();

		this.store.on('beforewrite', this.onStoreBeforeWrite, this);
		this.store.on('exception', this.onStoreException, this);
		this.store.on('write', this.onStoreWrite, this);

		MShop.panel.AbstractItemUi.superclass.initComponent.call(this);
	},

	setSiteCheck : function( itemUi )
	{
		itemUi.fieldsReadOnly = false;
		itemUi.readOnlyClass = '';

		if( itemUi.record && (itemUi.record.get( itemUi.siteidProperty ) != MShop.config.site['locale.site.id']) )
		{
			itemUi.fieldsReadOnly = true;
			itemUi.readOnlyClass = 'site-mismatch';
		}
	},

	initFbar: function() {
		this.fbar = {
			xtype: 'toolbar',
			buttonAlign: 'right',
			hideBorders: true,
			items: [
				{
					xtype: 'button',
					text: _('Cancel'),
					width: 120,
					scale: 'medium',
					handler: this.close,
					scope: this
				},
				{
					xtype: 'button',
					text: _('Save'),
					width: 120,
					scale: 'medium',
					handler: this.onSaveItem,
					scope: this
				}
			]
		};
	},

	initRecord: function() {
		if (! this.mainForm) {
			// wait till ref if here
			return this.initRecord.defer(50, this, arguments);
		}

		if (! this.record) {
			this.record = new this.recordType();
			this.isNewRecord = true;
		}

		this.mainForm.getForm().loadRecord(this.record);

		/** @todo Is this correct? */
		return true;
	},

	afterRender: function() {
		MShop.panel.AbstractItemUi.superclass.afterRender.apply(this, arguments);

		// kill x scrollers
		this.getEl().select('form').applyStyles({'overflow-x': 'hidden'});

		this.saveMask = new Ext.LoadMask(this.el, {msg: _('Saving')});
	},

	onDestroy: function() {
		this.store.un('beforewrite', this.onStoreBeforeWrite, this);
		this.store.un('exception', this.onStoreException, this);
		this.store.un('write', this.onStoreWrite, this);

		MShop.panel.AbstractItemUi.superclass.onDestroy.apply(this, arguments);
	},

	/**
	 * if it's not us who is saving, cancle save request
	 */
	onStoreBeforeWrite: function(store, action, rs, options ) {
		var records = Ext.isArray(rs) ? rs : [rs];

		if (records.indexOf(this.record) !== -1) {
			return this.isSaveing;
		}
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

		if (this.fireEvent('beforesave', this, this.record) === false) {
			this.isSaveing = false;
			this.saveMask.hide();
		}

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

		if (this.isNewRecord) {
			this.store.add(this.record);
		}

		// store async action is triggered. {@see onStoreWrite/onStoreException}
		if (! this.store.autoSave) {
			this.onAfterSave();
		}
	},

	onStoreException: function(proxy, type, action, options, response) {
		if (this.isSaveing) {
			this.isSaveing = false;
			this.saveMask.hide();
		}
	},

	onStoreWrite: function(store, action, result, transaction, rs) {

		var records = Ext.isArray(rs) ? rs : [rs];

		if (records.indexOf(this.record) !== -1 && this.isSaveing) {
			var ticketFn = this.onAfterSave.deferByTickets(this),
				wrapTicket = ticketFn();

			this.fireEvent('save', this, this.record, ticketFn);
			wrapTicket();
		}
	},

	onAfterSave : function() {
		this.isSaveing = false;
		this.saveMask.hide();
		this.close();
	}
});

// NOTE: we need to register this abstract class so getByXtype can find decedents
Ext.reg('MShop.panel.abstractitemui', MShop.panel.AbstractItemUi);
