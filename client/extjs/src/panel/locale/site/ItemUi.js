/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemUi.js 14701 2012-01-05 08:52:24Z nsendetzky $
 */


Ext.ns( 'MShop.panel.locale.site' );

MShop.panel.locale.site.ItemUi = Ext.extend( MShop.panel.AbstractItemUi, {

	recordName : 'Locale_Site',
	idProperty : 'locale.site.id',
	siteidProperty : 'locale.site.id',

	initComponent : function()
	{
		this.title = _('Locale site item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.locale.site.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.locale.site.ItemUi.BasicPanel',
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
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'locale.site.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'locale.site.status'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'locale.site.code',
							allowBlank : false,
							emptyText : _('Unique site code (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'locale.site.label',
							allowBlank : false,
							emptyText : _('Internal site name (required)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'locale.site.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'locale.site.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'locale.site.editor'
						} ]
					} ]
				}, {
					xtype: 'MShop.panel.locale.site.configui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get('locale.site.config') : {} )
				} ]
			} ]
		} ];

		this.store.on('beforesave', this.onBeforeSave, this);
		
		MShop.panel.locale.site.ItemUi.superclass.initComponent.call( this );
	},

	afterRender : function()
	{
		this.setTitle( this.title + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.locale.site.ItemUi.superclass.afterRender.apply( this, arguments );
	},
	
	onBeforeSave: function( store, data ) {

		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.locale.site.configui' );
		var first = editorGrid.shift();
		
		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( key.trim() !== '' ) {
						config[key] = value.trim();
					}
				}, this);
			});
		}

		if( data.create && data.create[0] ) {
			data.create[0].data['locale.site.config'] = config;
		} else if( data.update && data.update[0] ) {
			data.update[0].data['locale.site.config'] = config;
		}
	},
} );

Ext.reg( 'MShop.panel.locale.site.itemui', MShop.panel.locale.site.ItemUi );