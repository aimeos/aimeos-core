/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.status');

MShop.elements.status.ComboBox = function(config) {
    Ext.applyIf(config, {
        fieldLabel: _('Status'),
        anchor: '100%',
        store: MShop.elements.status._store,
        mode: 'local',
        displayField: 'label',
        valueField: 'value',
        triggerAction: 'all',
        typeAhead: true,
		value: 1
    });

    MShop.elements.status.ComboBox.superclass.constructor.call(this, config);
};

Ext.extend(MShop.elements.status.ComboBox, Ext.form.ComboBox);

Ext.reg('MShop.elements.status.combo', MShop.elements.status.ComboBox);


/**
 * @static
 *
 * @return {Ext.data.DirectStore}
 */
MShop.elements.status._store = new Ext.data.ArrayStore({
    idIndex : 0,
    fields : [
       {name: 'value', type: 'integer'},
       {name: 'label', type: 'string'}
    ],
    data : [
    	[-2, _('archive')],
    	[-1, _('review')],
    	[0, _('disabled')],
    	[1, _('enabled')]
    ]
});

