/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.text');

MShop.panel.text.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Text',
	idProperty : 'text.id',
	siteidProperty : 'text.siteid',
	itemUiXType : 'MShop.panel.text.itemui',

	autoExpandColumn : 'text-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'text.label',
			operator : 'startswith',
			value : ''
		} ]
	},

	getColumns : function() {
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Text_Type', this.domain);
		
		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: { '&&': [ { '==': { 'text.type.domain': this.domain } } ] }
			}
		};
		this.ItemTypeStore = MShop.GlobalStoreMgr.get('Text_Type', this.domain + '/text/type', storeConfig);

		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'text.id',
				header : _('ID'),
				sortable : true,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.typeid',
				header : _('Type'),
				width : 70,
				renderer : this.typeColumnRenderer.createDelegate(this, [ this.typeStore, "text.type.label" ], true)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.languageid',
				header : _('Lang'),
				sortable : true,
				width : 50,
				renderer : MShop.elements.language.renderer
			}, {
				xtype : 'gridcolumn',
				id : 'text-list-label',
				dataIndex : 'text.label',
				header : _('Label'),
				sortable : true,
				editable : false
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.content',
				header : _('Content'),
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.ctime',
				header : _('Created'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'text.editor',
				header : _('Editor'),
				sortable : true,
				width : 120,
				editable : false,
				hidden : true
			}
		];
	}
});

Ext.reg('MShop.panel.text.listuismall', MShop.panel.text.ListUiSmall);
