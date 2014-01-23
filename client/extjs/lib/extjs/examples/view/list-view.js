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

Ext.onReady(function(){

    var store = new Ext.data.JsonStore({
        url: 'get-images.php',
        root: 'images',
        fields: ['name', 'url', {name:'size', type: 'float'}, {name:'lastmod', type:'date', dateFormat:'timestamp'}]
    });
    store.load();

    var listView = new Ext.list.ListView({
        store: store,
        multiSelect: true,
        emptyText: 'No images to display',
        reserveScrollOffset: true,

        columns: [{
            header: 'File',
            width: .5,
            dataIndex: 'name'
        },{
            header: 'Last Modified',
            xtype: 'datecolumn',
            format: 'm-d h:i a',
            width: .35, 
            dataIndex: 'lastmod'
        },{
            header: 'Size',
            dataIndex: 'size',
            tpl: '{size:fileSize}',
            align: 'right',
            cls: 'listview-filesize'
        }]
    });
    
    // put it in a Panel so it looks pretty
    var panel = new Ext.Panel({
        id:'images-view',
        width:425,
        height:250,
        collapsible:true,
        layout:'fit',
        title:'Simple ListView <i>(0 items selected)</i>',
        items: listView
    });
    panel.render(document.body);

    // little bit of feedback
    listView.on('selectionchange', function(view, nodes){
        var l = nodes.length;
        var s = l != 1 ? 's' : '';
        panel.setTitle('Simple ListView <i>('+l+' item'+s+' selected)</i>');
    });
});