/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: init.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


/* superglobal lang stubs */
_ = function(m) {return m; };
n_ = function(s,p,n) {return ( n > 1 ? p : s ); };

Ext.onReady(function() {

	Ext.ns('MShop.API');
    
    // init jsonSMD
    Ext.Direct.addProvider(Ext.apply(MShop.config.smd, {
        'type'              : 'jsonrpcprovider',
        'namespace'         : 'MShop.API',
        'url'               : MShop.config.smd.target,
        'useNamedParams'    : true
    }));
    
    // init schemas
    MShop.Schema.register(MShop.config.itemschema, MShop.config.searchschema);
    
    // no endswith textfilters operators
    Ext.ux.AdvancedSearch.TextFilter.prototype.operators = ['equals', 'contains', 'startswith'];
    
    MShop.urlManager = new MShop.UrlManager( window.location.href );
    
    // build interface
    new Ext.Viewport({
        layout: 'fit',
        items: [{
            layout: 'fit',
            border: false,
            tbar: ['->', /*_('Site:'),*/ {xtype: 'MShop.elements.site.combo'}],
            items: [{
                xtype: 'tabpanel',
                border: false,
                activeTab: MShop.urlManager.getActiveTab(),
                id: 'MShop.MainTabPanel',
                itemId: 'MShop.MainTabPanel',
                plugins: ['ux.itemregistry']
            }]
        }]
    });
});
