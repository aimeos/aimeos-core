/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */


Ext.ns('MShop.panel.customer');

MShop.panel.customer.AddressUi = Ext.extend(Ext.FormPanel, {

    siteidProperty : 'customer.siteid',

    title : MShop.I18n.dt('client/extjs', 'Billing address'),
    border : false,
    layout : 'hbox',
    layoutConfig : {
        align : 'stretch'
    },
    itemId : 'MShop.panel.customer.AddressUi',
    plugins : ['ux.itemregistry'],


    initComponent : function() {

        this.items = [{
            xtype : 'form',
            title : MShop.I18n.dt('client/extjs', 'Billing address'),
            flex : 1,
            autoScroll : true,
            items : [{
                xtype : 'fieldset',
                style : 'padding-right: 25px;',
                border : false,
                autoWidth : true,
                labelAlign : 'left',
                defaults : {
                    anchor : '100%'
                },
                items : [{
                    xtype : 'displayfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'ID'),
                    name : 'customer.id'
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Company'),
                    name : 'customer.company',
                    allowBlank : false,
                    maxLength : 100
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Vat ID'),
                    name : 'customer.vatid',
                    allowBlank : false,
                    maxLength : 32
                }, {
                    xtype : 'MShop.elements.salutation.combo',
                    name : 'customer.salutation'
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Title'),
                    name : 'customer.title',
                    allowBlank : false,
                    maxLength : 64
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Firstname'),
                    name : 'customer.firstname',
                    allowBlank : false,
                    maxLength : 64
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Lastname'),
                    name : 'customer.lastname',
                    allowBlank : false,
                    maxLength : 64
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Address 1'),
                    name : 'customer.address1',
                    allowBlank : false,
                    maxLength : 255
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Address 2'),
                    name : 'customer.address2',
                    allowBlank : false,
                    maxLength : 255
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Address 3'),
                    name : 'customer.address3',
                    allowBlank : false,
                    maxLength : 255
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Postal code'),
                    name : 'customer.postal',
                    allowBlank : false,
                    maxLength : 16
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'City'),
                    name : 'customer.city',
                    allowBlank : false,
                    maxLength : 255
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'State'),
                    name : 'customer.state',
                    allowBlank : false,
                    maxLength : 255
                }, {
                    xtype : 'displayfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Country'),
                    name : 'customer.countryid'
                }, {
                    xtype : 'displayfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Language'),
                    name : 'customer.languageid'
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Telephone'),
                    name : 'customer.telephone',
                    allowBlank : false,
                    maxLength : 32
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Telefax'),
                    name : 'customer.telefax',
                    allowBlank : false,
                    maxLength : 32
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'E-Mail'),
                    name : 'customer.email',
                    allowBlank : false,
                    maxLength : 255
                }, {
                    xtype : 'textfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Website'),
                    name : 'customer.website',
                    allowBlank : false,
                    maxLength : 255
                }, {
                    xtype : 'displayfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Created'),
                    name : 'customer.ctime'
                }, {
                    xtype : 'displayfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Last modified'),
                    name : 'customer.mtime'
                }, {
                    xtype : 'displayfield',
                    fieldLabel : MShop.I18n.dt('client/extjs', 'Editor'),
                    name : 'customer.editor'
                }]
            }]
        }];

        MShop.panel.customer.AddressUi.superclass.initComponent.call(this);
    }
});

Ext.reg('MShop.panel.customer.addressui', MShop.panel.customer.AddressUi);

//hook address into the customer ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.customer.ItemUi', 'MShop.panel.customer.AddressUi',
    MShop.panel.customer.AddressUi, 10);
