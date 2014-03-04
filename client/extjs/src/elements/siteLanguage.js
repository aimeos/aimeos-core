/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://www.arcavias.com/en/license
 */

Ext.ns('MShop.elements.siteLanguage');

MShop.elements.siteLanguage.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel : MShop.I18n.dt('client/extjs', 'Language'),
        anchor : '100%',
        store : MShop.elements.siteLanguage.getStore(),
        mode : 'local',
        displayField : 'locale.language.label',
        valueField : 'locale.language.id',
        statusField : 'locale.language.status',
        triggerAction : 'all',
        pageSize : 20,
        emptyText : MShop.I18n.dt('client/extjs', 'All'),
        typeAhead : true
    });

    MShop.elements.siteLanguage.ComboBox.superclass.constructor.call(this, config);
};

/**
 * @static
 * 
 * @param {String} langId
 * @return {String} label
 */
MShop.elements.siteLanguage.renderer = function(langId, metaData, record, rowIndex, colIndex, store) {
    var language = MShop.elements.siteLanguage.getStore().getById(langId);

    if( language ) {
        metaData.css = 'statustext-' + Number( language.get( 'locale.language.status' ) );
        return language.get( 'locale.language.label' );
    }

    metaData.css = 'statustext-1';
    return langId || MShop.I18n.dt( 'client/extjs', 'All' );
};

Ext.extend(MShop.elements.siteLanguage.ComboBox, Ext.form.ComboBox, {

    initComponent : function() {
        this.store = MShop.elements.siteLanguage.getStore();
        this.on('select', this.onSiteLanguageSelect, this);
        MShop.elements.siteLanguage.ComboBox.superclass.initComponent.call(this);
    },

    onSiteLanguageSelect : function(ComboBox, language) {
        var mainTabPanel = Ext.getCmp('MShop.MainTabPanel'),
            activeTabPanel = mainTabPanel.getActiveTab(),
            domainTabIdx = mainTabPanel.items.indexOf(activeTabPanel),
            languageCode = language ? language.get('locale.language.code') : 'en';

        new Ext.LoadMask(Ext.getBody(), {
            msg : MShop.I18n.dt('client/extjs', 'Switching site language ...')
        }).show();
        
        console.log(languageCode);
        
        MShop.urlManager.redirect({
            locale : languageCode
        });
    }
});

Ext.reg('MShop.elements.siteLanguage.combo', MShop.elements.siteLanguage.ComboBox);

/**
 * @static
 *
 * @return {Ext.data.DirectStore}
 */
MShop.elements.siteLanguage.getStore = function() {
    if (!MShop.elements.siteLanguage._store) {
        MShop.elements.siteLanguage._store = MShop.GlobalStoreMgr.createStore('Locale_Language', {
            remoteSort : true,
            sortInfo : {
                field : 'locale.language.label',
                direction : 'ASC'
            }
        });
    }

    return MShop.elements.siteLanguage._store;
};

//preload
Ext.onReady(function() {
    MShop.elements.siteLanguage.getStore().load();
});
