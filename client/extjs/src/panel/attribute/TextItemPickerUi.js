/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.attribute');

// hook text picker into the attribute ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.attribute.ItemUi', 'MShop.panel.attribute.TextItemPickerUi', {
    xtype : 'MShop.panel.text.itempickerui',
    itemConfig : {
        recordName : 'Attribute_Lists',
        idProperty : 'attribute.lists.id',
        siteidProperty : 'attribute.lists.siteid',
        listNamePrefix : 'attribute.lists.',
        listTypeIdProperty : 'attribute.lists.type.id',
        listTypeLabelProperty : 'attribute.lists.type.label',
        listTypeControllerName : 'Attribute_Lists_Type',
        listTypeCondition : {
            '&&' : [{
                '==' : {
                    'attribute.lists.type.domain' : 'text'
                }
            }]
        },
        listTypeKey : 'attribute/lists/type/text'
    },
    listConfig : {
        domain : 'attribute',
        prefix : 'text.'
    }
}, 10);
