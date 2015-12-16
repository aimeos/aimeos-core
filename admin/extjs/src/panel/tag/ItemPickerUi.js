/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

Ext.ns('MShop.panel.tag');

MShop.panel.tag.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

    title : MShop.I18n.dt('admin', 'Tags'),

    initComponent : function() {

        Ext.apply(this.itemConfig, {
            title : MShop.I18n.dt('admin', 'Associated tags'),
            xtype : 'MShop.panel.listitemlistui',
            domain : 'tag',
            getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
        });

        Ext.apply(this.listConfig, {
            title : MShop.I18n.dt('admin', 'Available tags'),
            xtype : 'MShop.panel.tag.listuismall'
        });

        MShop.panel.tag.ItemPickerUi.superclass.initComponent.call(this);
    },

    getAdditionalColumns : function() {

        var conf = this.itemConfig;
        this.typeStore = MShop.GlobalStoreMgr.get('Tag_Type', conf.domain);
        this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

        return [
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'typeid',
                header : MShop.I18n.dt('admin', 'List type'),
                id : 'listtype',
                width : 70,
                renderer : this.typeColumnRenderer.createDelegate(this,
                    [this.listTypeStore, conf.listTypeLabelProperty], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Type'),
                id : 'reftype',
                width : 70,
                renderer : this.refTypeColumnRenderer.createDelegate(this, [
                    this.typeStore,
                    'tag.typeid',
                    'tag.type.label'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Language'),
                id : 'reflang',
                width : 70,
                renderer : this.refLangColumnRenderer.createDelegate(this, ['tag.languageid'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Label'),
                id : 'refcontent',
                renderer : this.refColumnRenderer.createDelegate(this, ['tag.label'], true)
            }];
    }
});

Ext.reg('MShop.panel.tag.itempickerui', MShop.panel.tag.ItemPickerUi);
