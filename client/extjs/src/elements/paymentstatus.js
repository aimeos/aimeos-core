/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: paymentstatus.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.elements.paymentstatus');

/**
 * @static
 * 
 * @return {String} label
 */
MShop.elements.paymentstatus.renderer = function(value) {
	var data = MShop.elements.paymentstatus._store.getAt( MShop.elements.paymentstatus._store.find('value', value) );

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
MShop.elements.paymentstatus._store = new Ext.data.ArrayStore({
	idIndex : 0,
	fields : [
		{name: 'value', type: 'integer'},
		{name: 'label', type: 'string'}
	],
	data : [
		[-1, _('unfinished')],
		[0, _('deleted')],
		[1, _('canceled')],
		[2, _('refused')],
		[3, _('refund')],
		[4, _('pending')],
		[5, _('authorized')],
		[6, _('received')]
	]
});