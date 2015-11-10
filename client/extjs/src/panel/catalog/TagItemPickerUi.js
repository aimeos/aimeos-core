/*!
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

Ext.ns('MShop.panel.catalog');

// hook catalog tag picker into the catalog ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.catalog.ItemUi', 'MShop.panel.catalog.TagItemPickerUi', {
    xtype : 'MShop.panel.tag.itempickerui',
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
                    'catalog.lists.type.domain' : 'tag'
                }
            }]
        },
        listTypeKey : 'catalog/lists/type/tag'
    },
    listConfig : {
        prefix : 'tag.'
    }
}, 100);
