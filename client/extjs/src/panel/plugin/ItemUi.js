/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.plugin');

MShop.panel.plugin.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,


	initComponent : function() {
		this.title = _('Plugin item details');
		
		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.plugin.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.plugin.ItemUi.BasicPanel',
				plugins : [ 'ux.itemregistry' ],
				defaults : {
					bodyCssClass : this.readOnlyClass
				},
				items : [ {
					xtype : 'form',
					title : 'Details',
					flex : 1,
					ref : '../../mainForm',
					autoScroll : true,
					items : [ {
						xtype : 'fieldset',
						style: 'padding-right: 25px;',
						border : false,
						labelAlign : 'top',
						defaults: {
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'plugin.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'plugin.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'plugin.typeid',
							mode : 'local',
							store : MShop.GlobalStoreMgr.get('Plugin_Type'),
							displayField : 'plugin.type.label',
							valueField : 'plugin.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Plugin type (required)'),
							listeners: {
								'render' : {
									fn: function() {
										var record, index = this.store.find( 'plugin.type.code', 'order' );
										if( ( record = this.store.getAt( index ) ) ) {
											this.setValue( record.id );
										}
									}
								}
							}
						}, {
							xtype : 'textfield',
							fieldLabel : _('Provider'),
							name : 'plugin.provider',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Name of the plugin provider class (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'plugin.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Internal plugin name (required)')
						}, {
							xtype : 'numberfield',
							fieldLabel : _('Position'),
							name : 'plugin.position',
							allowDecimals : false,
							allowBlank : false,
							value : 0
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'plugin.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'plugin.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'plugin.editor'
						} ]
					} ]
				}, {
					xtype: 'MShop.panel.plugin.configui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get('plugin.config') : {} )
				}]
			} ]
		} ];

		this.store.on('beforesave', this.onBeforeSave, this);

		MShop.panel.plugin.ItemUi.superclass.initComponent.call(this);
	},


	afterRender : function()
	{
		var label = this.record ? this.record.data['plugin.label'] : 'new';

		this.setTitle( 'Plugin: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.product.ItemUi.superclass.afterRender.apply( this, arguments );
	},


	onBeforeSave: function( store, data ) {

		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.plugin.configui' );
		var first = editorGrid.shift();

		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( key.trim() !== '' ) {
						config[key] = value;
					}
				}, this);
			});
		}

		if( data.create && data.create[0] ) {
			data.create[0].data['plugin.config'] = config;
		} else if( data.update && data.update[0] ) {
			data.update[0].data['plugin.config'] = config;
		}
	}
});

Ext.reg('MShop.panel.plugin.itemui', MShop.panel.plugin.ItemUi);