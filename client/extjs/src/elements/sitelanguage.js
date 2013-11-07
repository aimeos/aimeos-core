/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.sitelanguage');

MShop.elements.sitelanguage.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel: _('Language'),
        anchor: '100%',
        store: MShop.elements.sitelanguage.getStore(),
        mode: 'local',
        displayField: 'locale.language.label',
        valueField: 'locale.language.id',
        statusField: 'locale.language.status',
        triggerAction: 'all',
        pageSize: 20,
        emptyText: _('all'),
        typeAhead: true
    });
    
    MShop.elements.sitelanguage.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.sitelanguage.ComboBox, Ext.ux.form.ClearableComboBox);

Ext.reg('MShop.elements.sitelanguage.combo', MShop.elements.sitelanguage.ComboBox);


/**
 * @static
 * 
 * @param {String} langId
 * @return {String} label
 */
MShop.elements.sitelanguage.renderer = function(langId, metaData, record, rowIndex, colIndex, store) {

	var lang = MShop.elements.sitelanguage.getStore().getById(langId);

    metaData.css = 'statustext-' + ( lang ? Number( lang.get('locale.language.status') ) : '1' );

    return langId || _('all');
};


/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.sitelanguage.getStore = function() {
    if (! MShop.elements.sitelanguage._store) {
        MShop.elements.sitelanguage._store = MShop.GlobalStoreMgr.createStore('Locale_Language', {
            remoteSort: true,
            sortInfo: {
                field: 'locale.language.status',
                direction: 'DESC'
            }
        });
    }
    
    return MShop.elements.sitelanguage._store;
};

// preload
Ext.onReady(function() { MShop.elements.sitelanguage.getStore().load(); });
