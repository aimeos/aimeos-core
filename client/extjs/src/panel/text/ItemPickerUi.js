/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemPickerUi.js 14341 2011-12-14 16:00:50Z nsendetzky $
 */


Ext.ns('MShop.panel.text');

MShop.panel.text.ItemPickerUi = Ext.extend( MShop.panel.AbstractListItemPickerUi, {

	title : _('Text'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Texts'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'text',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Texts'),
			xtype : 'MShop.panel.text.listuismall'
		});

		MShop.panel.text.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {
		
		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Text_Type', conf.domain);
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
				renderer : this.refStatusColumnRenderer.createDelegate(this, [ 'text.status' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'text.typeid', 'text.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Lang'),
				id : 'reflang',
				width : 50,
				renderer : this.refLangColumnRenderer.createDelegate(this, [ 'text.languageid' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'reflabel',
				hidden : true,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'text.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Content'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'text.content' ], true)
			}
		];
	}
});

Ext.reg('MShop.panel.text.itempickerui', MShop.panel.text.ItemPickerUi);
