/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */


Ext.ns('MShop.panel.customer');

// hook media picker into the customer ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.customer.ItemUi', 'MShop.panel.customer.MediaItemPickerUi', {

	xtype : 'MShop.panel.media.itempickerui',
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
                    'customer.lists.type.domain' : 'media'
                }
            }]
        },
        listTypeKey : 'customer/lists/type/media'
    },
    listConfig : {
        domain : 'customer',
        prefix : 'media.'
    }
}, 50);
