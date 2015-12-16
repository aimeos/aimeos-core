/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

Ext.ns('MShop.panel.tag');

MShop.panel.tag.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Tag',
    idProperty : 'tag.id',
    siteidProperty : 'tag.siteid',
    itemUiXType : 'MShop.panel.tag.itemui',

    autoExpandColumn : 'product-tag-label',

    filterConfig : {
        filters : [{
            dataIndex : 'tag.label',
            operator : '=~',
            value : ''
        }]
    },

    getColumns : function() {
        // make sure type store gets loaded in same batch as this grid data
        this.typeStore = MShop.GlobalStoreMgr.get('Tag_Type');

        return [{
            xtype : 'gridcolumn',
            dataIndex : 'tag.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'tag.typeid',
            header : MShop.I18n.dt('admin', 'Type'),
            sortable : true,
            width : 70,
            renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "tag.type.label"], true)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'tag.languageid',
            header : MShop.I18n.dt('admin', 'Language'),
            sortable : true,
            width : 70,
            renderer : MShop.elements.language.renderer
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'tag.label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            id : 'product-tag-label'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'tag.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'tag.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'tag.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 130,
            editable : false,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.tag.listuismall', MShop.panel.tag.ListUiSmall);
