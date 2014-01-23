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
 * @class Ext.grid.GridDragZone
 * @extends Ext.dd.DragZone
 * <p>A customized implementation of a {@link Ext.dd.DragZone DragZone} which provides default implementations of two of the
 * template methods of DragZone to enable dragging of the selected rows of a GridPanel.</p>
 * <p>A cooperating {@link Ext.dd.DropZone DropZone} must be created who's template method implementations of
 * {@link Ext.dd.DropZone#onNodeEnter onNodeEnter}, {@link Ext.dd.DropZone#onNodeOver onNodeOver},
 * {@link Ext.dd.DropZone#onNodeOut onNodeOut} and {@link Ext.dd.DropZone#onNodeDrop onNodeDrop}</p> are able
 * to process the {@link #getDragData data} which is provided.
 */
Ext.grid.GridDragZone = function(grid, config){
    this.view = grid.getView();
    Ext.grid.GridDragZone.superclass.constructor.call(this, this.view.mainBody.dom, config);
    this.scroll = false;
    this.grid = grid;
    this.ddel = document.createElement('div');
    this.ddel.className = 'x-grid-dd-wrap';
    // prevent the default action, but don't stop propagation
    this.preventDefault = true;
};

Ext.extend(Ext.grid.GridDragZone, Ext.dd.DragZone, {
    ddGroup : "GridDD",

    /**
     * <p>The provided implementation of the getDragData method which collects the data to be dragged from the GridPanel on mousedown.</p>
     * <p>This data is available for processing in the {@link Ext.dd.DropZone#onNodeEnter onNodeEnter}, {@link Ext.dd.DropZone#onNodeOver onNodeOver},
     * {@link Ext.dd.DropZone#onNodeOut onNodeOut} and {@link Ext.dd.DropZone#onNodeDrop onNodeDrop} methods of a cooperating {@link Ext.dd.DropZone DropZone}.</p>
     * <p>The data object contains the following properties:<ul>
     * <li><b>grid</b> : Ext.Grid.GridPanel<div class="sub-desc">The GridPanel from which the data is being dragged.</div></li>
     * <li><b>ddel</b> : htmlElement<div class="sub-desc">An htmlElement which provides the "picture" of the data being dragged.</div></li>
     * <li><b>rowIndex</b> : Number<div class="sub-desc">The index of the row which receieved the mousedown gesture which triggered the drag.</div></li>
     * <li><b>selections</b> : Array<div class="sub-desc">Array of the selected Records which are being dragged from the GridPanel.
     * Unless a CellSelectionModel is being used and the grid is configured <code>dragCell: true</code>, in which case, this will be
     * an Array containing the single selected cell data as <code>[rowIndex, cellIndex]</code>.</div></li>
     * </ul></p>
     */
    getDragData : function(e){
        var t = Ext.lib.Event.getTarget(e),
            sm,
            rowIndex = this.view.findRowIndex(t),
            cellIndex,
            selectedCell,
            selection;

        if (rowIndex !== false){
            sm = this.grid.selModel;

            // Handle mousedown on unselected items (depending on what kind of selection we are using)
            // Select the mousedowned item
            if (sm.getSelectedCell) {
                cellIndex = this.view.findCellIndex(t);
                selectedCell = sm.getSelectedCell();
                if (!selectedCell || selectedCell[0] !== rowIndex || selectedCell[1] !== cellIndex) {
                    sm.handleMouseDown(this.grid, rowIndex, cellIndex, e);
                }
                if (this.grid.dragCell) {
                    // Selection is the cell coordinates
                    selection = sm.getSelectedCell();
                    if (!this.grid.hasOwnProperty('ddText')) {
                        this.grid.ddText = '{0} selected cell{1}';
                    }
                } else {
                    // Selection is the mousedowned row
                    selection = [this.grid.store.getAt(rowIndex)];
                }
            } else {
                if(!sm.isSelected(rowIndex) || e.hasModifier()){
                    sm.handleMouseDown(this.grid, rowIndex, e);
                }
                selection = sm.getSelections();
            }
            return {grid: this.grid, ddel: this.ddel, rowIndex: rowIndex, selections: selection};
        }
        return false;
    },

    /**
     * <p>The provided implementation of the onInitDrag method. Sets the <tt>innerHTML</tt> of the drag proxy which provides the "picture"
     * of the data being dragged.</p>
     * <p>The <tt>innerHTML</tt> data is found by calling the owning GridPanel's {@link Ext.grid.GridPanel#getDragDropText getDragDropText}.</p>
     */
    onInitDrag : function(e){
        var data = this.dragData;
        this.ddel.innerHTML = this.grid.getDragDropText();
        this.proxy.update(this.ddel);
        // fire start drag?
    },

    /**
     * An empty immplementation. Implement this to provide behaviour after a repair of an invalid drop. An implementation might highlight
     * the selected rows to show that they have not been dragged.
     */
    afterRepair : function(){
        this.dragging = false;
    },

    /**
     * <p>An empty implementation. Implement this to provide coordinates for the drag proxy to slide back to after an invalid drop.</p>
     * <p>Called before a repair of an invalid drop to get the XY to animate to.</p>
     * @param {EventObject} e The mouse up event
     * @return {Array} The xy location (e.g. [100, 200])
     */
    getRepairXY : function(e, data){
        return false;
    },

    onEndDrag : function(data, e){
        // fire end drag?
    },

    onValidDrop : function(dd, e, id){
        // fire drag drop?
        this.hideProxy();
    },

    beforeInvalidDrop : function(e, id){

    }
});
