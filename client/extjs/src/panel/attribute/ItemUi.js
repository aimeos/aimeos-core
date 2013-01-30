/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemUi.js 14341 2011-12-14 16:00:50Z nsendetzky $
 */


Ext.ns('MShop.panel.attribute');

MShop.panel.attribute.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'attribute.siteid',

	initComponent : function() {

		this.title = _('Attribute item details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );
		
		if(this.copyActive){
			this.record.data['attribute.id'] = null;
		}
		
		var storeConfig = {
			baseParams: {
				site: MShop.config.site["locale.site.code"],
				condition: { '&&': [ { '==': { 'attribute.type.domain': this.domain } } ] }
			}
		};
		this.typeStore = MShop.GlobalStoreMgr.get('Attribute_Type', this.domain + '/attribute/type', storeConfig);

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.attribute.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.attribute.ItemUi.BasicPanel',
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
						defaults : {
							readOnly : this.fieldsReadOnly,
							anchor : '100%'
						},
						items : [ {
							xtype : 'displayfield',
							fieldLabel : _( 'ID' ),
							name : 'attribute.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'attribute.status'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'attribute.typeid',
							mode : 'local',
							store : this.typeStore,
							displayField : 'attribute.type.label',
							valueField : 'attribute.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Attribute type, e.g width, size, etc. (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Code'),
							name : 'attribute.code',
							allowBlank : false,
							emptyText : _('Attribute code (required)')
						}, {
							xtype : 'textarea',
							fieldLabel : _('Label'),
							name : 'attribute.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Internal attribute name (required)')
						}, {
							xtype : 'numberfield',
							fieldLabel : _('Item position sharing the same type'),
							name : 'attribute.position',
							allowDecimals : false,
							allowBlank : false,
							value : 0
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'attribute.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'attribute.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'attribute.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.attribute.ItemUi.superclass.initComponent.call(this);
	},

	
	afterRender : function()
	{
		var label = this.record ? this.record.data['attribute.label'] : 'new';
		this.setTitle( 'Attribute: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.attribute.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.attribute.itemui', MShop.panel.attribute.ItemUi);
