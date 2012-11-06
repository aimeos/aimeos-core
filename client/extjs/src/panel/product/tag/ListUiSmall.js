/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ListUiSmall.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.product.tag');

MShop.panel.product.tag.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Product_Tag',
	idProperty : 'product.tag.id',
	siteidProperty : 'product.tag.siteid',
	itemUiXType : 'MShop.panel.product.tag.itemui',

	autoExpandColumn : 'product-tag-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'product.tag.label',
			operator : 'startswith',
			value : ''
		} ]
	},


	getColumns : function()
	{
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Product_Tag_Type');

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.typeid',
				header : _('Type'),
				sortable : true,
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "product.tag.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.languageid',
				header : _('Lang'),
				sortable : true,
				width : 70,
				renderer : MShop.elements.language.renderer
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.label',
				header : _('Label'),
				sortable : true,
				id : 'product-tag-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'product.tag.editor',
				header : _('Editor'),
				sortable : true,
				width : 130,
				editable : false,
				hidden : true
			}
		];
	}
} );

Ext.reg('MShop.panel.product.tag.listuismall', MShop.panel.product.tag.ListUiSmall);