/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: TreeUi.js 14834 2012-01-12 17:13:15Z nsendetzky $
 */


Ext.ns('MShop.panel.catalog');

MShop.panel.catalog.TreeUi = Ext.extend(Ext.tree.TreePanel, {

	rootVisible : true,
	useArrows : true,
	autoScroll : true,
	animate : true,
	enableDD : true,
	containerScroll : true,
	border : false,
	ddGroup : 'MShop.panel.catalog',
	maskDisabled: true,

	recordName : 'Catalog',
	idProperty : 'catalog.id',
	exportMethod : 'Catalog_Export_Text.createJob',
	importMethod: 'Catalog_Import_Text.uploadFile',


	initComponent : function()
	{
		this.title = _('Catalog');

		MShop.panel.AbstractListUi.prototype.initActions.call(this);
		MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

		this.recordClass = MShop.Schema.getRecord(this.recordName);

		this.initLoader();

		// fake a root -> needed by extjs
		this.root = new Ext.tree.AsyncTreeNode( { id : 'root' });

		// store is used for data transfer mainly
		this.initStore();

		this.on('movenode', this.onMoveNode, this);
		this.on('containercontextmenu', this.onContainerContextMenu, this);
		this.on('contextmenu', this.onContextMenu, this);
		this.on('dblclick', this.onOpenEditWindow.createDelegate(this, [ 'edit' ]), this);
		this.getSelectionModel().on('selectionchange', this.onSelectionChange, this, { buffer : 10 });

		MShop.panel.catalog.TreeUi.superclass.initComponent.call(this);
	},

	afterRender: function() {
		MShop.panel.catalog.TreeUi.superclass.afterRender.apply(this, arguments);

		this.disable();
		this.root.expand();
	},

	getCtxMenu : MShop.panel.AbstractListUi.prototype.getCtxMenu,


	onExport: function() {
		var win = new MShop.elements.exportlanguage.Window();
		win.on('save', this.finishExport, this);
		win.show();
	},

	finishExport: function(langwin, languages) {
		var selection = this.getSelectionModel().getSelectedNode();

		var downloader = new Ext.ux.file.Downloader({
			url: MShop.config.smd.target,
			params: {
				method: this.exportMethod,
				params: Ext.encode({
					items: selection.id,
					lang: languages,
					site: MShop.config.site['locale.site.code']
				})
			}
		}).start();
	},

	inspectCreateNode : function(attr)
	{

		// adding label to object as text is necessary
		var status = attr['catalog.status'];

		attr.id = attr['catalog.id'];
		attr.text = attr['catalog.label'];
		attr.code = attr['catalog.code'];
		attr.cls = 'statustext-' + status;

		// create record and insert into own store
		this.store.suspendEvents(false);
		var oldRecord = this.store.getById(attr['catalog.id']);
		this.store.remove(oldRecord);

		this.store.add( [ new this.recordClass( {
				id : attr.id,
				status : status,
				code : attr['catalog.code'],
				label : attr['catalog.label'],
				'catalog.siteid' :attr['catalog.siteid'],
				'catalog.ctime' : attr['catalog.ctime'],
				'catalog.mtime' : attr['catalog.mtime'],
				'catalog.editor' : attr['catalog.editor']
		}, attr.id ) ] );

		this.store.resumeEvents();
	},

	initLoader : function()
	{
		this.loader = new Ext.tree.TreeLoader( {

				nodeParameter : 'items',
				paramOrder : [ 'site', 'items' ],
				baseParams : {
					site : MShop.config.site["locale.site.code"]
				},
				directFn : MShop.API.Catalog.getTree,

				processResponse : function(response, node, callback, scope)
				{
					// reset root
					if (node.id === 'root') {
						// we create the node to have it in the store
						var newNode = this.createNode(response.responseText.items);

						node.setId(response.responseText.items['catalog.id']);
						node.setText(response.responseText.items['catalog.label']);
						node.getUI().addClass(newNode.attributes.cls);
						node.getOwnerTree().enable();
						node.getOwnerTree().actionAdd.setDisabled(node.id !== 'root');
					}

					// cut off item itself
					response.responseData = response.responseText.items.children;
					return Ext.tree.TreeLoader.prototype.processResponse.apply(this, arguments);
				},

				createNode : Ext.tree.TreeLoader.prototype.createNode.createInterceptor( this.inspectCreateNode, this )
		});

		this.loader.on('loadexception', function(loader, node, response) {

			if (node.id === 'root') {
				// no root node yet
				node.getUI().hide();
				node.getOwnerTree().enable();
				return;
			}
		}, this);
	},

	// NOTE: loading is done via treeloader, records get
	// created/inserted in this store from the treeloader also
	initStore : function()
	{
		this.store = new Ext.data.DirectStore(Ext.apply( {
				autoLoad : false,
				remoteSort : true,
				hasMultiSort : true,
				fields : MShop.Schema.getRecord(this.recordName),
				api : {
					create : MShop.API.Catalog.insertItems,
					update : MShop.API.Catalog.saveItems,
					destroy : MShop.API.Catalog.deleteItems
				},
				writer : new Ext.data.JsonWriter( {
					writeAllFields : true,
					encode : false
				}),
				paramsAsHash : true,
				root : 'items',
				totalProperty : 'total',
				idProperty : this.idProperty
			},
			this.storeConfig ) );

		// make sure site param gets set for write actions
		this.store.on('exception', this.onStoreException, this);
		this.store.on('beforewrite', this.onBeforeWrite, this);
		this.store.on('write', this.onWrite, this);
	},

	onBeforeWrite : function(store, action, records, options)
	{
		if (action === 'create')
		{
			var parent = this.getSelectionModel().getSelectedNode();

			// NOTE: baseParams is the only hook we have here
			store.baseParams = store.baseParams || {};
			store.baseParams.parentid = parent ? parent.id : null;
		}

		MShop.panel.AbstractListUi.prototype.onBeforeWrite.apply(this, arguments);
	},

	onContainerContextMenu : function(tree, e)
	{
		e.preventDefault();

		// deselect all selections
		this.getSelectionModel().clearSelections();

		this.getCtxMenu().showAt(e.getXY());
	},

	onContextMenu : function(node, e)
	{
		e.preventDefault();

		// select ctx node
		this.getSelectionModel().select(node);

		this.getCtxMenu().showAt(e.getXY());
	},

	onDeleteSelectedItems : function()
	{
		var that = this;

		Ext.Msg.show({
			title: _('Delete items?'),
			msg: _('You are going to delete one or more items. Would you like to proceed?'),
			buttons: Ext.Msg.YESNO,
			fn: function (btn) {
				if (btn == 'yes') {
					var node = that.getSelectionModel().getSelectedNode(),
					root = that.getRootNode();

					if( node )
					{
						that.store.remove(that.store.getById(node.id));
						if (node === root) {
							that.getSelectionModel().clearSelections();

							that.setRootNode(new Ext.tree.AsyncTreeNode( { id : 'root' }));
							that.getRootNode().getUI().hide();
						} else {
							node.remove(true);
						}
					}
				}
			},
			animEl: 'elId',
			icon: Ext.MessageBox.QUESTION
		});
	},

	onOpenEditWindow : function(action)
	{
		var record;

		if( action !== 'add' ) {
			record = this.store.getById(this.getSelectionModel().getSelectedNode().id);
		}

		var itemUi = Ext.ComponentMgr.create(
			{
				xtype : 'MShop.panel.catalog.itemui',
				record : record,
				store : this.store
			}
		);

		itemUi.show();
	},

	onSelectionChange : function(sm, node)
	{
		this.actionAdd.setDisabled(!node && this.getRootNode().id !== 'root');
		this.actionEdit.setDisabled(!node);
		this.actionDelete.setDisabled(!node);
		this.actionExport.setDisabled(!node);
	},

	onStoreException : MShop.panel.AbstractListUi.prototype.onStoreException,

	onMoveNode : function(tree, node, oldParent, newParent, index)
	{
		var ref = node.nextSibling ? node.nextSibling.id : null;

		MShop.API.Catalog.moveItems(
			MShop.config.site["locale.site.code"],
			node.id,
			oldParent.id,
			newParent.id,
			ref,
			function( success, response)
			{
				if (!success) {
					this.onStoreException(null, null, null, null, response);
				}
			},
			this );
	},

	onWrite : function(store, action, result, t, rs)
	{
		var selectedNode = this.getSelectionModel().getSelectedNode();

		Ext.each(
			[].concat(rs),
			function(r) {
				var newNode = this.getLoader().createNode(r.data);

				switch (action)
				{
					case 'create':
						if (selectedNode) {
							selectedNode.appendChild(newNode);
						} else {
							this.setRootNode(newNode);
						}
						break;
					case 'update':
						// @TODO: rethink update vs.
						// recreate -> affects expands
						// of subnodes
						var oldNode = this.getNodeById(r.id);
						if (oldNode === this.getRootNode()) {
							this.setRootNode(newNode);
						} else {
							oldNode.parentNode.replaceChild(newNode, oldNode);
						}
						break;
					case 'destroy':
						break; // do nothing
					default:
						throw new Ext.Error('Invalid action "' + action + '"');
				}
			},
			this );
	},

	setDomainProperty : MShop.panel.AbstractListUi.prototype.setDomainProperty,
	setSiteParam : MShop.panel.AbstractListUi.prototype.setSiteParam
});

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', MShop.panel.catalog.TreeUi, 30);
