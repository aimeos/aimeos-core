/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */

Ext.ns('MShop.panel.customer');

MShop.panel.customer.ItemUi = Ext.extend(MShop.panel.AbstractListItemUi, {

    siteidProperty : 'customer.siteid',

    initComponent : function() {

        MShop.panel.AbstractItemUi.prototype.setSiteCheck(this);

        this.items = [{
            xtype : 'tabpanel',
            activeTab : 0,
            border : false,
            itemId : 'MShop.panel.customer.ItemUi',
            plugins : ['ux.itemregistry'],
            items : [{
                title : MShop.I18n.dt('admin', 'Basic'),
                itemId : 'MShop.panel.customer.ItemUi.BasicPanel',
                xtype : 'panel',
                layout : 'fit',
                plugins : ['ux.itemregistry'],
                defaults : {
                    bodyCssClass : this.readOnlyClass
                },
                items : [{
                    xtype : 'form',
                    layout : 'fit',
                    ref : '../../mainForm',
                    border : false,
                    items : [{
                        xtype : 'panel',
                        layout : {
                            type : 'hbox',
                            align : 'stretch'
                        },
                        border : false,
                        items : [{
                            title : MShop.I18n.dt('admin', 'Details'),
                            xtype : 'panel',
                            layout : 'fit',
                            flex : 1,
                            autoScroll : true,
                            items : [{
                                xtype : 'fieldset',
                                border : false,
                                labelAlign : 'top',
                                defaults : {
                                    readOnly : this.fieldsReadOnly,
                                    anchor : '-25'
                                },
                                autoHeight : true,
                                autoWidth : true,
                                items : [{
                                        xtype : 'displayfield',
                                        fieldLabel : MShop.I18n.dt('admin', 'ID'),
                                        name : 'customer.id'
                                    }, {
                                        xtype : 'MShop.elements.status.combo',
                                        name : 'customer.status'
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.code',
                                        fieldLabel : MShop.I18n.dt('admin', 'User name'),
                                        emptyText : MShop.I18n.dt('admin', 'User name, e.g. e-mail address (required)'),
                                        allowBlank : false,
                                        maxLength : 32
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.password',
                                        fieldLabel : MShop.I18n.dt('admin', 'Password'),
                                        emptyText : MShop.I18n.dt('admin', 'Password (required)'),
                                        allowBlank : false,
                                        maxLength : 255
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.label',
                                        fieldLabel : MShop.I18n.dt('admin', 'Full name'),
                                        emptyText : MShop.I18n.dt('admin', 'Full name (required)'),
                                        allowBlank : false,
                                        maxLength : 255
                                    }, {
                                        xtype : 'datefield',
                                        name : 'customer.birthday',
                                        fieldLabel : MShop.I18n.dt('admin', 'Birthday'),
                                        emptyText : MShop.I18n.dt('admin', 'YYYY-MM-DD (optional)'),
                                        format : 'Y-m-d'
                                    }, {
                                        xtype : 'datefield',
                                        name : 'customer.dateverified',
                                        fieldLabel : MShop.I18n.dt('admin', 'Verification date'),
                                        emptyText : MShop.I18n.dt('admin', 'YYYY-MM-DD (optional)'),
                                        format : 'Y-m-d'
                                    }, {
                                        xtype : 'displayfield',
                                        name : 'customer.ctime',
                                        fieldLabel : MShop.I18n.dt('admin', 'Created')
                                    }, {
                                        xtype : 'displayfield',
                                        name : 'customer.mtime',
                                        fieldLabel : MShop.I18n.dt('admin', 'Last modified')
                                    }, {
                                        xtype : 'displayfield',
                                        name : 'customer.editor',
                                        fieldLabel : MShop.I18n.dt('admin', 'Editor')
                                }]
                            }]
                        }, {
                            title : MShop.I18n.dt('admin', 'Billing address'),
                            xtype : 'panel',
                            layout : 'fit',
                            flex : 1,
                            autoScroll : true,
                            items : [{
                                xtype : 'fieldset',
                                border : false,
                                labelAlign : 'top',
                                defaults : {
                                    readOnly : this.fieldsReadOnly,
                                    anchor : '-25'
                                },
                                autoHeight : true,
                                autoWidth : true,
                                items : [{
                                        xtype : 'textfield',
                                        name : 'customer.company',
                                        fieldLabel : MShop.I18n.dt('admin', 'Company'),
                                        emptyText : MShop.I18n.dt('admin', 'Company name'),
                                        maxLength : 100
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.vatid',
                                        fieldLabel : MShop.I18n.dt('admin', 'Vat ID'),
                                        emptyText : MShop.I18n.dt('admin', 'Vat ID, e.g. "GB999999999"'),
                                        maxLength : 32
                                    }, {
                                        xtype : 'MShop.elements.salutation.combo',
                                        name : 'customer.salutation'
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.title',
                                        fieldLabel : MShop.I18n.dt('admin', 'Title'),
                                        emptyText : MShop.I18n.dt('admin', 'Honorary title'),
                                        maxLength : 64
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.firstname',
                                        fieldLabel : MShop.I18n.dt('admin', 'First name'),
                                        emptyText : MShop.I18n.dt('admin', 'First name'),
                                        maxLength : 64
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.lastname',
                                        fieldLabel : MShop.I18n.dt('admin', 'Last name'),
                                        emptyText : MShop.I18n.dt('admin', 'Last name (required)'),
                                        allowBlank : false,
                                        maxLength : 64
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.address1',
                                        fieldLabel : MShop.I18n.dt('admin', 'Address 1'),
                                        emptyText : MShop.I18n.dt('admin', 'Street (required)'),
                                        allowBlank : false,
                                        maxLength : 255
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.address2',
                                        fieldLabel : MShop.I18n.dt('admin', 'Address 2'),
                                        emptyText : MShop.I18n.dt('admin', 'House number'),
                                        maxLength : 255
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.address3',
                                        fieldLabel : MShop.I18n.dt('admin', 'Address 3'),
                                        emptyText : MShop.I18n.dt('admin', 'Additional information, e.g. flat number'),
                                        maxLength : 255
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.postal',
                                        fieldLabel : MShop.I18n.dt('admin', 'Postal code'),
                                        emptyText : MShop.I18n.dt('admin', 'Postal code'),
                                        maxLength : 16
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.city',
                                        fieldLabel : MShop.I18n.dt('admin', 'City'),
                                        emptyText : MShop.I18n.dt('admin', 'City name (required)'),
                                        allowBlank : false,
                                        maxLength : 255
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.state',
                                        fieldLabel : MShop.I18n.dt('admin', 'State'),
                                        emptyText : MShop.I18n.dt('admin', 'Country state, e.g. "NY"'),
                                        maxLength : 255
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.countryid',
                                        fieldLabel : MShop.I18n.dt('admin', 'Country code'),
                                        emptyText : MShop.I18n.dt('admin', 'Two letter country code, e.g. "US" (required)'),
                                        regex : /[A-Za-z]{2}/,
                                        allowBlank : false,
                                        maxLength : 2
                                    }, {
                                        xtype : 'MShop.elements.language.combo',
                                        name : 'customer.languageid'
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.telephone',
                                        fieldLabel : MShop.I18n.dt('admin', 'Telephone'),
                                        emptyText : MShop.I18n.dt('admin', 'Telephone number, e.g. +155512345'),
                                        maxLength : 32
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.telefax',
                                        fieldLabel : MShop.I18n.dt('admin', 'Telefax'),
                                        emptyText : MShop.I18n.dt('admin', 'Facsimile number, e.g. +155512345'),
                                        maxLength : 32
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.email',
                                        fieldLabel : MShop.I18n.dt('admin', 'E-Mail'),
                                        emptyText : MShop.I18n.dt('admin', 'E-Mail, e.g. me@example.com (required)'),
                                        allowBlank : false,
                                        maxLength : 255
                                    }, {
                                        xtype : 'textfield',
                                        name : 'customer.website',
                                        fieldLabel : MShop.I18n.dt('admin', 'Website'),
                                        emptyText : MShop.I18n.dt('admin', 'Web site, e.g. www.example.com'),
                                        maxLength : 255
                                }]
                            }]
                        }]
                    }]
                }]
            }]
        }];

        MShop.panel.customer.ItemUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        var label = this.record ? this.record.data['customer.label'] : MShop.I18n.dt('admin', 'new');
        //#: Customer item panel title with customer label ({0}) and site code ({1)}
        var string = MShop.I18n.dt('admin', 'Customer: {0} ({1})');
        this.setTitle(String.format(string, label, MShop.config.site["locale.site.label"]));

        MShop.panel.customer.ItemUi.superclass.afterRender.apply(this, arguments);
    }

});

Ext.reg('MShop.panel.customer.itemui', MShop.panel.customer.ItemUi);
