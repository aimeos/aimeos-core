/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemPickerUi.js 14341 2011-12-14 16:00:50Z nsendetzky $
 */


Ext.ns('MShop.panel.product');

MShop.panel.product.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

	title : _('Product'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Products'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'product',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Products'),
			xtype : 'MShop.panel.product.listuismall'
		});

		MShop.panel.product.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {

		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Product_Type', conf.domain);
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
				renderer : this.refStatusColumnRenderer.createDelegate(this, [ 'product.status' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'product.typeid', 'product.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Code'),
				id : 'refcode',
				width : 100,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'product.code' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'product.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Supplier'),
				id : 'refsupplier',
				width : 120,
				hidden : true,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'product.suppliercode' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Product start'),
				id : 'refprodstart',
				width : 120,
				hidden : true,
				renderer : this.refDateColumnRenderer.createDelegate(this, [ 'product.datestart' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Product end'),
				id : 'refprodend',
				width : 120,
				hidden : true,
				renderer : this.refDateColumnRenderer.createDelegate(this, [ 'product.dateend' ], true)
			}
		];
	}
});

Ext.reg('MShop.panel.product.itempickerui', MShop.panel.product.ItemPickerUi);
