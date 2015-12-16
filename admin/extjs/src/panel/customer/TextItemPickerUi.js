/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */


Ext.ns('MShop.panel.customer');

// hook text picker into the customer ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.customer.ItemUi', 'MShop.panel.customer.TextItemPickerUi', {

	xtype : 'MShop.panel.text.itempickerui',
    itemConfig : {
        recordName : 'Customer_Lists',
        idProperty : 'customer.lists.id',
        siteidProperty : 'customer.lists.siteid',
        listDomain : 'customer',
        listNamePrefix : 'customer.lists.',
        listTypeIdProperty : 'customer.lists.type.id',
        listTypeLabelProperty : 'customer.lists.type.label',
        listTypeControllerName : 'Customer_Lists_Type',
        listTypeCondition : {
            '&&' : [{
                '==' : {
                    'customer.lists.type.domain' : 'text'
                }
            }]
        },
        listTypeKey : 'customer/lists/type/text'
    },
    listConfig : {
        domain : 'customer',
        prefix : 'text.'
    }
}, 40);
