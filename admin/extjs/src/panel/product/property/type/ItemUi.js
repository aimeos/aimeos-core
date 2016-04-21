/*!
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * Copyright Aimeos (aimeos.org), 2016
 */

Ext.ns('MShop.panel.product.property.type');

MShop.panel.product.property.type.ItemUi = Ext.extend(MShop.panel.AbstractTypeItemUi, {
    siteidProperty : 'product.property.type.siteid',
    typeDomain : 'product.property.type',

    initComponent : function() {
        MShop.panel.AbstractTypeItemUi.prototype.setSiteCheck(this);
        MShop.panel.product.type.ItemUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        var label = this.record ? this.record.data['product.property.type.label'] : MShop.I18n.dt('admin', 'new');
        //#: Product property type item panel title with type label ({0}) and site code ({1)}
        var string = MShop.I18n.dt('admin', 'Product property type: {0} ({1})');
        this.setTitle(String.format(string, label, MShop.config.site["locale.site.label"]));

        MShop.panel.product.property.type.ItemUi.superclass.afterRender.apply(this, arguments);
    }
});

Ext.reg('MShop.panel.product.property.type.itemui', MShop.panel.product.property.type.ItemUi);
