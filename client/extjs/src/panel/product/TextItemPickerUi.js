/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: TextItemPickerUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.product');

// hook text picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', {
	xtype : 'MShop.panel.text.itempickerui',
	itemConfig : {
		recordName : 'Product_List',
		idProperty : 'product.list.id',
		siteidProperty : 'product.list.siteid',
		listNamePrefix : 'product.list.',
		listTypeIdProperty : 'product.list.type.id',
		listTypeLabelProperty : 'product.list.type.label',
		listTypeControllerName : 'Product_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'product.list.type.domain': 'text' } } ] },
		listTypeKey : 'product/list/type/text'
	},
	listConfig : {
		domain : 'product',
		prefix : 'text.'
	}
}, 10);
