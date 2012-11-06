/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ItemUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.job');

MShop.panel.job.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

	maximized : true,
	layout : 'fit',
	modal : true,

	initComponent : function() {

		this.title = _('Job item details');

		this.items = [ {
			xtype : 'tabpanel',
			activeTab : 0,
			border : false,
			itemId : 'MShop.panel.job.ItemUi',
			plugins : [ 'ux.itemregistry' ],
			items : [ {
				xtype : 'panel',
				title : _('Basic'),
				border : false,
				layout : 'hbox',
				layoutConfig : {
					align : 'stretch'
				},
				itemId : 'MShop.panel.job.ItemUi.BasicPanel',
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
							name : 'job.id'
						}, {
							xtype : 'MShop.elements.status.combo',
							name : 'job.status'
						}, {
							xtype : 'textfield',
							fieldLabel : _('Label'),
							name : 'job.label',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Job label (required)')
						}, {
							xtype : 'textfield',
							fieldLabel : _('Method'),
							name : 'job.method',
							allowBlank : false,
							maxLength : 255,
							emptyText : _('Called controller/method (required)')
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Created'),
							name : 'job.ctime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Last modified'),
							name : 'job.mtime'
						}, {
							xtype : 'displayfield',
							fieldLabel : _('Editor'),
							name : 'job.editor'
						} ]
					} ]
				} ]
			} ]
		} ];

		MShop.panel.job.ItemUi.superclass.initComponent.call(this);
	}
});

Ext.reg('MShop.panel.job.itemui', MShop.panel.job.ItemUi);
