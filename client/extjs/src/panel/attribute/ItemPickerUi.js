/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemPickerUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.attribute');

MShop.panel.attribute.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

	title : _('Attribute'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Attributes'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'attribute',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Attributes'),
			xtype : 'MShop.panel.attribute.listuismall'
		});

		MShop.panel.attribute.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {

		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Attribute_Type', conf.domain);
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
				header : _('Status'),
				id : 'refstatus',
				width : 50,
				align: 'center',
				renderer : this.refStatusColumnRenderer.createDelegate(this, [ 'attribute.status' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'attribute.typeid', 'attribute.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Code'),
				id : 'refcode',
				width : 80,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'attribute.code' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'attribute.label' ], true)
			}
		];
	}
});

Ext.reg('MShop.panel.attribute.itempickerui', MShop.panel.attribute.ItemPickerUi);
