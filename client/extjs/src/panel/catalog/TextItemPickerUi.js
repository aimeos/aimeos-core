/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.catalog');

// hook text picker into the catalog ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.catalog.ItemUi', 'MShop.panel.catalog.TextItemPickerUi', {
    xtype : 'MShop.panel.text.itempickerui',
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
                    'catalog.lists.type.domain' : 'text'
                }
            }]
        },
        listTypeKey : 'catalog/lists/type/text'
    },
    listConfig : {
        domain : 'catalog',
        prefix : 'text.'
    }
}, 10);
