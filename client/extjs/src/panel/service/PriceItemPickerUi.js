/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: PriceItemPickerUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.service');

// hook price picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.service.ItemUi', {
	xtype : 'MShop.panel.price.itempickerui',
	itemConfig : {
		recordName : 'Service_List',
		idProperty : 'service.list.id',
		siteidProperty : 'service.list.siteid',
		listNamePrefix : 'service.list.',
		listTypeIdProperty : 'service.list.type.id',
		listTypeLabelProperty : 'service.list.type.label',
		listTypeControllerName : 'Service_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'service.list.type.domain': 'price' } } ] },
		listTypeKey : 'service/list/type/price'
	},
	listConfig : {
		domain : 'service',
		prefix : 'price.'
	}
}, 30);
