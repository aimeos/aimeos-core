/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015
 */

Ext.ns('MShop.panel.supplier.address');

MShop.panel.supplier.address.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Supplier_Address',
    idProperty : 'supplier.address.id',
    siteidProperty : 'supplier.address.siteid',
    itemUiXType : 'MShop.panel.supplier.address.itemui',

    autoExpandColumn : 'supplier-address-email',

    filterConfig : {
        filters : [{
            dataIndex : 'supplier.address.lastname',
            operator : '=~',
            value : ''
        }]
    },

    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Address');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.supplier.address.ListUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        this.itemUi = this.findParentBy(function(c) {
            return c.isXType(MShop.panel.AbstractItemUi, false);
        });

        MShop.panel.supplier.address.ListUi.superclass.afterRender.apply(this, arguments);
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
                    'supplier.address.parentid' : this.itemUi.record ? this.itemUi.record.id : null
                }
            }]
        };

    },

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.company',
            header : MShop.I18n.dt('admin', 'Company'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.vatid',
            header : MShop.I18n.dt('admin', 'Vat ID'),
            width : 100,
            hidden : true
        }, {
            dataIndex : 'supplier.address.salutation',
            width : 70,
            hidden : true,
            renderer : MShop.elements.salutation.renderer
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.title',
            header : MShop.I18n.dt('admin', 'Title'),
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.firstname',
            header : MShop.I18n.dt('admin', 'Firstname'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.lastname',
            header : MShop.I18n.dt('admin', 'Lastname'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.address1',
            header : MShop.I18n.dt('admin', 'Street'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.address2',
            header : MShop.I18n.dt('admin', 'House no'),
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.address3',
            header : MShop.I18n.dt('admin', 'Additional'),
            width : 100,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.postal',
            header : MShop.I18n.dt('admin', 'Zip code'),
            width : 70
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.city',
            header : MShop.I18n.dt('admin', 'City'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.state',
            header : MShop.I18n.dt('admin', 'State'),
            width : 100,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.countryid',
            header : MShop.I18n.dt('admin', 'Country'),
            width : 30
        }, {
            dataIndex : 'supplier.address.languageid',
            width : 70,
            hidden : true,
            renderer : MShop.elements.language.renderer
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.telephone',
            header : MShop.I18n.dt('admin', 'Telephone'),
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.telefax',
            header : MShop.I18n.dt('admin', 'Telefax'),
            width : 100,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.email',
            header : MShop.I18n.dt('admin', 'E-Mail'),
            id : 'supplier-address-email'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.website',
            width : 150,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'supplier.address.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'supplier.address.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            width : 130,
            format : 'Y-m-d H:i:s',
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'supplier.address.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            width : 130,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.supplier.address.listui', MShop.panel.supplier.address.ListUi);

//hook this into the supplier item tab panel
Ext.ux.ItemRegistry.registerItem('MShop.panel.supplier.ItemUi', 'MShop.panel.supplier.address.listui', MShop.panel.supplier.address.ListUi, 10);
