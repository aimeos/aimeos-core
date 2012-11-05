/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.grid.CheckboxSelectionModel
 * @extends Ext.grid.RowSelectionModel
 * A custom selection model that renders a column of checkboxes that can be toggled to select or deselect rows.
 * @constructor
 * @param {Object} config The configuration options
 */
Ext.grid.CheckboxSelectionModel = Ext.extend(Ext.grid.RowSelectionModel, {

    /**
     * @cfg {Boolean} checkOnly <tt>true</tt> if rows can only be selected by clicking on the
     * checkbox column (defaults to <tt>false</tt>).
     */
    /**
     * @cfg {String} header Any valid text or HTML fragment to display in the header cell for the
     * checkbox column.  Defaults to:<pre><code>
     * '&lt;div class="x-grid3-hd-checker">&#38;#160;&lt;/div>'</tt>
     * </code></pre>
     * The default CSS class of <tt>'x-grid3-hd-checker'</tt> displays a checkbox in the header
     * and provides support for automatic check all/none behavior on header click. This string
     * can be replaced by any valid HTML fragment, including a simple text string (e.g.,
     * <tt>'Select Rows'</tt>), but the automatic check all/none behavior will only work if the
     * <tt>'x-grid3-hd-checker'</tt> class is supplied.
     */
    header : '<div class="x-grid3-hd-checker">&#160;</div>',
    /**
     * @cfg {Number} width The default width in pixels of the checkbox column (defaults to <tt>20</tt>).
     */
    width : 20,
    /**
     * @cfg {Boolean} sortable <tt>true</tt> if the checkbox column is sortable (defaults to
     * <tt>false</tt>).
     */
    sortable : false,

    // private
    menuDisabled : true,
    fixed : true,
    hideable: false,
    dataIndex : '',
    id : 'checker',

    constructor : function(){
        Ext.grid.CheckboxSelectionModel.superclass.constructor.apply(this, arguments);

        if(this.checkOnly){
            this.handleMouseDown = Ext.emptyFn;
        }
    },

    // private
    initEvents : function(){
        Ext.grid.CheckboxSelectionModel.superclass.initEvents.call(this);
        this.grid.on('render', function(){
            var view = this.grid.getView();
            view.mainBody.on('mousedown', this.onMouseDown, this);
            Ext.fly(view.innerHd).on('mousedown', this.onHdMouseDown, this);

        }, this);
    },

    // If handleMouseDown was called from another event (enableDragDrop), set a flag so
    // onMouseDown does not process it a second time
    handleMouseDown : function() {
        Ext.grid.CheckboxSelectionModel.superclass.handleMouseDown.apply(this, arguments);
        this.mouseHandled = true;
    },

    // private
    onMouseDown : function(e, t){
        if(e.button === 0 && t.className == 'x-grid3-row-checker'){ // Only fire if left-click
            e.stopEvent();
            var row = e.getTarget('.x-grid3-row');

            // mouseHandled flag check for a duplicate selection (handleMouseDown) call
            if(!this.mouseHandled && row){
                var index = row.rowIndex;
                if(this.isSelected(index)){
                    this.deselectRow(index);
                }else{
                    this.selectRow(index, true);
                    this.grid.getView().focusRow(index);
                }
            }
        }
        this.mouseHandled = false;
    },

    // private
    onHdMouseDown : function(e, t){
        if(t.className == 'x-grid3-hd-checker'){
            e.stopEvent();
            var hd = Ext.fly(t.parentNode);
            var isChecked = hd.hasClass('x-grid3-hd-checker-on');
            if(isChecked){
                hd.removeClass('x-grid3-hd-checker-on');
                this.clearSelections();
            }else{
                hd.addClass('x-grid3-hd-checker-on');
                this.selectAll();
            }
        }
    },

    // private
    renderer : function(v, p, record){
        return '<div class="x-grid3-row-checker">&#160;</div>';
    }
});