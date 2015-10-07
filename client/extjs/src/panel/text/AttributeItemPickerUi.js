/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.text');

// hook media picker into the text ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.text.ItemUi', 'MShop.panel.text.AttributeItemPickerUi', {
    xtype : 'MShop.panel.attribute.itempickerui',
    itemConfig : {
        recordName : 'Text_Lists',
        idProperty : 'text.lists.id',
        siteidProperty : 'text.lists.siteid',
        listNamePrefix : 'text.lists.',
        listTypeIdProperty : 'text.lists.type.id',
        listTypeLabelProperty : 'text.lists.type.label',
        listTypeControllerName : 'Text_Lists_Type',
        listTypeCondition : {
            '&&' : [{
                '==' : {
                    'text.lists.type.domain' : 'attribute'
                }
            }]
        },
        listTypeKey : 'text/lists/type/attribute'
    },
    listConfig : {
        domain : ['text', 'product'],
        prefix : 'attribute.'
    }
}, 20);
