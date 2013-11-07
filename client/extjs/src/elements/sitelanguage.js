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
        displayField: 'locale.languageid',
        valueField: 'locale.languageid',
        statusField: 'locale.status',
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

//	var lang = MShop.elements.sitelanguage.getStore().getById(langId);
//
//    metaData.css = 'statustext-' + ( lang ? Number( lang.get('locale.status') ) : '1' );
//
//    return langId || _('all');
};


MShop.elements.sitelanguage.getFields = function() {
	
}

/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.sitelanguage.getStore = function() {
	var newEntry = Ext.data.Record.create([
			{name:'locale.languageid', mapping:'locale.languageid' },
			{name:'locale.status', mapping:'locale.status' },
			{name:'locale.language.label', mapping:'locale.language.label' }
		] 
	);
	
	
	
	
	if (! MShop.elements.sitelanguage._store) {
        MShop.elements.sitelanguage._store = MShop.GlobalStoreMgr.createStore('Locale', {
            remoteSort: true,
            sortInfo: {
                field: 'locale.status',
                direction: 'DESC'
            }
        });
    }
    
    if (! MShop.elements.sitelanguage._languages) {
        MShop.elements.sitelanguage._languages = MShop.GlobalStoreMgr.createStore('Locale_Language', {
            remoteSort: true,
            sortInfo: {
                field: 'locale.language.status',
                direction: 'DESC'
            }
        });
        
        
        var langid = [];
        var status = [];
        MShop.elements.sitelanguage._store.load(function(){
		    this.each(function(record){
		        langid.push(record.get('locale.languageid'));
		        status.push(record.get('locale.status'));
		       // Do stuff with value
		    })
		});

		    
    if(! MShop.elements.sitelanguage._newstore ) {
    	MShop.elements.sitelanguage._newstore = new Ext.data.DirectStore(Ext.apply({
    		autoLoad: true
    	}));
    }
    
    return MShop.elements.sitelanguage._newstore;
};


MShop.elements.sitelanguage.getLanguageStore = function() {
	   if (! MShop.elements.sitelanguage._languages) {
        MShop.elements.sitelanguage._languages = MShop.GlobalStoreMgr.createStore('Locale_Language', {
            remoteSort: true,
            sortInfo: {
                field: 'locale.language.status',
                direction: 'DESC'
            }
        });
        
        return MShop.elements.sitelanguage._languages;
    }
};

// preload
Ext.onReady(function() { 
MShop.elements.sitelanguage.getLanguageStore().load(); 
MShop.elements.sitelanguage.getStore().load(); 
})};
