/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.domain');

MShop.elements.domain.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel: MShop.I18n.dt( 'client/extjs', 'Domain' ),
        anchor: '100%',
        store: MShop.elements.domain._store,
        mode: 'local',
        displayField: 'label',
        emptyText : MShop.I18n.dt( 'client/extjs', 'Domain (required)' ),
        valueField: 'value',
        triggerAction: 'all',
        allowEmpty: false,
        typeAhead: true
    });

    MShop.elements.domain.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.domain.ComboBox, Ext.form.ComboBox);

Ext.reg('MShop.elements.domain.combo', MShop.elements.domain.ComboBox);

MShop.elements.domain.renderer = function(id, metaData, record, rowIndex, colIndex, store) {

    var value = MShop.elements.domain._store.getById(id);

    if( value ) {
    	metaData.css = 'text_type_' + value.get('value');
    	return label;
    }

    return value;
};

/**
 * @static
 *
 * @return {Ext.data.DirectStore}
 */
MShop.elements.domain._store = new Ext.data.ArrayStore({
    idIndex : 0,
    fields : [
       {name: 'value', type: 'string'},
       {name: 'label', type: 'string'}
    ],
    data : [
  		['attribute', MShop.I18n.dt( 'client/extjs', 'Attribute' ) ],
    	['catalog', MShop.I18n.dt( 'client/extjs', 'Catalog' ) ],
    	['customer', MShop.I18n.dt( 'client/extjs', 'Customer' ) ],
    	['media', MShop.I18n.dt( 'client/extjs', 'Media' ) ],
    	['plugin', MShop.I18n.dt( 'client/extjs', 'Plugin' ) ],
    	['price', MShop.I18n.dt( 'client/extjs', 'Price' ) ],
    	['product', MShop.I18n.dt( 'client/extjs', 'Product' ) ],
    	['service', MShop.I18n.dt( 'client/extjs', 'Service' ) ],
    	['supplier', MShop.I18n.dt( 'client/extjs', 'Supplier' ) ],
    	['text', MShop.I18n.dt( 'client/extjs', 'Text' ) ]
    ]
});