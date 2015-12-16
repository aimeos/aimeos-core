/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.elements.domain');

MShop.elements.domain.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel : MShop.I18n.dt('admin', 'Domain'),
        anchor : '100%',
        store : MShop.elements.domain.getStore(),
        mode : 'local',
        displayField : 'label',
        emptyText : MShop.I18n.dt('admin', 'Domain (required)'),
        valueField : 'value',
        triggerAction : 'all',
        allowEmpty : false,
        typeAhead : true
    });

    MShop.elements.domain.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.domain.ComboBox, Ext.form.ComboBox);

Ext.reg('MShop.elements.domain.combo', MShop.elements.domain.ComboBox);

/**
 * @static
 * @return {Ext.data.DirectStore}
 */
MShop.elements.domain.getStore = function() {

    if(!MShop.elements.domain._store) {

        MShop.elements.domain._store = new Ext.data.ArrayStore({
            idIndex : 0,
            fields : [{
                name : 'value',
                type : 'string'
            }, {
                name : 'label',
                type : 'string'
            }],
            data : [
                ['attribute', MShop.I18n.dt('admin', 'Attribute')],
                ['catalog', MShop.I18n.dt('admin', 'Catalog')],
                ['customer', MShop.I18n.dt('admin', 'Customer')],
                ['media', MShop.I18n.dt('admin', 'Media')],
                ['plugin', MShop.I18n.dt('admin', 'Plugin')],
                ['price', MShop.I18n.dt('admin', 'Price')],
                ['product', MShop.I18n.dt('admin', 'Product')],
                ['service', MShop.I18n.dt('admin', 'Service')],
                ['supplier', MShop.I18n.dt('admin', 'Supplier')],
                ['text', MShop.I18n.dt('admin', 'Text')]]
        });
    }

    return MShop.elements.domain._store;
};
