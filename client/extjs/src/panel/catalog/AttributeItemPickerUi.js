/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.catalog');

// hook attribute picker into the catalog ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.catalog.ItemUi', 'MShop.panel.catalog.AttributeItemPickerUi', {
    xtype : 'MShop.panel.attribute.itempickerui',
    itemConfig : {
        recordName : 'Catalog_Lists',
        idProperty : 'catalog.lists.id',
        siteidProperty : 'catalog.lists.siteid',
        listNamePrefix : 'catalog.lists.',
        listTypeIdProperty : 'catalog.lists.type.id',
        listTypeLabelProperty : 'catalog.lists.type.label',
        listTypeControllerName : 'Catalog_Lists_Type',
        listTypeCondition : {
            '&&' : [{
                '==' : {
                    'catalog.lists.type.domain' : 'attribute'
                }
            }]
        },
        listTypeKey : 'catalog/lists/type/attribute'
    },
    listConfig : {
        domain : 'catalog',
        prefix : 'attribute.'
    }
}, 50);
