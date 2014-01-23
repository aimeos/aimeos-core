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
/**
 * @class Ext.tree.TreeSorter
 * Provides sorting of nodes in a {@link Ext.tree.TreePanel}.  The TreeSorter automatically monitors events on the
 * associated TreePanel that might affect the tree's sort order (beforechildrenrendered, append, insert and textchange).
 * Example usage:<br />
 * <pre><code>
new Ext.tree.TreeSorter(myTree, {
    folderSort: true,
    dir: "desc",
    sortType: function(value, node) {
        // sort by a custom, typed attribute:
        return parseInt(node.id, 10);
    }
});
</code></pre>
 * @constructor
 * @param {TreePanel} tree
 * @param {Object} config
 */
Ext.tree.TreeSorter = Ext.extend(Object, {
    
    constructor: function(tree, config){
        /**
     * @cfg {Boolean} folderSort True to sort leaf nodes under non-leaf nodes (defaults to false)
     */
    /**
     * @cfg {String} property The named attribute on the node to sort by (defaults to "text").  Note that this
     * property is only used if no {@link #sortType} function is specified, otherwise it is ignored.
     */
    /**
     * @cfg {String} dir The direction to sort ("asc" or "desc," case-insensitive, defaults to "asc")
     */
    /**
     * @cfg {String} leafAttr The attribute used to determine leaf nodes when {@link #folderSort} = true (defaults to "leaf")
     */
    /**
     * @cfg {Boolean} caseSensitive true for case-sensitive sort (defaults to false)
     */
    /**
     * @cfg {Function} sortType A custom "casting" function used to convert node values before sorting.  The function
     * will be called with a two parameters, the {@link #property} value for the node and the {@link Ext.tree.TreeNode} being evaluated. 
     * It is expected to return the node's sort value cast to the specific data type required for sorting.  This could be used, for example, when
     * a node's text (or other attribute) should be sorted as a date or numeric value.  See the class description for
     * example usage.
     */

    Ext.apply(this, config);
    tree.on({
        scope: this,
        beforechildrenrendered: this.doSort,
        append: this.updateSort,
        insert: this.updateSort,
        textchange: this.updateSortParent
    });

    var desc = this.dir && this.dir.toLowerCase() == 'desc',
        prop = this.property || 'text',
        sortType = this.sortType,
        folderSort = this.folderSort,
        caseSensitive = this.caseSensitive === true,
        leafAttr = this.leafAttr || 'leaf';

    if(Ext.isString(sortType)){
        sortType = Ext.data.SortTypes[sortType];
    }
    this.sortFn = function(n1, n2){
        var attr1 = n1.attributes,
            attr2 = n2.attributes;
            
        if(folderSort){
            if(attr1[leafAttr] && !attr2[leafAttr]){
                return 1;
            }
            if(!attr1[leafAttr] && attr2[leafAttr]){
                return -1;
            }
        }
        var prop1 = attr1[prop],
            prop2 = attr2[prop],
            v1 = sortType ? sortType(prop1, n1) : (caseSensitive ? prop1 : prop1.toUpperCase()),
            v2 = sortType ? sortType(prop2, n2) : (caseSensitive ? prop2 : prop2.toUpperCase());
            
        if(v1 < v2){
            return desc ? 1 : -1;
        }else if(v1 > v2){
            return desc ? -1 : 1;
        }
        return 0;
    };
    },
    
    doSort : function(node){
        node.sort(this.sortFn);
    },

    updateSort : function(tree, node){
        if(node.childrenRendered){
            this.doSort.defer(1, this, [node]);
        }
    },

    updateSortParent : function(node){
        var p = node.parentNode;
        if(p && p.childrenRendered){
            this.doSort.defer(1, this, [p]);
        }
    }    
});
