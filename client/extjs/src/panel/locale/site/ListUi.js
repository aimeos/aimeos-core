/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ListUi.js 14701 2012-01-05 08:52:24Z nsendetzky $
 */


Ext.ns( 'MShop.panel.locale.site' );

MShop.panel.locale.site.ListUi = Ext.extend( MShop.panel.AbstractListUi, {

	recordName : 'Locale_Site',
	idProperty : 'locale.site.id',
	siteidProperty : 'locale.site.id',
	itemUiXType : 'MShop.panel.locale.site.itemui',

	autoExpandColumn : 'locale-site-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'locale.site.label',
			operator : 'startswith',
			value : 0
		} ]
	},

	initComponent : function()
	{
		this.title = _( 'Locale Site' );

		MShop.panel.AbstractListUi.prototype.initActions.call( this );
		MShop.panel.AbstractListUi.prototype.initToolbar.call( this );

		this.initStore();

		MShop.panel.locale.site.ListUi.superclass.initComponent.call( this );
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.id',
				header : _('ID'),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.code',
				header : _('Code'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.label',
				header : _('Label'),
				sortable : true,
				width : 100,
				editable : false,
				id : 'locale-site-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.config',
				header : _('Configuration'),
				width : 200,
				editable : false,
				renderer: function (value) {
					var s = "";
					Ext.iterate(value, function (key, value, object) {
						s = s + String.format('<div>{0}: {1}</div>', key, value);
					}, this);
					return s;
				}
			}, {
				xtype : 'datecolumn',
				dataIndex : 'locale.site.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'locale.site.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'locale.site.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	},

	initStore: function() {
		this.store = new Ext.data.DirectStore(Ext.apply({
			autoLoad: false,
			remoteSort : true,
			hasMultiSort: true,
			fields: MShop.Schema.getRecord(this.recordName),
			api: {
				read    : MShop.API[this.recordName].searchItems,
				create  : MShop.API[this.recordName].insertItems,
				update  : MShop.API[this.recordName].saveItems,
				destroy : MShop.API[this.recordName].deleteItems
			},
			writer: new Ext.data.JsonWriter({
				writeAllFields: true,
				encode: false
			}),
			paramsAsHash: true,
			root: 'items',
			totalProperty: 'total',
			idProperty: this.idProperty,
			sortInfo: this.sortInfo
		}, this.storeConfig));

		// make sure site param gets set for read/write actions
		this.store.on('beforeload', this.onBeforeLoad, this);
		this.store.on('exception', this.onStoreException, this);
		this.store.on('beforewrite', this.onBeforeWrite, this);
	}
} );

Ext.reg('MShop.panel.locale.site.listui', MShop.panel.locale.site.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.locale.site.listui', MShop.panel.locale.site.ListUi, 80);
