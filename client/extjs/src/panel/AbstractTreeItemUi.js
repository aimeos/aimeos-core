/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

/**
 * Abtract Tree ItemUi
 *
 * subclasses need to provide
 * - this.items
 * - this.mainForm reference
 * - this.treeUi
 *
 * @namespace   MShop
 * @class       MShop.panel.AbstractTreeItemUi
 * @extends     Ext.Window
 */
MShop.panel.AbstractTreeItemUi = Ext.extend(MShop.panel.AbstractItemUi, {
    /**
     * Action from treeUi
     * default is "add": creating new entry as phantom
     */
    action: 'add',
    
    /**
     * Reference to the parent treeUi
     * Where this itemUi is opened from
     */
    treeUI: null,
    
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

        if (this.action == 'copy') {
            this.items[0].deferredRender = false;
        }
        
        MShop.panel.AbstractItemUi.superclass.initComponent.call(this);
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

        this.mainForm.getForm().updateRecord(this.record);
        
        if (this.action == 'copy') {
            this.record.id = null;
            this.record.phantom = true;
        }

        if (this.action == 'copy' || this.action == 'add') {
            this.store.add(this.record);
        }

        // store async action is triggered. {@see onStoreWrite/onStoreException}
        if (! this.store.autoSave) {
            this.onAfterSave();
        }
    },

    onStoreException: function(proxy, type, action, options, response) {
        if (/*itwasus &&*/ this.isSaveing) {
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

    onAfterSave: function() {
        this.isSaveing = false;
        this.saveMask.hide();

        this.close();
    }
});

// NOTE: we need to register this abstract class so getByXtype can find decedents
// do we rly need to get this abstract this way? i think this is not used yet and we should think about keeping this register as small as possible
Ext.reg('MShop.panel.abstracttreeitemui', MShop.panel.AbstractTreeItemUi);
