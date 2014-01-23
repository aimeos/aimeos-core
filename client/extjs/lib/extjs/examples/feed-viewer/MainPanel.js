/*
This file is part of Ext JS 3.4

Copyright (c) 2011-2013 Sencha Inc

Contact:  http://www.sencha.com/contact

GNU General Public License Usage
This file may be used under the terms of the GNU General Public License version 3.0 as
published by the Free Software Foundation and appearing in the file LICENSE included in the
packaging of this file.

Please review the following information to ensure the GNU General Public License version 3.0
requirements will be met: http://www.gnu.org/copyleft/gpl.html.

If you are unsure which license is appropriate for your use, please contact the sales department
at http://www.sencha.com/contact.

Build date: 2013-04-03 15:07:25
*/
Ext.define('MainPanel', {

    extend: 'Ext.TabPanel',
    
    xtype: 'appmainpanel',

    constructor: function() {
        var me = this;

        me.preview = new Ext.Panel({
            id: 'preview',
            region: 'south',
            cls:'preview',
            autoScroll: true,
            listeners: FeedViewer.LinkInterceptor,

            tbar: [{
                id:'tab',
                text: 'View in New Tab',
                iconCls: 'new-tab',
                disabled:true,
                handler : me.openTab,
                scope: me
            },
            '-',
            {
                id:'win',
                text: 'Go to Post',
                iconCls: 'new-win',
                disabled:true,
                scope: me,
                handler : function(){
                    window.open(me.gsm.getSelected().data.link);
                }
            }],

            clear: function(){
                this.body.update('');
                var items = this.topToolbar.items;
                items.get('tab').disable();
                items.get('win').disable();
            }
        });

        me.grid = Ext.create({
            viewer: me,
            xtype: 'appfeedgrid',
            tbar:[{
                text:'Open All',
                tooltip: {title:'Open All',text:'Opens all item in tabs'},
                iconCls: 'tabs',
                handler: me.openAll,
                scope:me
            },
            '-',
            {
                split:true,
                text:'Reading Pane',
                tooltip: {title:'Reading Pane',text:'Show, move or hide the Reading Pane'},
                iconCls: 'preview-bottom',
                handler: me.movePreview.createDelegate(me, []),
                menu:{
                    id:'reading-menu',
                    cls:'reading-menu',
                    width:100,
                    items: [{
                        text:'Bottom',
                        checked:true,
                        group:'rp-group',
                        checkHandler:me.movePreview,
                        scope:me,
                        iconCls:'preview-bottom'
                    },{
                        text:'Right',
                        checked:false,
                        group:'rp-group',
                        checkHandler:me.movePreview,
                        scope:me,
                        iconCls:'preview-right'
                    },{
                        text:'Hide',
                        checked:false,
                        group:'rp-group',
                        checkHandler:me.movePreview,
                        scope:me,
                        iconCls:'preview-hide'
                    }]
                }
            },
            '-',
            {
                pressed: true,
                enableToggle:true,
                text:'Summary',
                tooltip: {title:'Post Summary',text:'View a short summary of each item in the list'},
                iconCls: 'summary',
                scope:me,
                toggleHandler: function(btn, pressed){
                    this.grid.togglePreview(pressed);
                }
            }]
        });

        me.callParent([{
            id:'main-tabs',
            activeTab:0,
            region:'center',
            margins:'0 5 5 0',
            resizeTabs:true,
            tabWidth:150,
            minTabWidth: 120,
            enableTabScroll: true,
            plugins: new Ext.ux.TabCloseMenu(),
            items: {
                id:'main-view',
                layout:'border',
                title:'Loading...',
                hideMode:'offsets',
                items:[
                    me.grid, {
                    id:'bottom-preview',
                    layout:'fit',
                    items:me.preview,
                    height: 250,
                    split: true,
                    border:false,
                    region:'south'
                }, {
                    id:'right-preview',
                    layout:'fit',
                    border:false,
                    region:'east',
                    width:350,
                    split: true,
                    hidden:true
                }]
            }
        }]);

        me.gsm = me.grid.getSelectionModel();

        me.gsm.on('rowselect', function(sm, index, record){
            FeedViewer.getTemplate().overwrite(me.preview.body, record.data);
            var items = me.preview.topToolbar.items;
            items.get('tab').enable();
            items.get('win').enable();
        }, me, {buffer:250});

        me.grid.store.on('beforeload', me.preview.clear, me.preview);
        me.grid.store.on('load', me.gsm.selectFirstRow, me.gsm);

        me.grid.on('rowdblclick', me.openTab, me);
    },

    loadFeed : function(feed){
        this.grid.loadFeed(feed.url);
        Ext.getCmp('main-view').setTitle(feed.text);
    },

    movePreview : function(m, pressed){
        if(!m){ // cycle if not a menu item click
            var items = Ext.menu.MenuMgr.get('reading-menu').items.items;
            var b = items[0], r = items[1], h = items[2];
            if(b.checked){
                r.setChecked(true);
            }else if(r.checked){
                h.setChecked(true);
            }else if(h.checked){
                b.setChecked(true);
            }
            return;
        }
        if(pressed){
            var preview = this.preview;
            var right = Ext.getCmp('right-preview');
            var bot = Ext.getCmp('bottom-preview');
            var btn = this.grid.getTopToolbar().items.get(2);
            switch(m.text){
                case 'Bottom':
                    right.hide();
                    bot.add(preview);
                    bot.show();
                    bot.ownerCt.doLayout();
                    btn.setIconClass('preview-bottom');
                    break;
                case 'Right':
                    bot.hide();
                    right.add(preview);
                    right.show();
                    right.ownerCt.doLayout();
                    btn.setIconClass('preview-right');
                    break;
                case 'Hide':
                    preview.ownerCt.hide();
                    preview.ownerCt.ownerCt.doLayout();
                    btn.setIconClass('preview-hide');
                    break;
            }
        }
    },

    openTab : function(record){
        record = (record && record.data) ? record : this.gsm.getSelected();
        var d = record.data;
        var id = !d.link ? Ext.id() : d.link.replace(/[^A-Z0-9-_]/gi, '');
        var tab;
        if(!(tab = this.getItem(id))){
            tab = new Ext.Panel({
                id: id,
                cls:'preview single-preview',
                title: d.title,
                tabTip: d.title,
                html: FeedViewer.getTemplate().apply(d),
                closable:true,
                listeners: FeedViewer.LinkInterceptor,
                autoScroll:true,
                border:true,

                tbar: [{
                    text: 'Go to Post',
                    iconCls: 'new-win',
                    handler : function(){
                        window.open(d.link);
                    }
                }]
            });
            this.add(tab);
        }
        this.setActiveTab(tab);
    },

    openAll : function(){
        this.beginUpdate();
        this.grid.store.data.each(this.openTab, this);
        this.endUpdate();
    }
});