/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.product.tag');

MShop.panel.product.tag.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

	title : _('Tags'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Tags'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'product/tag',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Tags'),
			xtype : 'MShop.panel.product.tag.listuismall'
		});

		MShop.panel.product.tag.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {

		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Product_Tag_Type', conf.domain);
		this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'typeid',
				header : _('List type'),
				id : 'listtype',
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [ this.listTypeStore, conf.listTypeLabelProperty ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'product.tag.typeid', 'product.tag.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Lang'),
				id : 'reflang',
				width : 70,
				renderer : this.refLangColumnRenderer.createDelegate(this, [ 'product.tag.languageid' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'product.tag.label' ], true)
			}
		];
	}
});

Ext.reg('MShop.panel.product.tag.itempickerui', MShop.panel.product.tag.ItemPickerUi);
