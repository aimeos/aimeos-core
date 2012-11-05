/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ProductItemPickerUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.catalog');

// hook product picker into the catalog ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.catalog.ItemUi', {
	xtype : 'MShop.panel.product.itempickerui',
	itemConfig : {
		recordName : 'Catalog_List',
		idProperty : 'catalog.list.id',
		siteidProperty : 'catalog.list.siteid',
		listNamePrefix : 'catalog.list.',
		listTypeIdProperty : 'catalog.list.type.id',
		listTypeLabelProperty : 'catalog.list.type.label',
		listTypeControllerName : 'Catalog_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'catalog.list.type.domain': 'product' } } ] },
		listTypeKey : 'catalog/list/type/product'
	},
	listConfig : {
		prefix : 'product.'
	}
}, 30);
