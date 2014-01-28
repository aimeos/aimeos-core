/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://www.arcavias.com/license
 */

Ext.ns('MShop');

MShop.I18n = {
	
	translations: {},
	
	
	init : function( translations ) {
	},

	
	dt: function( domain, string ) {
		return string;
	},
	
	
	dn: function( domain, singular, plural, num ) {
		return singular;
	}
};
