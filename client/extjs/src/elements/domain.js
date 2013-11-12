/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.domain');

MShop.elements.domain.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel: _('Domain'),
        anchor: '100%',
        store: MShop.elements.domain._store,
        mode: 'local',
        displayField: 'label',
        emptyText : _('Domain (required)'),
        valueField: 'value',
        triggerAction: 'all',
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
  		['attribute', _('Attribute')],
    	['product', _('Product')],
    	['media', _('Media')],
    	['catalog', _('Catalog')],
    	['service', _('Service')],
    	['plugin', _('Plugin')],
    	['customer', _('Customer')],
    	['supplier', _('Supplier')],
    	['text', _('Text')],
    	['price', _('Price')]
    ]
});