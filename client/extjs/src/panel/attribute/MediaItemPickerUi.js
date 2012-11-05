/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: MediaItemPickerUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.attribute');

// hook media picker into the attribute ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.attribute.ItemUi', {
	xtype : 'MShop.panel.media.itempickerui',
	itemConfig : {
		recordName : 'Attribute_List',
		idProperty : 'attribute.list.id',
		siteidProperty : 'attribute.list.siteid',
		listNamePrefix : 'attribute.list.',
		listTypeIdProperty : 'attribute.list.type.id',
		listTypeLabelProperty : 'attribute.list.type.label',
		listTypeControllerName : 'Attribute_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'attribute.list.type.domain': 'media' } } ] },
		listTypeKey : 'attribute/list/type/media'
	},
	listConfig : {
		domain : 'attribute',
		prefix : 'media.'
	}
}, 20);
