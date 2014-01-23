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
Imgorg.ImageDv = Ext.extend(Ext.DataView,{
    tpl: new Ext.XTemplate(
        '<tpl for=".">',
        '<div class="thumb-wrap" id="{id}">',
        '<div class="thumb"><img src="images/thumbs/{filename}" class="thumb-img"></div>',
        '<span class="x-editable">{filename:ellipsis(15)}</span></div>',
        '</tpl>'
    ),
    
    initComponent: function() {
        Ext.apply(this, {
            itemSelector: 'div.thumb-wrap',
            style: 'overflow:auto',
            multiSelect: true,
            overClass: 'x-view-over',
            emptyText: 'No images to display',
            plugins: [new Ext.DataView.DragSelector({
                dragSafe: true
            }), new Ext.DataView.LabelEditor({
                allowBlank: false,
                alignment: 'c-c',
                dataIndex: 'filename'
            })],
            store: new Ext.data.DirectStore({
                directFn: Imgorg.ss.Images.load,
                root: '',
                fields: ['filename', 'url', 'id', 'size']
            })
        });
        
        this.addEvents('viewitem');
        Imgorg.ImageDv.superclass.initComponent.call(this);
        this.on({// hacks to force the labeleditor to stop editing when we get a click elsewhere
            click: function() {
                this.plugins[1].completeEdit();
            },
            containerclick: function(dv, e) {
                this.plugins[1].completeEdit();
            },
            contextmenu: this.onContextMenu,
            containercontextmenu: this.onContextMenu,
            scope: this
        });
        this.store.on('update', this.syncRename, this);
    },
    
    afterRender: function() {
        Imgorg.ImageDv.superclass.afterRender.call(this);
        this.el.unselectable(); // messy if they can select the text of the file names
    },
    
    onContextMenu: function(e, node) {
        e.stopEvent();
        if(!this.contMenu) {
            this.contMenu = new Ext.menu.Menu({
                items: [{
                    text: 'View in Tab(s)',
                    handler: function() {
                        this.fireEvent('viewitem', this, node);
                    },
                    scope: this
                },{
                    text: 'Add to Album',
                    handler: this.selectAlbum,
                    scope: this
                },{
                    text: 'Tag',
                    handler: this.tag,
                    scope: this
                },{
                    text: 'Remove',
                    handler: this.removeImages,
                    scope: this
                }]
            });
        }
        this.currentNode = node;
        this.contMenu.showAt(e.getXY());
    },
    
    selectAlbum: function() {
        if (!this.albumWin) {
            this.albumWin = new Imgorg.AlbumWin();
        }
        this.albumWin.selectedRecords = this.getSelectedRecords();
        this.albumWin.show(this.currentNode);
    },
    
    tag: function() {
        Imgorg.TagWin.selectedRecords = this.getSelectedRecords();
        Imgorg.TagWin.show(this.currentNode);
    },
    
    syncRename: function(store, rec, op) {
        if (op == 'edit') {
            Imgorg.ss.Images.rename({image: rec.data.id, name: rec.data.filename, url: rec.data.url});
        }
    },
    
    removeImages: function() {
        var recs = this.getSelectedRecords();
        var imageIds = [];
        for (var i = 0;i < recs.length;i++) {
            imageIds.push(recs[i].data.id);
            this.store.remove(recs[i]);
        }
        Imgorg.ss.Images.remove({images: imageIds});
    }
});
Ext.reg('img-dv', Imgorg.ImageDv);
