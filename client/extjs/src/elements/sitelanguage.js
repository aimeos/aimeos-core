/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
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
//        listeners: 
//        { 
//        	'render' : 
//	        {
//				fn: function(combo, metaData, record, rowIndex, colIndex, store) {
//					if(combo.getValue() == 'all') {
//						combo.setValue('all');
//					} else {
//						console.log( combo );
//						console.log(combo.getRawValue());
//						var lang = MShop.elements.sitelanguage.getStore().getById(combo.getRawValue());
//						
//						var mappedLang = MShop.elements.sitelanguage.getLanguagesStore().getById(lang);
//						combo.setValue( mappedLang );
//					}
//				}
//			}
//			'expand' :
//			{
//				fn: function(langId, metaData, record, rowIndex, colIndex, store) {
//					var lang = MShop.elements.sitelanguage.getStore().getById(langId);
//					var mappedLang = MShop.elements.sitelanguage.getLanguagesStore().getById(lang);
//					console.log( 'expand' );
//					metaData.css = 'statustext-' + ( lang ? Number( lang.get('locale.status') ) : '1' );
//					this.setValue( mappedLang ) || this.setValue('all');
//				}
//			}
//		}
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
	var mappedLang = MShop.elements.sitelanguage.getLanguagesStore().getById(lang);

    metaData.css = 'statustext-' + ( lang ? Number( lang.get('locale.status') ) : '1' );

    return mappedLang || _('all');
};


/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.sitelanguage.getStore = function() {
    if (! MShop.elements.sitelanguage._store) {
        MShop.elements.sitelanguage._store = MShop.GlobalStoreMgr.createStore('Locale', {
            remoteSort: true,
            autoLoad: true,
            sortInfo: {
                field: 'locale.status',
                direction: 'DESC'
            }
        });
        
        MShop.elements.sitelanguage._store.each(function (el) {
        	console.log(el.toString());
        });
        console.error(MShop.elements.sitelanguage._store.count());
    }
    
    return MShop.elements.sitelanguage._store;
};


MShop.elements.sitelanguage.getLanguagesStore = function() {
    if (! MShop.elements.sitelanguage._langstore) {
        MShop.elements.sitelanguage._langstore = MShop.GlobalStoreMgr.createStore('Locale_Language', {
            remoteSort: true,
            sortInfo: {
                field: 'locale.status',
                direction: 'DESC'
            }
        });
    }
    
    return MShop.elements.sitelanguage._langstore;
};

// preload
Ext.onReady(function() {
	MShop.elements.sitelanguage.getStore().load(); 
	MShop.elements.sitelanguage.getLanguagesStore().load(); 
});
