/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
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
							maxLength : 32,
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
						config[key] = value;
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
	
	onSaveItem: function() {
		if( !this.mainForm.getForm().isValid() && this.fireEvent( 'validate', this ) !== false )
		{
			Ext.Msg.alert( _( 'Invalid Data' ), _( 'Please recheck you data' ) );
			return;
		}

		this.saveMask.show();
		this.isSaveing = true;

		this.record.dirty = true;

		if( this.fireEvent( 'beforesave', this, this.record ) === false )
		{
			this.isSaveing = false;
			this.saveMask.hide();
		}

		this.record.beginEdit();
		this.record.set( 'locale.site.label', this.mainForm.getForm().findField( 'locale.site.label' ).getValue() );
		this.record.set( 'locale.site.status', this.mainForm.getForm().findField( 'locale.site.status' ).getValue() );
		this.record.set( 'locale.site.code', this.mainForm.getForm().findField( 'locale.site.code' ).getValue() );
		this.record.endEdit();

		if( this.isNewRecord ) {
			this.store.add( this.record );
		}

		if( !this.store.autoSave ) {
			this.onAfterSave();
		}
	}
} );




Ext.reg( 'MShop.panel.locale.site.itemui', MShop.panel.locale.site.ItemUi );