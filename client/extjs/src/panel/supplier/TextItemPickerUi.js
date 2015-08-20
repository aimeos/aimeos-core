/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */


Ext.ns('MShop.panel.supplier');

// hook text picker into the supplier ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.supplier.ItemUi', 'MShop.panel.supplier.TextItemPickerUi', {

	xtype : 'MShop.panel.text.itempickerui',
    itemConfig : {
        recordName : 'Supplier_List',
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
                    'supplier.list.type.domain' : 'text'
                }
            }]
        },
        listTypeKey : 'supplier/list/type/text'
    },
    listConfig : {
        domain : 'supplier',
        prefix : 'text.'
    }
}, 40);
