/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.panel.locale.site');

MShop.panel.locale.site.ConfigUi = Ext.extend(Ext.grid.EditorGridPanel, {

	stripeRows: true,

	initComponent: function() {
		this.title = MShop.I18n.dt( 'client/extjs', 'Configuration' );		
		this.colModel = this.getColumnModel();
		this.tbar = this.getToolBar();
		this.store = this.getStore();
		this.sm = new Ext.grid.RowSelectionModel();
		this.autoExpandColumn = 'locale-site-config-value';
		this.record = Ext.data.Record.create([
			{name: 'name', type: 'string'},
			{name: 'value', type: 'string'}
		]);

		if (!Ext.isObject(this.data)) {
			this.data = {};
		}

		MShop.panel.locale.site.ConfigUi.superclass.initComponent.call(this);
	},

	getToolBar: function() {
		var that = this;
		return new Ext.Toolbar([
			{
				text: MShop.I18n.dt( 'client/extjs', 'Add' ), 
				handler: function () {
					that.store.insert(0, new that.record({name: '', value: ''}));
				}
			},
			{
				text: MShop.I18n.dt( 'client/extjs', 'Delete' ), 
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
			defaults: { width: 250, sortable: true },
			columns: [ {
				header: MShop.I18n.dt( 'client/extjs', 'Name' ),
				dataIndex: 'name',
				editor: { xtype: 'textfield'}
			}, {
				header: MShop.I18n.dt( 'client/extjs', 'Value' ),
				dataIndex: 'value',
				editor: { xtype: 'textfield' },
				id: 'locale-site-config-value'
			} ]
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
				if( typeof value === "object" ) {
					value = Ext.util.JSON.encode(value);
				}
				this.store.loadData([[key, value]], true);
			}, this);
		},
		beforeedit: function (e) {
			if( typeof e.value === "object" ) {
				e.record.data[e.field] = Ext.util.JSON.encode(e.value);
			}
		},
		afteredit: function (obj) {
			if (obj.record.data.name.trim() !== '') {
				if( obj.originalValue != obj.record.data.name ) {
					delete this.data[obj.originalValue];
				}
				if( obj.record.data.value[0] === '{' ) {
					try {
						obj.record.data.value = Ext.util.JSON.decode(obj.record.data.value);
					} catch( err ) {
						Ext.Msg.alert(
							MShop.I18n.dt( 'client/extjs', 'Invalid data' ),
							String.format( MShop.I18n.dt( 'client/extjs', 'Invalid value for configuration key "{0}"' ), obj.record.data.name ) );
						
						throw new Ext.Error('InvalidData', obj.record.data);
					}
				}
				this.data[obj.record.data.name] = obj.record.data.value;
			}
		}
	}

});

Ext.reg('MShop.panel.locale.site.configui', MShop.panel.locale.site.ConfigUi);