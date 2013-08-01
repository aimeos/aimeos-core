/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/license
 */


Ext.ns('MShop.panel.text.type');

MShop.panel.text.type.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'text.type.siteid',

	initComponent : function() {
		this.title = _('Text type details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.text.type.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.text.type.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							anchor : '100%',
							readOnly : this.fieldsReadOnly
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'text.type.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'text.type.status',
							allowBlank : false
						}, {
							xtype : 'MShop.elements.domain.combo',
							name : 'text.type.domain',
							allowBlank : false
						}, {
							xtype : 'textfield',
							name : 'text.type.code',
							fieldLabel : _('Code'),
							emptyText : _('Code (required)'),
							allowBlank : false
						}, {
							xtype : 'textfield',
							name : 'text.type.label',
							fieldLabel : _('Label'),
							emptyText : _('Label (required)'),
							allowBlank : false
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'text.type.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'text.type.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'text.type.editor'
						}]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.text.type.ItemUi.superclass.initComponent.call(this);
	},

	afterRender : function()
	{
		var label = this.record ? this.record.data['text.type.label'] : 'new';

		this.setTitle( 'Text Type: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.text.type.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.text.type.itemui', MShop.panel.text.type.ItemUi);
