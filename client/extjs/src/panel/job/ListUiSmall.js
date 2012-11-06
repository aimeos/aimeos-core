/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ListUiSmall.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.job');

MShop.panel.job.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

	recordName : 'Admin_Job',
	idProperty : 'job.id',
	siteidProperty : 'job.siteid',
	itemUiXType : 'MShop.panel.job.itemui',

	sortInfo : {
		field : 'job.ctime',
		direction : 'DESC'
	},

	autoExpandColumn : 'job-list-label',

	filterConfig : {
		filters : [ {
			dataIndex : 'job.ctime',
			operator : 'after',
			value : Ext.util.Format.date( new Date( new Date().valueOf() - 7 * 86400 * 1000 ), 'Y-m-d H:i:s' )
		} ]
	},

	initComponent : function()
	{
		this.title = _('Job');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		MShop.panel.job.ListUiSmall.superclass.initComponent.call(this);
	},

	getColumns : function()
	{
		return [
			{
				xtype : 'gridcolumn',
				dataIndex : 'job.id',
				header : _('Id'),
				sortable : true,
				width : 50,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.status',
				header : _('Status'),
				sortable : true,
				width : 50,
				align: 'center',
				renderer : this.statusColumnRenderer.createDelegate(this)
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.label',
				header : _('Label'),
				sortable : true,
				editable : false,
				id : 'job-list-label'
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.method',
				header : _('Method'),
				sortable : true,
				width : 200,
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.parameter',
				header : _('Parameter'),
				sortable : false,
				width : 100,
				editable : false,
				hidden : true,
				renderer : function( data ) {
					try {
						var result = '';
						var object = Ext.decode( data );

						for( var name in object ) {
							result += name + ': ' + object[name] + '<br/>';
						}
						return result;
					} catch( e ) {
						return data;
					}
				}
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.result',
				header : _('Result'),
				sortable : false,
				width : 200,
				editable : false,
				renderer : function( data ) {
					try {
						var result = '';
						var object = Ext.decode( data );

						if ( object instanceof Array ) {
							return '';
						}

						for( var name in object ) {
							result += name + ': ' + object[name] + '<br/>';
						}
						return result;
					} catch( e ) {
						return data;
					}
				}
			}, {
				xtype : 'datecolumn',
				dataIndex : 'job.ctime',
				header : _('Created'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'datecolumn',
				dataIndex : 'job.mtime',
				header : _('Last modified'),
				sortable : true,
				width : 130,
				format : 'Y-m-d H:i:s',
				editable : false,
				hidden : true
			}, {
				xtype : 'gridcolumn',
				dataIndex : 'job.editor',
				header : _('Editor'),
				sortable : true,
				width : 80,
				editable : false
			}
		];
	}
} );

Ext.reg('MShop.panel.job.listuismall', MShop.panel.job.ListUiSmall);

// hook this into the main tab panel
// Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', MShop.panel.job.ListUi, 20);
