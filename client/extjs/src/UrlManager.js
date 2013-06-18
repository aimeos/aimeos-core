/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop');

MShop.UrlManager = function( href ) {
		this.href = href || null;
		this.tmpl = new Ext.Template( MShop.config.urlTemplate );
		this.data = {
			site: MShop.config.site['locale.site.code'],
			tab : MShop.config.activeTab
		};
};

MShop.UrlManager.prototype = {
		redirect: function( config )
		{
			if( typeof config == 'object' )
			{
				if( config.hasOwnProperty( 'site' ) ) {
					this.setSiteCode( config.site );
				}

				if( config.hasOwnProperty( 'tab' ) ) {
					this.setActiveTab( config.tab );
				}
			}
			window.location.href = this.tmpl.apply( this.data );
		},

		setActiveTab: function( value ) {
			this.data.tab = parseInt( value, 10 );
		},

		getActiveTab: function() {
			return this.data.tab;
		},

		setSiteCode: function( siteCode ) {
			this.data.site = siteCode;
		}
};
