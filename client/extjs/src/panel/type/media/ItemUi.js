/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 * LGPLv3, http://www.arcavias.com/license
 */


Ext.ns('MShop.panel.media.type');

MShop.panel.media.type.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {
	
	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'media.type.siteid',

	initComponent : function() {
		this.title = _('Media type details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.media.type.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.media.type.ItemUi.BasicPanel',
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
							name : 'media.type.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'media.type.status',
							allowBlank : false
						}, {
							xtype : 'MShop.elements.domain.combo',
							name : 'media.type.domain',
							allowBlank : false
						}, {
							xtype : 'textfield',
							name : 'media.type.code',
							fieldLabel : _('Code'),
							emptyText : _('Code (required)'),
							allowBlank : false
						}, {
							xtype : 'textfield',
							name : 'media.type.label',
							fieldLabel : _('Label'),
							emptyText : _('Label (required)'),
							allowBlank : false
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'media.type.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'media.type.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'media.type.editor'
						}]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.media.type.ItemUi.superclass.initComponent.call(this);
	},

	afterRender : function()
	{
		var label = this.record ? this.record.data['media.type.label'] : 'new';

		this.setTitle( 'Media Type: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.media.type.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.media.type.itemui', MShop.panel.media.type.ItemUi);
