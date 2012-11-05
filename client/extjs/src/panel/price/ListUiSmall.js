/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ListUiSmall.js 14341 2011-12-14 16:00:50Z nsendetzky $
 */


Ext.ns('MShop.panel.price');

MShop.panel.price.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Price',
	idProperty : 'price.id',
	siteidProperty : 'price.siteid',
	itemUiXType : 'MShop.panel.price.itemui',

	autoExpandColumn : 'price-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'price.currencyid',
			operator : 'startswith',
			value : ''
		} ]
	},

	getColumns : function() {
		// make sure type store gets loaded in same batch as this grid data
		this.typeStore = MShop.GlobalStoreMgr.get('Price_Type', this.domain);

		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: { '&&': [ { '==': { 'price.type.domain': this.domain } } ] }
			}
		};
		this.ItemTypeStore = MShop.GlobalStoreMgr.get('Price_Type', this.domain + '/price/type', storeConfig);

		return [ {
			xtype : 'gridcolumn',
			dataIndex : 'price.id',
			header : _('ID'),
			sortable : true,
			editable : false,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.label',
			header: _('Label'),
			sortable: true,
			align: 'left',
			id : 'price-label'
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.status',
			header : _('Status'),
			sortable : true,
			width : 50,
			align: 'center',
			renderer : this.statusColumnRenderer.createDelegate(this)
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.typeid',
			header : _('Type'),
			width : 70,
			renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "price.type.label" ], true)
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.currencyid',
			header : _('Currency'),
			sortable : true,
			width : 50
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.quantity',
			header : _('Quantity'),
			align : 'right',
			sortable : true,
			width : 70,
			align : 'right'
		}, {
			xtype : 'numbercolumn',
			dataIndex : 'price.value',
			header : _('Price'),
			sortable : true,
			width: 100,
			id : 'price-list-price',
			align : 'right'
		}, {
			xtype : 'numbercolumn',
			dataIndex : 'price.rebate',
			header : _('Rebate'),
			sortable : true,
			width : 70,
			hidden : true,
			align : 'right'
		}, {
			xtype : 'numbercolumn',
			dataIndex : 'price.shipping',
			header : _('Shipping'),
			sortable : true,
			width : 70,
			hidden : true,
			align : 'right'
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.taxrate',
			header : _('Tax rate'),
			sortable : true,
			width : 70,
			align : 'right',
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.ctime',
			header : _('Created'),
			sortable : true,
			width : 120,
			editable : false,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.mtime',
			header : _('Last modified'),
			sortable : true,
			width : 120,
			editable : false,
			hidden : true
		}, {
			xtype : 'gridcolumn',
			dataIndex : 'price.editor',
			header : _('Editor'),
			sortable : true,
			width : 120,
			editable : false,
			hidden : true
		} ];
	}
});

Ext.reg('MShop.panel.price.listuismall', MShop.panel.price.ListUiSmall);
