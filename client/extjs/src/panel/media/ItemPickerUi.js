/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.media');

MShop.panel.media.ItemPickerUi = Ext.extend( MShop.panel.AbstractListItemPickerUi, {

	title : _('Media'),

	initComponent : function() {

		Ext.apply(this.itemConfig, {
			title : _('Associated Media'),
			xtype : 'MShop.panel.listitemlistui',
			domain : 'media',
			getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
		});

		Ext.apply(this.listConfig, {
			title : _('Available Media'),
			xtype : 'MShop.panel.media.listuismall'
		});

		MShop.panel.media.ItemPickerUi.superclass.initComponent.call(this);
	},

	getAdditionalColumns : function() {

		var conf = this.itemConfig;
		this.typeStore = MShop.GlobalStoreMgr.get('Media_Type', conf.domain);
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
				renderer : this.refStatusColumnRenderer.createDelegate(this, [ 'media.status' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Type'),
				id : 'reftype',
				width : 70,
				renderer : this.refTypeColumnRenderer.createDelegate(this, [ this.typeStore, 'media.typeid', 'media.type.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Mimetype'),
				id : 'refmimetype',
				width : 80,
				hidden: true,
				sortable: true,
				renderer : this.refColumnRenderer.createDelegate(this, [ 'media.mimetype' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Lang'),
				id : 'reflang',
				width : 50,
				renderer : this.refLangColumnRenderer.createDelegate(this, [ 'media.languageid' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Label'),
				id : 'refcontent',
				renderer : this.refColumnRenderer.createDelegate(this, [ 'media.label' ], true)
			},
			{
				xtype : 'gridcolumn',
				dataIndex : conf.listNamePrefix + 'refid',
				header : _('Preview'),
				id : 'refpreview',
				width : 100,
				renderer : this.refPreviewRenderer.createDelegate(this)
			} ];
	},

	refPreviewRenderer : function(refId, metaData, record, rowIndex, colIndex, store) {
		var refItem = this.getRefStore().getById(refId);
		return (refItem ? "<img class='mshop-admin-media-list-preview' src=\"" + refItem.get('media.preview') + "\" />" : '');
	}
});

Ext.reg('MShop.panel.media.itempickerui', MShop.panel.media.ItemPickerUi);
