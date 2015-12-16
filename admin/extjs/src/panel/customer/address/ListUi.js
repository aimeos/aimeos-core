/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */

Ext.ns('MShop.panel.customer.address');

MShop.panel.customer.address.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Customer_Address',
    idProperty : 'customer.address.id',
    siteidProperty : 'customer.address.siteid',
    itemUiXType : 'MShop.panel.customer.address.itemui',

    autoExpandColumn : 'customer-address-email',

    filterConfig : {
        filters : [{
            dataIndex : 'customer.address.lastname',
            operator : '=~',
            value : ''
        }]
    },

    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Address');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.customer.address.ListUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        this.itemUi = this.findParentBy(function(c) {
            return c.isXType(MShop.panel.AbstractItemUi, false);
        });

        MShop.panel.customer.address.ListUi.superclass.afterRender.apply(this, arguments);
    },

    onBeforeLoad : function(store, options) {
        this.setSiteParam(store);

        if(this.domain) {
            this.setDomainFilter(store, options);
        }

        options.params = options.params || {};
        options.params.condition = {
            '&&' : [{
                '==' : {
                    'customer.address.parentid' : this.itemUi.record ? this.itemUi.record.id : null
                }
            }]
        };

    },

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.company',
            header : MShop.I18n.dt('admin', 'Company'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.vatid',
            header : MShop.I18n.dt('admin', 'Vat ID'),
            width : 100,
            hidden : true
        }, {
            dataIndex : 'customer.address.salutation',
            width : 70,
            hidden : true,
            renderer : MShop.elements.salutation.renderer
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.title',
            header : MShop.I18n.dt('admin', 'Title'),
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.firstname',
            header : MShop.I18n.dt('admin', 'Firstname'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.lastname',
            header : MShop.I18n.dt('admin', 'Lastname'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.address1',
            header : MShop.I18n.dt('admin', 'Street'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.address2',
            header : MShop.I18n.dt('admin', 'House no'),
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.address3',
            header : MShop.I18n.dt('admin', 'Additional'),
            width : 100,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.postal',
            header : MShop.I18n.dt('admin', 'Zip code'),
            width : 70
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.city',
            header : MShop.I18n.dt('admin', 'City'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.state',
            header : MShop.I18n.dt('admin', 'State'),
            width : 100,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.countryid',
            header : MShop.I18n.dt('admin', 'Country'),
            align : 'center',
            width : 30
        }, {
            dataIndex : 'customer.address.languageid',
            width : 70,
            hidden : true,
            renderer : MShop.elements.language.renderer
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.telephone',
            header : MShop.I18n.dt('admin', 'Telephone'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.telefax',
            header : MShop.I18n.dt('admin', 'Telefax'),
            width : 100,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.email',
            header : MShop.I18n.dt('admin', 'E-Mail'),
            id : 'customer-address-email'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.website',
            width : 150,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'customer.address.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'customer.address.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'customer.address.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            width : 130,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.customer.address.listui', MShop.panel.customer.address.ListUi);

//hook this into the customer item tab panel
Ext.ux.ItemRegistry.registerItem('MShop.panel.customer.ItemUi', 'MShop.panel.customer.address.listui', MShop.panel.customer.address.ListUi, 10);
