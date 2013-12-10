/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns( 'MShop.panel.locale' );

MShop.panel.locale.ListUi = Ext.extend( MShop.panel.AbstractListUi, {

	recordName : 'Locale',
	idProperty : 'locale.id',
	siteidProperty : 'locale.siteid',
	itemUiXType : 'MShop.panel.locale.itemui',

	sortInfo : {
		field : 'locale.position',
		direction : 'ASC'
	},

	autoExpandColumn : 'locale-currencyid',

	filterConfig : {
		filters : [ {
			dataIndex : 'locale.position',
			operator : 'greaterequals',
			value : 0
		} ]
	},

	initComponent : function()
	{
		this.title = _( 'Locale' );

		MShop.panel.AbstractListUi.prototype.initActions.call( this );
		MShop.panel.AbstractListUi.prototype.initToolbar.call( this );
		this.actionCopy.setHidden(true);
		
		MShop.panel.locale.ListUi.superclass.initComponent.call( this );
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'locale.id',
				header : _('ID'),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.languageid',
				header : _('Language ID'),
				sortable : true,
				width : 100
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.currencyid',
				header : _('Currency ID'),
				sortable : true,
				width : 100,
				id : 'locale-currencyid'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.position',
				header : _('Position'),
				sortable : true,
				width : 50
			}, {
				xtype : 'datecolumn',
				dataIndex : 'locale.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'locale.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	},

	initToolbar: function() {
		this.tbar = [
			this.actionAdd,
			this.actionEdit,
			this.actionDelete,
			this.actionExport,
			this.importButton
		];
	}
} );

Ext.reg('MShop.panel.locale.listui', MShop.panel.locale.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.locale.listui', MShop.panel.locale.ListUi, 80);
