/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: DateFilter.js 14664 2012-01-03 16:38:41Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

Ext.ux.AdvancedSearch.DateFilter = Ext.extend(Ext.ux.AdvancedSearch.Filter, {

	defaultOperator : 'equals',

	defaultValue : '',

	// _('equals') _('before') _('after')
	operators : ['equals', 'before', 'after'],


	initComponent : function() {

		this.valueFieldConfig = {
			xtype: 'datefield',
			format: 'Y-m-d H:i:s'
		};

		Ext.ux.AdvancedSearch.DateFilter.superclass.initComponent.call(this);
	},


	getValue : function() {
		
		v = Ext.ux.AdvancedSearch.DateFilter.superclass.getValue.call(this);

		if( v ) {
			return new Date( v ).format( 'Y-m-d H:i:s' );
		}

		return v;
	}
});

Ext.reg( 'ux.datefilter', Ext.ux.AdvancedSearch.DateFilter );
