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
var TreeTest = function(){
    // shorthand
    var Tree = Ext.tree;
    
    return {
        init : function(){
            // yui-ext tree
            var tree = new Tree.TreePanel({
                animate:true, 
                autoScroll:true,
                loader: new Tree.TreeLoader({dataUrl:'get-nodes.php'}),
                enableDD:true,
                containerScroll: true,
                border: false,
                width: 250,
                height: 300,
                dropConfig: {appendOnly:true}
            });
            
            // add a tree sorter in folder mode
            new Tree.TreeSorter(tree, {folderSort:true});
            
            // set the root node
            var root = new Tree.AsyncTreeNode({
                text: 'Ext JS', 
                draggable:false, // disable root node dragging
                id:'src'
            });
            tree.setRootNode(root);
            
            // render the tree
            tree.render('tree');
            
            root.expand(false, /*no anim*/ false);
            
            //-------------------------------------------------------------
            
            // ExtJS tree            
            var tree2 = new Tree.TreePanel({
                animate:true,
                autoScroll:true,
                //rootVisible: false,
                loader: new Ext.tree.TreeLoader({
                    dataUrl:'get-nodes.php',
                    baseParams: {path:'extjs'} // custom http params
                }),
                containerScroll: true,
                border: false,
                width: 250,
                height: 300,
                enableDD:true,
                dropConfig: {appendOnly:true}
            });
            
            // add a tree sorter in folder mode
            new Tree.TreeSorter(tree2, {folderSort:true});
            
            // add the root node
            var root2 = new Tree.AsyncTreeNode({
                text: 'Extensions', 
                draggable:false, 
                id:'ux'
            });
            tree2.setRootNode(root2);
            tree2.render('tree2');
            
            root2.expand(false, /*no anim*/ false);
        }
    };
}();

Ext.EventManager.onDocumentReady(TreeTest.init, TreeTest, true);