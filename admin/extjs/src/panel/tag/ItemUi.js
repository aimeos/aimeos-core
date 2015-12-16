/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

Ext.ns('MShop.panel.tag');

MShop.panel.tag.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {
    siteidProperty : 'tag.siteid',

    initComponent : function() {

        MShop.panel.AbstractItemUi.prototype.setSiteCheck(this);

        this.items = [{
            xtype : 'tabpanel',
            activeTab : 0,
            border : false,
            itemId : 'MShop.panel.tag.ItemUi',
            plugins : ['ux.itemregistry'],
            items : [{
                xtype : 'panel',
                title : MShop.I18n.dt('admin', 'Basic'),
                border : false,
                layout : 'hbox',
                layoutConfig : {
                    align : 'stretch'
                },
                itemId : 'MShop.panel.tag.ItemUi.BasicPanel',
                plugins : ['ux.itemregistry'],
                defaults : {
                    bodyCssClass : this.readOnlyClass
                },
                items : [{
                    title : MShop.I18n.dt('admin', 'Details'),
                    xtype : 'form',
                    border : false,
                    flex : 1,
                    ref : '../../mainForm',
                    autoScroll : true,
                    items : [{
                        xtype : 'fieldset',
                        style : 'padding-right: 25px;',
                        border : false,
                        flex : 1,
                        labelAlign : 'top',
                        defaults : {
                            readOnly : this.fieldsReadOnly,
                            anchor : '100%'
                        },
                        items : [{
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'ID'),
                            name : 'tag.id'
                        }, {
                            xtype : 'combo',
                            fieldLabel : MShop.I18n.dt('admin', 'Type'),
                            name : 'tag.typeid',
                            mode : 'local',
                            store : this.listUI.typeStore,
                            displayField : 'tag.type.label',
                            valueField : 'tag.type.id',
                            forceSelection : true,
                            triggerAction : 'all',
                            allowBlank : false,
                            typeAhead : true
                        }, {
                            xtype : 'MShop.elements.language.combo',
                            name : 'tag.languageid'
                        }, {
                            xtype : 'textfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Label'),
                            name : 'tag.label',
                            allowBlank : false,
                            emptyText : MShop.I18n.dt('admin', 'Tag value (required)')
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Created'),
                            name : 'tag.ctime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Last modified'),
                            name : 'tag.mtime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Editor'),
                            name : 'tag.editor'
                        }]
                    }]
                }]
            }]
        }];

        MShop.panel.tag.ItemUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        var label = this.record ? this.record.data['tag.label'] : MShop.I18n.dt('admin', 'new');
        //#: Tag item panel title with tag label ({0}) and site code ({1)}
        var string = MShop.I18n.dt('admin', 'Tag: {0} ({1})');
        this.setTitle(String.format(string, label, MShop.config.site["locale.site.label"]));

        MShop.panel.tag.ItemUi.superclass.afterRender.apply(this, arguments);
    }
});

Ext.reg('MShop.panel.tag.itemui', MShop.panel.tag.ItemUi);
