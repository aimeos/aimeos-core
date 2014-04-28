/*!
 * Copyright (c) Metaways Infosystems GmbH, 2013
 */


Ext.ns('MShop.panel.coupon');

MShop.panel.coupon.ConfigUi = Ext.extend(Ext.grid.EditorGridPanel, {

	stripeRows: true,
	autoExpandColumn : 'coupon-config-value',

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

		MShop.panel.coupon.ConfigUi.superclass.initComponent.call(this);
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
				{header: _('Value'), dataIndex: 'value', editor: { xtype: 'textfield'}, id:'coupon-config-value'}
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
			if (obj.record.data.name.trim() !== '') {
				if( obj.originalValue != obj.record.data.name ) {
					delete this.data[obj.originalValue];
				}
				this.data[obj.record.data.name] = obj.record.data.value;
			}
		}
	}

});

Ext.reg('MShop.panel.coupon.configui', MShop.panel.coupon.ConfigUi);