/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.text');

MShop.panel.text.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'text.siteid',

	initComponent : function() {
	
		this.title = _('Text item details');
		
		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.text.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.text.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					border : false,
					layout : 'fit',
					flex : 1,
					ref : '../../mainForm',
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						flex : 1,
						labelAlign : 'top',
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'text.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'text.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'text.typeid',
							mode : 'local',
							store : this.listUI.ItemTypeStore,
							displayField : 'text.type.label',
							valueField : 'text.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Name, description, etc. (required)')
						}, {
							xtype : 'MShop.elements.language.combo',
							name : 'text.languageid'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'text.label'
						}, {
							xtype : MShop.Config.get('client/extjs/common/editor', 'htmleditor'),
							fieldLabel : 'Content',
							name : 'text.content',
							enableFont : false
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'text.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'text.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'text.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.text.ItemUi.superclass.initComponent.call(this);
	},

	
	afterRender : function()
	{
		var label = this.record ? this.record.data['text.text'] : 'new';
		this.setTitle( 'Text: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.text.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.text.itemui', MShop.panel.text.ItemUi);
