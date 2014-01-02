/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/license
 */


Ext.ns('MShop.panel.text.type');

MShop.panel.text.type.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	// Data Record (MShop_Text_Type like db. just without MShop)
	recordName : 'Text_Type',
	idProperty : 'text.type.id',
	siteidProperty : 'text.type.siteid',
	itemUiXType : 'MShop.panel.text.type.itemui',
	
	// Sort by id ASC
	sortInfo : {
		field : 'text.type.id',
		direction : 'ASC'
	},

	// Create filter
	filterConfig : {
		filters : [ {
			dataIndex : 'text.type.label',
			operator : 'startswith',
			value : ''
		} ]
	},
	
	// Override initComponent to set Label of tab.
	initComponent : function()
	{
		this.title = _('Text Type');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.text.type.ListUi.superclass.initComponent.call(this);
	},
	
	
	autoExpandColumn : 'text-type-label',

	getColumns : function() {
		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.id',
				header : _('ID'),
				sortable : true,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.domain',
				header : _('Domain'),
				sortable : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.code',
				header : _('Code'),
				sortable : true,
				width : 150,
				align: 'center',
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.label',
				id: 'text-type-label',
				header : _('Label'),
				sortable : true,
				editable : false
			}, {
				xtype : 'datecolumn',
				dataIndex : 'text.type.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'text.type.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.type.editor',
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
});

Ext.reg('MShop.panel.text.type.listui', MShop.panel.text.type.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.type.tabUi', 'MShop.panel.text.type.listui', MShop.panel.text.type.ListUi, 10);
