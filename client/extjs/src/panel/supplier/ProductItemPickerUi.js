/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */


Ext.ns('MShop.panel.supplier');

// hook product picker into the supplier ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.supplier.ItemUi', 'MShop.panel.supplier.ProductItemPickerUi', {

	xtype : 'MShop.panel.product.itempickerui',
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
                    'supplier.list.type.domain' : 'product'
                }
            }]
        },
        listTypeKey : 'supplier/list/type/product'
    },
    listConfig : {
        prefix : 'product.'
    }
}, 30);
