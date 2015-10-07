/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */


Ext.ns('MShop.panel.supplier');

// hook attribute picker into the supplier ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.supplier.ItemUi', 'MShop.panel.supplier.AttributeItemPickerUi', {

	xtype : 'MShop.panel.attribute.itempickerui',
    itemConfig : {
        recordName : 'Supplier_Lists',
        idProperty : 'supplier.list.id',
        siteidProperty : 'supplier.list.siteid',
        listDomain : 'supplier',
        listNamePrefix : 'supplier.list.',
        listTypeIdProperty : 'supplier.list.type.id',
        listTypeLabelProperty : 'supplier.list.type.label',
        listTypeControllerName : 'Supplier_List_Type',
        listTypeCondition : {
            '&&' : [{
                '==' : {
                    'supplier.list.type.domain' : 'attribute'
                }
            }]
        },
        listTypeKey : 'supplier/list/type/attribute'
    },
    listConfig : {
        domain : 'supplier',
        prefix : 'attribute.'
    }
}, 60);
