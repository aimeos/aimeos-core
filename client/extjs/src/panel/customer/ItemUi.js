/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */

Ext.ns('MShop.panel.customer');

MShop.panel.customer.ItemUi = Ext.extend(MShop.panel.AbstractListItemUi, {

    initComponent : function() {

        this.items = [{
            xtype : 'tabpanel',
            activeTab : 0,
            border : false,
            itemId : 'MShop.panel.customer.ItemUi',
            plugins : ['ux.itemregistry'],
            items : [{
                xtype : 'panel',
                title : MShop.I18n.dt('client/extjs', 'Basic'),
                border : false,
                layout : 'hbox',
                layoutConfig : {
                    align : 'stretch'
                },
                itemId : 'MShop.panel.customer.ItemUi.BasicPanel',
                plugins : ['ux.itemregistry'],
                defaults : {
                    bodyCssClass : this.readOnlyClass
                },
                items : [{
                    xtype : 'form',
                    title : 'Details',
                    flex : 1,
                    ref : '../../mainForm',
                    autoScroll : true,
                    items : [{
                        xtype : 'fieldset',
                        style : 'padding-right: 25px;',
                        border : false,
                        labelAlign : 'top',
                        defaults : {
                            readOnly : this.fieldsReadOnly,
                            anchor : '100%'
                        },
                        items : [{
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'ID'),
                            name : 'customer.id'
                        }, {
                            xtype : 'MShop.elements.status.combo',
                            name : 'customer.status'
                        }, {
                            xtype : 'textfield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Full name'),
                            name : 'customer.label',
                            allowBlank : false,
                            maxLength : 255,
                            emptyText : MShop.I18n.dt('client/extjs', 'Full name (required)')
                        }, {
                            xtype : 'textfield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Login name'),
                            name : 'customer.code',
                            allowBlank : false,
                            maxLength : 32,
                            emptyText : MShop.I18n.dt('client/extjs', 'Login name, e.g. e-mail address (required)')
                        }, {
                            xtype : 'textfield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Password'),
                            name : 'customer.password',
                            allowBlank : false,
                            maxLength : 255,
                            emptyText : MShop.I18n.dt('client/extjs', 'Password (required)')
                        }, {
                            xtype : 'datefield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Birthday'),
                            name : 'customer.birthday',
                            format : 'Y-m-d',
                            emptyText : MShop.I18n.dt('client/extjs', 'YYYY-MM-DD (optional)')
                        }, {
                            xtype : 'datefield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Verified date'),
                            name : 'customer.dateverified',
                            format : 'Y-m-d',
                            emptyText : MShop.I18n.dt('client/extjs', 'YYYY-MM-DD (optional)')
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
                }, {
                    xtype : 'MShop.panel.customer.AddressUi',
                    layout : 'fit',
                    flex : 1
                    data : (this.record ? this.record : {})
                }]
            }]
        }];

        MShop.panel.customer.ItemUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        var label = this.record ? this.record.data['customer.label'] : MShop.I18n.dt('client/extjs', 'new');
        //#: Customer item panel title with customer label ({0}) and site code ({1)}
        var string = MShop.I18n.dt('client/extjs', 'Customer: {0} ({1})');
        this.setTitle(String.format(string, label, MShop.config.site["locale.site.label"]));

        MShop.panel.product.ItemUi.superclass.afterRender.apply(this, arguments);
    }

});

Ext.reg('MShop.panel.customer.itemui', MShop.panel.customer.ItemUi);
