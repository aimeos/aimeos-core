/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: NumberFilter.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('Ext.ux.AdvancedSearch');

Ext.ux.AdvancedSearch.NumberFilter = Ext.extend(Ext.ux.AdvancedSearch.Filter, {
    
    defaultOperator: 'equals',
    
    defaultValue: '',
    
    // _('equals') _('greaterequals') _('lessequals') _('greater') _('less')
    operators: ['equals', 'greaterequals', 'lessequals', 'greater', 'less'],
    
    initComponent: function() {
        
        Ext.ux.AdvancedSearch.NumberFilter.superclass.initComponent.call(this);
    }
    
    
});

Ext.reg('ux.numberfilter', Ext.ux.AdvancedSearch.NumberFilter);