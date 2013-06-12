/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: TreeUi.js 14834 2012-01-12 17:13:15Z nsendetzky $
 */


Ext.ns('MShop.panel.catalog');

MShop.panel.catalog.TreeUi = Ext.extend(MShop.panel.AbstractTreeUi, {

	rootVisible : true,
	useArrows : true,
	autoScroll : true,
	animate : true,
	enableDD : true,
	containerScroll : true,
	border : false,
	ddGroup : 'MShop.panel.catalog',
	maskDisabled: true,
	domain: 'catalog',

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

		this.initLoader(false);

		// fake a root -> needed by extjs
		this.root = new Ext.tree.AsyncTreeNode( { id : 'root' } );
		
		MShop.panel.catalog.TreeUi.superclass.initComponent.call(this);
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
				'catalog.siteid' : attr['catalog.siteid'],
				'catalog.ctime' : attr['catalog.ctime'],
				'catalog.mtime' : attr['catalog.mtime'],
				'catalog.editor' : attr['catalog.editor']
		}, attr.id ) ] );

		this.store.resumeEvents();
	},
});

// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.catalog.treeui', MShop.panel.catalog.TreeUi, 30);
