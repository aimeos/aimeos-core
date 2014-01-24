/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.service');

MShop.panel.service.ItemUi = Ext.extend(MShop.panel.AbstractListItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,


	initComponent : function() {
		this.title = _('Service item details');
		
		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.service.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.service.ItemUi.BasicPanel',
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
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'service.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'service.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'service.typeid',
							mode : 'local',
							store : MShop.GlobalStoreMgr.get('Service_Type'),
							displayField : 'service.type.label',
							valueField : 'service.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Payment or delivery (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'service.code',
							allowBlank : false,
							maxLength : 32,
							emptyText : _('Unique service code (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Provider'),
							name : 'service.provider',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Name of the service provider class (required)')
						}, {
							xtype : 'textarea',
							fieldLabel : _('Label'),
							name : 'service.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Internal service name (required)')
						}, {
							xtype : 'numberfield',
							fieldLabel : _('Position'),
							name : 'service.position',
							allowDecimals : false,
							allowBlank : false,
							value : 0
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'service.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'service.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'service.editor'
						} ]
					} ]
				}, {
					xtype: 'MShop.panel.service.configui',
					layout: 'fit',
					flex: 1,
					data: ( this.record ? this.record.get('service.config') : {} )
				} ]
			} ]
		} ];

		this.store.on('beforesave', this.onBeforeSave, this);

		MShop.panel.service.ItemUi.superclass.initComponent.call(this);
	},
	

	afterRender : function()
	{
		var label = this.record ? this.record.data['service.label'] : 'new';

		this.setTitle( 'Service: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.product.ItemUi.superclass.afterRender.apply( this, arguments );
	},


	onBeforeSave: function( store, data ) {

		var config = {};
		var editorGrid = this.findByType( 'MShop.panel.service.configui' );
		var first = editorGrid.shift();
		
		if( first ) {
			Ext.each( first.data, function( item, index ) {
				Ext.iterate( item, function( key, value, object ) {
					if( ( key = key.trim() ) !== '' ) {
						config[key] = value.trim();
					}
				}, this);
			});
		}

		if( data.create && data.create[0] ) {
			data.create[0].data['service.config'] = config;
		} else if( data.update && data.update[0] ) {
			data.update[0].data['service.config'] = config;
		}
	}
});

Ext.reg('MShop.panel.service.itemui', MShop.panel.service.ItemUi);
