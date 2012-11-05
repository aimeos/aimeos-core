/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: ConfigUi.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.plugin');

MShop.panel.plugin.ConfigUi = Ext.extend(Ext.grid.EditorGridPanel, {

	stripeRows: true,

	initComponent: function() {
		this.title = _('Configuration');		
		this.colModel = this.getColumnModel();
		this.tbar = this.getToolBar();
		this.store = this.getStore();
		this.sm = new Ext.grid.RowSelectionModel();
		this.record = Ext.data.Record.create([
			{name: 'name', type: 'string'},
			{name: 'value', type: 'string'}
		]);

		if (!Ext.isObject(this.data)) {
			this.data = {};
		}

		MShop.panel.plugin.ConfigUi.superclass.initComponent.call(this);
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
					var selection = that.getSelectionModel().getSelections()[0];
					if (selection) {
						that.store.remove(selection);
						var data = {};
						Ext.each(that.store.data.items, function (item, index) {
							data[item.data.name] = item.data.value;
						}, this);
						that.data = data;
					}
				}
			}
		]);
	},

	getColumnModel: function() {
		return new Ext.grid.ColumnModel({
			defaults: { width: 250, sortable: true },
			columns: [
				{header: _('Name'), dataIndex: 'name', editor: { xtype: 'textfield'}},
				{header: _('Value'), dataIndex: 'value', editor: { xtype: 'textfield'}}
			]
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
			if (obj.record.data.name.trim() !== '' && obj.record.data.value.trim() !== '') {
				this.data[obj.record.data.name] = obj.record.data.value;
			}
		}
	}

});

Ext.reg('MShop.panel.plugin.configui', MShop.panel.plugin.ConfigUi);