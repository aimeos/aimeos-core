/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements.deliverystatus');

/**
 * @static
 * 
 * @return {String} label
 */
MShop.elements.deliverystatus.renderer = function(value) {
	var data = MShop.elements.deliverystatus._store.getAt( MShop.elements.deliverystatus._store.find('value', value) );
	
	if( data ) {
		return data.get('label');
	}

	return value;
};

/**
 * @static
 * 
 * @return {Ext.data.DirectStore}
 */
MShop.elements.deliverystatus._store = new Ext.data.ArrayStore({
	idIndex : 0,
	fields : [
		{name: 'value', type: 'integer'},
		{name: 'label', type: 'string'}
	],
	data : [
		[-1, _('unfinished')],
		[0, _('deleted')],
		[1, _('pending')],
		[2, _('progress')],
		[3, _('dispatched')],
		[4, _('delivered')],
		[5, _('lost')],
		[6, _('refused')],
		[7, _('returned')]
	]
});

