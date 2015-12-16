/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
    maskDisabled : true,
    domain : 'catalog',

    recordName : 'Catalog',
    idProperty : 'catalog.id',
    exportMethod : 'Catalog_Export_Text.createJob',
    importMethod : 'Catalog_Import_Text.uploadFile',


    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Catalog');
        this.domain = 'catalog';
        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        this.recordClass = MShop.Schema.getRecord(this.recordName);

        this.initLoader(true);

        // fake a root -> needed by extjs
        this.root = new Ext.tree.AsyncTreeNode({
            id : 'root'
        });

        MShop.panel.catalog.TreeUi.superclass.initComponent.call(this);
    },

    inspectCreateNode : function(attr) {

        attr.id = attr['catalog.id'];
        attr.label = attr['catalog.label'];
        attr.text = attr['catalog.id'] + " - " + attr['catalog.label'];
        attr.code = attr['catalog.code'];
        attr.status = attr['catalog.status'];
        attr.cls = 'statustext-' + attr.status;

        // create record and insert into own store
        this.store.suspendEvents(false);

        this.store.remove(this.store.getById(attr.id));
        this.store.add([new this.recordClass(attr, attr.id)]);

        this.store.resumeEvents();
    }
});


// hook this into the main tab panel
Ext.ux.ItemRegistry.registerItem('MShop.MainTabPanel', 'MShop.panel.catalog.treeui', MShop.panel.catalog.TreeUi, 30);
