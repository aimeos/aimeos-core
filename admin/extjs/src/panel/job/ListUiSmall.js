/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
        filters : [{
            dataIndex : 'job.ctime',
            operator : '>',
            value : Ext.util.Format.date(new Date(new Date().valueOf() - 7 * 86400 * 1000), 'Y-m-d H:i:s')
        }]
    },

    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Jobs');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.job.ListUiSmall.superclass.initComponent.call(this);
    },

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'job.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            width : 50,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'job.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            width : 50,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'job.label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            editable : false,
            id : 'job-list-label'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'job.method',
            header : MShop.I18n.dt('admin', 'Method'),
            sortable : true,
            width : 200,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'job.parameter',
            header : MShop.I18n.dt('admin', 'Parameter'),
            sortable : false,
            width : 100,
            editable : false,
            hidden : true,
            renderer : function(data) {
                var result = '';
                if(data instanceof Object) {
                    for( var name in data) {
                        result += name + ': ' + data[name] + '<br/>';
                    }
                }
                return result;
            }
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'job.result',
            header : MShop.I18n.dt('admin', 'Result'),
            sortable : false,
            width : 200,
            editable : false,
            renderer : function(data) {
                var result = '';
                if(data instanceof Object) {
                    for( var name in data) {
                        result += name + ': ' + data[name] + '<br/>';
                    }
                }
                return result;
            }
        }, {
            xtype : 'datecolumn',
            dataIndex : 'job.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'job.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'job.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 80,
            editable : false
        }];
    }
});

Ext.reg('MShop.panel.job.listuismall', MShop.panel.job.ListUiSmall);
