/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.language');

MShop.elements.language.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel: _('Language'),
        anchor: '100%',
        store: MShop.elements.language.getStore(),
        mode: 'local',
        displayField: 'locale.language.label',
        valueField: 'locale.language.id',
        statusField: 'locale.language.status',
        triggerAction: 'all',
        pageSize: 20,
        emptyText: _('all'),
        typeAhead: true
    });
    
    MShop.elements.language.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.language.ComboBox, Ext.ux.form.ClearableComboBox);

Ext.reg('MShop.elements.language.combo', MShop.elements.language.ComboBox);


/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.language.getStore = function() {
    if (! MShop.elements.language._store) {
        MShop.elements.language._store = MShop.GlobalStoreMgr.createStore('Locale_Language', {
            remoteSort: true,
            sortInfo: {
                field: 'locale.language.status',
                direction: 'DESC'
            }
        });
    }
    
    return MShop.elements.language._store;
};

// preload
Ext.onReady(function() { MShop.elements.language.getStore().load(); });
