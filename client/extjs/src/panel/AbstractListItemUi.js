/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel');

/**
 * Abtract List ItemUi
 * 
 * For uses by parent listUi
 *
 * subclasses need to provide
 * - this.items
 * - this.mainForm reference
 * - this.listUI
 *
 * @namespace   MShop
 * @class       MShop.panel.AbstractListItemUi
 * @extends     MShop.panel.AbstractItemUi
 */
MShop.panel.AbstractListItemUi = Ext.extend(MShop.panel.AbstractItemUi, {
    /**
     * Reference to his parent listUi, a itemUi can't be opened without any parent referenc
     * 
     * @required
     */
    listUI: null,

    /**
     * @todo consider to move all these events to his abstracts
     */
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

});

// NOTE: we need to register this abstract class so getByXtype can find decedents
// This may be not required to a abstract class due to ExtJS.extends
Ext.reg('MShop.panel.abstractlistitemui', MShop.panel.AbstractListItemUi);