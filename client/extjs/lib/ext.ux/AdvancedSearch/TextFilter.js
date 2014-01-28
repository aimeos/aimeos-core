/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: TextFilter.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

Ext.ux.AdvancedSearch.TextFilter = Ext.extend(Ext.ux.AdvancedSearch.Filter, {

    operators: ['==', '~=', '=~'],
    defaultOperator: '=~',
    defaultValue: '',


    initComponent: function() {
        Ext.ux.AdvancedSearch.TextFilter.superclass.initComponent.call(this);
    }

});

Ext.reg('ux.textfilter', Ext.ux.AdvancedSearch.TextFilter);