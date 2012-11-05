/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ConfigUi.js 14347 2011-12-15 08:47:00Z nsendetzky $
 */


Ext.ns('MShop.panel.service');

MShop.panel.service.ConfigUi = Ext.extend(Ext.grid.EditorGridPanel, {

	stripeRows: true,

	initComponent: function() {
		this.title = _('Configuration');		
		this.colModel = this.getColumnModel();
		this.tbar = this.getToolBar();
		this.store = this.getStore();
		this.sm = new Ext.grid.RowSelectionModel();
		this.autoExpandColumn = 'service-config-value';
		this.record = Ext.data.Record.create([
			{name: 'name', type: 'string'},
			{name: 'value', type: 'string'}
		]);

		if (!Ext.isObject(this.data)) {
			this.data = {};
		}

		MShop.panel.service.ConfigUi.superclass.initComponent.call(this);
	},

	getToolBar: function() {
		var that = this;
		return new Ext.Toolbar([
			{
				text: _('Add'), 
				handler: function () {
					that.store.insert(0, new that.record({name: '', value: ''}));
				}
			},
			{
				text: _('Delete'), 
				handler: function () {
					Ext.each( that.getSelectionModel().getSelections(), function( selection, idx ) {
						that.store.remove(selection);
					}, this );

					var data = {};
					Ext.each( that.store.data.items, function( item, index ) {
						data[item.data.name] = item.data.value;
					}, this );
					
					that.data = data;
				}
			}
		]);
	},

	getColumnModel: function() {
		return new Ext.grid.ColumnModel({
			defaults: {
				width: 250,
				sortable: true
			},
			columns: [{
				id: 'service-config-name',
				header: _('Name'),
				dataIndex: 'name',
				editor: {
					xtype: 'textfield'
				}
			}, {
				id: 'service-config-value',
				header: _('Value'),
				dataIndex: 'value',
				editor: {
					xtype: 'textfield'
				}
			}]
		});
	},

	getStore: function() {
		return new Ext.data.ArrayStore({
			autoSave: true,
			fields: [
				{name: 'name', type: 'string'},
				{name: 'value', type: 'string'}
			]
		});
	},

	listeners: {
		render: function (r) {
			Ext.iterate(this.data, function (key, value, object) {
				this.store.loadData([[key, value]], true);
			}, this);
		},
		afteredit: function (obj) {
			if (obj.record.data.name.trim() !== '') {
				this.data[obj.record.data.name] = obj.record.data.value;
			}
		}
	}

});

Ext.reg('MShop.panel.service.configui', MShop.panel.service.ConfigUi);