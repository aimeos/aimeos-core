/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

Ext.ns('MShop.panel.product');

// hook product tag picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.TagItemPickerUi', {
    xtype : 'MShop.panel.tag.itempickerui',
    itemConfig : {
        recordName : 'Product_Lists',
        idProperty : 'product.lists.id',
        siteidProperty : 'product.lists.siteid',
        listNamePrefix : 'product.lists.',
        listTypeIdProperty : 'product.lists.type.id',
        listTypeLabelProperty : 'product.lists.type.label',
        listTypeControllerName : 'Product_Lists_Type',
        listTypeCondition : {
            '&&' : [{
                '==' : {
                    'product.lists.type.domain' : 'tag'
                }
            }]
        },
        listTypeKey : 'product/lists/type/tag'
    },
    listConfig : {
        prefix : 'tag.'
    }
}, 100);
