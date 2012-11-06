/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: TextItemPickerUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.media');

// hook text picker into the media ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.media.ItemUi', {
	xtype : 'MShop.panel.text.itempickerui',
	itemConfig : {
		recordName : 'Media_List',
		idProperty : 'media.list.id',
		siteidProperty : 'media.list.siteid',
		listDomain : 'media',
		listNamePrefix : 'media.list.',
		listTypeIdProperty : 'media.list.type.id',
		listTypeLabelProperty : 'media.list.type.label',
		listTypeControllerName : 'Media_List_Type',
		listTypeCondition : { '&&': [ { '==': { 'media.list.type.domain': 'text' } } ] },
		listTypeKey : 'media/list/type/text'
	},
	listConfig : {
		domain : 'media',
		prefix : 'text.'
	}
}, 10);
