/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.product');

// hook price picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.PriceItemPickerUi', {
    xtype : 'MShop.panel.price.itempickerui',
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
                    'product.lists.type.domain' : 'price'
                }
            }]
        },
        listTypeKey : 'product/lists/type/price'
    },
    listConfig : {
        domain : 'product',
        prefix : 'price.'
    }
}, 30);
