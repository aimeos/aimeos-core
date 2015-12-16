/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.service');

MShop.panel.service.ItemUi = Ext.extend(MShop.panel.AbstractListItemUi, {

    siteidProperty : 'service.siteid',

    initComponent : function() {

        MShop.panel.AbstractListItemUi.prototype.setSiteCheck(this);

        this.items = [{
            xtype : 'tabpanel',
            activeTab : 0,
            border : false,
            itemId : 'MShop.panel.service.ItemUi',
            plugins : ['ux.itemregistry'],
            items : [{
                xtype : 'panel',
                title : MShop.I18n.dt('admin', 'Basic'),
                border : false,
                layout : 'hbox',
                layoutConfig : {
                    align : 'stretch'
                },
                itemId : 'MShop.panel.service.ItemUi.BasicPanel',
                plugins : ['ux.itemregistry'],
                defaults : {
                    bodyCssClass : this.readOnlyClass
                },
                items : [{
                    xtype : 'form',
                    title : MShop.I18n.dt('admin', 'Details'),
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
                            fieldLabel : MShop.I18n.dt('admin', 'ID'),
                            name : 'service.id'
                        }, {
                            xtype : 'MShop.elements.status.combo',
                            name : 'service.status'
                        }, {
                            xtype : 'combo',
                            fieldLabel : MShop.I18n.dt('admin', 'Type'),
                            name : 'service.typeid',
                            mode : 'local',
                            store : MShop.GlobalStoreMgr.get('Service_Type'),
                            displayField : 'service.type.label',
                            valueField : 'service.type.id',
                            forceSelection : true,
                            triggerAction : 'all',
                            allowBlank : false,
                            typeAhead : true
                        }, {
                            xtype : 'textfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Code'),
                            name : 'service.code',
                            allowBlank : false,
                            maxLength : 32,
                            regex : /^[^ \v\t\r\n\f]+$/,
                            emptyText : MShop.I18n.dt('admin', 'Unique code (required)')
                        }, {
                            xtype : 'textfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Provider'),
                            name : 'service.provider',
                            allowBlank : false,
                            maxLength : 255,
                            emptyText : MShop.I18n.dt('admin', 'Name of the service provider class (required)')
                        }, {
                            xtype : 'textarea',
                            fieldLabel : MShop.I18n.dt('admin', 'Label'),
                            name : 'service.label',
                            allowBlank : false,
                            maxLength : 255,
                            emptyText : MShop.I18n.dt('admin', 'Internal name (required)')
                        }, {
                            xtype : 'numberfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Position'),
                            name : 'service.position',
                            allowDecimals : false,
                            allowBlank : false,
                            value : 0
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Created'),
                            name : 'service.ctime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Last modified'),
                            name : 'service.mtime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Editor'),
                            name : 'service.editor'
                        }]
                    }]
                }, {
                    xtype : 'MShop.panel.configui',
                    layout : 'fit',
                    flex : 1,
                    data : (this.record ? this.record.get('service.config') : {})
                }]
            }]
        }];

        this.store.on('beforesave', this.onBeforeSave, this);

        MShop.panel.service.ItemUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        var label = this.record ? this.record.data['service.label'] : MShop.I18n.dt('admin', 'new');
        //#: Service item panel title with service label ({0}) and site code ({1)}
        var string = MShop.I18n.dt('admin', 'Service: {0} ({1})');
        this.setTitle(String.format(string, label, MShop.config.site["locale.site.label"]));

        MShop.panel.product.ItemUi.superclass.afterRender.apply(this, arguments);
    },

    onBeforeSave : function(store, data) {
        MShop.panel.service.ItemUi.superclass.onBeforeSave.call(this, store, data, {
            configname : 'service.config'
        });
    }
});

Ext.reg('MShop.panel.service.itemui', MShop.panel.service.ItemUi);
