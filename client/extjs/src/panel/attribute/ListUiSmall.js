/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ListUiSmall.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.attribute');

MShop.panel.attribute.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Attribute',
	idProperty : 'attribute.id',
	siteidProperty : 'attribute.siteid',
	itemUiXType : 'MShop.panel.attribute.itemui',
	exportMethod : 'Attribute_Export_Text.createJob',
	importMethod: 'Attribute_Import_Text.uploadFile',

	autoExpandColumn : 'attribute-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'attribute.label',
			operator : 'startswith',
			value : ''
		} ]
	},


	getColumns : function()
	{
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Attribute_Type');

		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: { '&&': [ { '==': { 'attribute.type.domain': this.domain } } ] }
			}
		};
		this.itemTypeStore = MShop.GlobalStoreMgr.get('Attribute_Type', this.domain + '/attribute/type', storeConfig);

		return [
			{
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
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.typeid',
				header : _('Type'),
				width : 80,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "attribute.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.code',
				header : _('Code'),
				sortable : true,
				width : 80
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
				xtype : 'gridcolumn',
				dataIndex : 'attribute.ctime',
				header : _('Created'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'attribute.editor',
				header : _('Editor'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg('MShop.panel.attribute.listuismall', MShop.panel.attribute.ListUiSmall);
