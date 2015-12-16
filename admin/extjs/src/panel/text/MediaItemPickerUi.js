/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.text');

// hook media picker into the text ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.text.ItemUi', 'MShop.panel.text.MediaItemPickerUi', {
    xtype : 'MShop.panel.media.itempickerui',
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
                    'text.lists.type.domain' : 'media'
                }
            }]
        },
        listTypeKey : 'text/lists/type/media'
    },
    listConfig : {
        domain : 'text',
        prefix : 'media.'
    }
}, 20);
