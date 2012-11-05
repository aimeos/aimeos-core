/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemUi.js 14341 2011-12-14 16:00:50Z nsendetzky $
 */


Ext.ns('MShop.panel.product.tag');

MShop.panel.product.tag.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,
	siteidProperty : 'product.tag.siteid',

	initComponent : function() {

		this.title = _('Product tag details');

		MShop.panel.AbstractItemUi.prototype.setSiteCheck( this );

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.product.tag.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.product.tag.ItemUi.BasicPanel',
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
							name : 'product.tag.id'
						}, {
							xtype : 'combo',
							fieldLabel : _('Type'),
							name : 'product.tag.typeid',
							mode : 'local',
							store : this.listUI.typeStore,
							displayField : 'product.tag.type.label',
							valueField : 'product.tag.type.id',
							forceSelection : true,
							triggerAction : 'all',
							allowBlank : false,
							typeAhead : true,
							emptyText : _('Type of product tag (required)')
						}, {
							xtype : 'MShop.elements.language.combo',
							name : 'product.tag.languageid'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'product.tag.label',
							allowBlank : false,
							emptyText : _('Tag value (required)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'product.tag.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'product.tag.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'product.tag.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.product.tag.ItemUi.superclass.initComponent.call(this);
	},

	afterRender : function()
	{
		var label = this.record ? this.record.data['product.tag.label'] : 'new';

		this.setTitle( 'Product tag: ' + label + ' (' + MShop.config.site["locale.site.label"] + ')' );

		MShop.panel.product.tag.ItemUi.superclass.afterRender.apply( this, arguments );
	}
});

Ext.reg('MShop.panel.product.tag.itemui', MShop.panel.product.tag.ItemUi);
