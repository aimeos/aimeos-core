/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ListUi.js 14347 2011-12-15 08:47:00Z nsendetzky $
 */


Ext.ns('MShop.panel.service');

MShop.panel.service.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Service',
	idProperty : 'service.id',
	siteidProperty : 'service.siteid',
	itemUiXType : 'MShop.panel.service.itemui',

	autoExpandColumn : 'service-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'service.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	initComponent : function()
	{
		this.title = _('Service');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.service.ListUi.superclass.initComponent.call(this);
	},

	getColumns : function()
	{
		// make sure service type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Service_Type');

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'service.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				editable : false,
				hidden : true
			},
			{
				xtype : 'gridcolumn',
				dataIndex : 'service.status',
				header : _('Status'),
				sortable : true,
				width : 70,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : 'service.typeid',
				header : _('Type'),
				width : 100,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "service.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.code',
				header : _('Code'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.provider',
				header : _('Provider'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.label',
				header : _('Label'),
				sortable : true,
				width : 100,
				editable : false,
				id : 'service-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.position',
				header : _('Position'),
				sortable : true,
				width : 100,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.config',
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
				dataIndex : 'service.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'service.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'service.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				hidden : true
			}
		];
	}

} );

Ext.reg('MShop.panel.service.listui', MShop.panel.service.ListUi);

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.service.listui', MShop.panel.service.ListUi, 50);
