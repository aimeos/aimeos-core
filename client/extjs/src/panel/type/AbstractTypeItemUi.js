/*!
 * Copyright (c) Metaways Infosystems GmbH, 2014
 * LGPLv3, http://www.arcavias.com/license
 * @author Michael Spahn <m.spahn@metaways.de>
 */

Ext.ns('MShop.panel.type');

MShop.panel.type.AbstractTypeItemUi = Ext.extend(MShop.panel.AbstractItemUi, {
   /**
    * Domain to configure fields
    * 
    * E.g. attribute.type
    */
   typeDomain : null,

   initComponent : function() {
        //MShop.panel.AbstractItemUi.prototype.setSiteCheck(this);
        this.items = [{
            xtype : 'tabpanel',
            activeTab : 0,
            border : false,
            itemId : 'MShop.panel.' + typeDomain + '.ItemUi',
            plugins : ['ux.itemregistry'],
            items : [{
                xtype : 'panel',
                title : MShop.I18n.dt('client/extjs', 'Basic'),
                border : false,
                layout : 'hbox',
                layoutConfig : {
                    align : 'stretch'
                },
                itemId : 'MShop.panel.' + typeDomain + '.ItemUi.BasicPanel',
                plugins : ['ux.itemregistry'],
                defaults : {
                    bodyCssClass : this.readOnlyClass
                },
                items : [{
                    xtype : 'form',
                    flex : 1,
                    ref : '../../mainForm',
                    autoScroll : true,
                    items : [{
                        xtype : 'fieldset',
                        style : 'padding-right: 25px;',
                        border : false,
                        labelAlign : 'top',
                        defaults : {
                            anchor : '100%',
                            readOnly : this.fieldsReadOnly
                        },
                        items : [{
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'ID'),
                            name : typeDomain + '.id'
                        }, {
                            xtype : 'MShop.elements.status.combo',
                            name : typeDomain + '.status',
                            allowBlank : false
                        }, {
                            xtype : 'MShop.elements.domain.combo',
                            name : typeDomain + '.domain',
                            allowBlank : false
                        }, {
                            xtype : 'textfield',
                            name : typeDomain + '.code',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Code'),
                            allowBlank : false,
                            emptyText : MShop.I18n.dt('client/extjs', 'Unique code (required)')
                        }, {
                            xtype : 'textfield',
                            name : typeDomain + '.label',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Label'),
                            allowBlank : false,
                            maxLength : 255,
                            emptyText : MShop.I18n.dt('client/extjs', 'Internal name (required)')
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Created'),
                            name : typeDomain + '.ctime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Last modified'),
                            name : typeDomain + '.mtime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('client/extjs', 'Editor'),
                            name : typeDomain + '.editor'
                        }]
                    }]
                }]
            }]
        }];

        MShop.panel.AbstractItemUi.superclass.initComponent.call(this);
    }
});

Ext.reg('MShop.panel.type.abstracttypeitemui', MShop.panel.type.AbstractTypeItemUi);
