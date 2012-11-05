/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ListUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.attribute');

MShop.panel.attribute.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Attribute',
	idProperty : 'attribute.id',
	siteidProperty : 'attribute.siteid',
	itemUiXType : 'MShop.panel.attribute.itemui',
	exportMethod : 'Attribute_Export_Text.createJob',

	autoExpandColumn : 'attribute-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'attribute.label',
			operator : 'startswith',
			value : ''
		} ]
	},


	initComponent : function()
	{
		this.title = _('Attribute');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.attribute.ListUi.superclass.initComponent.call(this);
	},


	getColumns : function()
	{
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Attribute_Type');

		return [ {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.status',
				header : _('Status'),
				sortable : true,
				width : 70,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.typeid',
				header : _('Type'),
				width : 100,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "attribute.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.code',
				header : _('Code'),
				sortable : true,
				width : 100
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.label',
				header : _('Label'),
				sortable : true,
				editable : false,
				id : 'attribute-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.position',
				header : _('Position'),
				sortable : true,
				width : 50,
				editable : false
			}, {
				xtype : 'datecolumn',
				dataIndex : 'attribute.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'attribute.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			} ];
	}
} );

Ext.reg('MShop.panel.attribute.listui', MShop.panel.attribute.ListUi);
