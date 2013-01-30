/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.media');

MShop.panel.media.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'media.siteid',

	initComponent : function() {
		
		this.title = _('Media item details');
		
		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		if(this.copyActive){
			this.record.data['media.id'] = null;
		}
		
		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.media.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.media.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					border : false,
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
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
							name : 'media.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'media.status'
						},
						{
							xtype : 'combo',
							fieldLabel : 'Type',
							name : 'media.typeid',
							mode : 'local',
							store : this.listUI.itemTypeStore,
							displayField : 'media.type.label',
							valueField : 'media.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('product picture, download, etc. (required)'),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'media.type.code', 'default' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'MShop.elements.language.combo',
							name : 'media.languageid'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Mimetype'),
							name : 'media.mimetype'
						}, {
							xtype : 'textfield',
							name : 'media.label',
							fieldLabel : 'Label',
							allowBlank : false,
							emptyText : _('Internal name (required)')
						}, {
							// NOTE: this is not used as a field, more like a
							// component which works on the whole record
							xtype : 'MShop.panel.media.mediafield',
							name : 'media.preview',
							width : 360,
							height : 280
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'media.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'media.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'media.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.media.ItemUi.superclass.initComponent.call(this);
	},

	
	afterRender : function()
	{
		var label = this.record ? this.record.data['media.label'] : 'new';
		this.setTitle( 'Media: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.media.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.media.itemui', MShop.panel.media.ItemUi);
