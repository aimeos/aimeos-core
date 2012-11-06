/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.grid.GridView
 * @extends Ext.util.Observable
 * <p>This class encapsulates the user interface of an {@link Ext.grid.GridPanel}.
 * Methods of this class may be used to access user interface elements to enable
 * special display effects. Do not change the DOM structure of the user interface.</p>
 * <p>This class does not provide ways to manipulate the underlying data. The data
 * model of a Grid is held in an {@link Ext.data.Store}.</p>
 * @constructor
 * @param {Object} config
 */
Ext.grid.GridView = Ext.extend(Ext.util.Observable, {
    /**
     * Override this function to apply custom CSS classes to rows during rendering.  You can also supply custom
     * parameters to the row template for the current row to customize how it is rendered using the <b>rowParams</b>
     * parameter.  This function should return the CSS class name (or empty string '' for none) that will be added
     * to the row's wrapping div.  To apply multiple class names, simply return them space-delimited within the string
     * (e.g., 'my-class another-class'). Example usage:
    <pre><code>
viewConfig: {
    forceFit: true,
    showPreview: true, // custom property
    enableRowBody: true, // required to create a second, full-width row to show expanded Record data
    getRowClass: function(record, rowIndex, rp, ds){ // rp = rowParams
        if(this.showPreview){
            rp.body = '&lt;p>'+record.data.excerpt+'&lt;/p>';
            return 'x-grid3-row-expanded';
        }
        return 'x-grid3-row-collapsed';
    }
},
    </code></pre>
     * @param {Record} record The {@link Ext.data.Record} corresponding to the current row.
     * @param {Number} index The row index.
     * @param {Object} rowParams A config object that is passed to the row template during rendering that allows
     * customization of various aspects of a grid row.
     * <p>If {@link #enableRowBody} is configured <b><tt></tt>true</b>, then the following properties may be set
     * by this function, and will be used to render a full-width expansion row below each grid row:</p>
     * <ul>
     * <li><code>body</code> : String <div class="sub-desc">An HTML fragment to be used as the expansion row's body content (defaults to '').</div></li>
     * <li><code>bodyStyle</code> : String <div class="sub-desc">A CSS style specification that will be applied to the expansion row's &lt;tr> element. (defaults to '').</div></li>
     * </ul>
     * The following property will be passed in, and may be appended to:
     * <ul>
     * <li><code>tstyle</code> : String <div class="sub-desc">A CSS style specification that willl be applied to the &lt;table> element which encapsulates
     * both the standard grid row, and any expansion row.</div></li>
     * </ul>
     * @param {Store} store The {@link Ext.data.Store} this grid is bound to
     * @method getRowClass
     * @return {String} a CSS class name to add to the row.
     */

    /**
     * @cfg {Boolean} enableRowBody True to add a second TR element per row that can be used to provide a row body
     * that spans beneath the data row.  Use the {@link #getRowClass} method's rowParams config to customize the row body.
     */

    /**
     * @cfg {String} emptyText Default text (html tags are accepted) to display in the grid body when no rows
     * are available (defaults to ''). This value will be used to update the <tt>{@link #mainBody}</tt>:
    <pre><code>
    this.mainBody.update('&lt;div class="x-grid-empty">' + this.emptyText + '&lt;/div>');
    </code></pre>
     */

    /**
     * @cfg {Boolean} headersDisabled True to disable the grid column headers (defaults to <tt>false</tt>).
     * Use the {@link Ext.grid.ColumnModel ColumnModel} <tt>{@link Ext.grid.ColumnModel#menuDisabled menuDisabled}</tt>
     * config to disable the <i>menu</i> for individual columns.  While this config is true the
     * following will be disabled:<div class="mdetail-params"><ul>
     * <li>clicking on header to sort</li>
     * <li>the trigger to reveal the menu.</li>
     * </ul></div>
     */

    /**
     * <p>A customized implementation of a {@link Ext.dd.DragZone DragZone} which provides default implementations
     * of the template methods of DragZone to enable dragging of the selected rows of a GridPanel.
     * See {@link Ext.grid.GridDragZone} for details.</p>
     * <p>This will <b>only</b> be present:<div class="mdetail-params"><ul>
     * <li><i>if</i> the owning GridPanel was configured with {@link Ext.grid.GridPanel#enableDragDrop enableDragDrop}: <tt>true</tt>.</li>
     * <li><i>after</i> the owning GridPanel has been rendered.</li>
     * </ul></div>
     * @property dragZone
     * @type {Ext.grid.GridDragZone}
     */

    /**
     * @cfg {Boolean} deferEmptyText True to defer <tt>{@link #emptyText}</tt> being applied until the store's
     * first load (defaults to <tt>true</tt>).
     */
    deferEmptyText : true,

    /**
     * @cfg {Number} scrollOffset The amount of space to reserve for the vertical scrollbar
     * (defaults to <tt>undefined</tt>). If an explicit value isn't specified, this will be automatically
     * calculated.
     */
    scrollOffset : undefined,

    /**
     * @cfg {Boolean} autoFill
     * Defaults to <tt>false</tt>.  Specify <tt>true</tt> to have the column widths re-proportioned
     * when the grid is <b>initially rendered</b>.  The
     * {@link Ext.grid.Column#width initially configured width}</tt> of each column will be adjusted
     * to fit the grid width and prevent horizontal scrolling. If columns are later resized (manually
     * or programmatically), the other columns in the grid will <b>not</b> be resized to fit the grid width.
     * See <tt>{@link #forceFit}</tt> also.
     */
    autoFill : false,

    /**
     * @cfg {Boolean} forceFit
     * Defaults to <tt>false</tt>.  Specify <tt>true</tt> to have the column widths re-proportioned
     * at <b>all times</b>.  The {@link Ext.grid.Column#width initially configured width}</tt> of each
     * column will be adjusted to fit the grid width and prevent horizontal scrolling. If columns are
     * later resized (manually or programmatically), the other columns in the grid <b>will</b> be resized
     * to fit the grid width. See <tt>{@link #autoFill}</tt> also.
     */
    forceFit : false,

    /**
     * @cfg {Array} sortClasses The CSS classes applied to a header when it is sorted. (defaults to <tt>['sort-asc', 'sort-desc']</tt>)
     */
    sortClasses : ['sort-asc', 'sort-desc'],

    /**
     * @cfg {String} sortAscText The text displayed in the 'Sort Ascending' menu item (defaults to <tt>'Sort Ascending'</tt>)
     */
    sortAscText : 'Sort Ascending',

    /**
     * @cfg {String} sortDescText The text displayed in the 'Sort Descending' menu item (defaults to <tt>'Sort Descending'</tt>)
     */
    sortDescText : 'Sort Descending',

    /**
     * @cfg {String} columnsText The text displayed in the 'Columns' menu item (defaults to <tt>'Columns'</tt>)
     */
    columnsText : 'Columns',

    /**
     * @cfg {String} selectedRowClass The CSS class applied to a selected row (defaults to <tt>'x-grid3-row-selected'</tt>). An
     * example overriding the default styling:
    <pre><code>
    .x-grid3-row-selected {background-color: yellow;}
    </code></pre>
     * Note that this only controls the row, and will not do anything for the text inside it.  To style inner
     * facets (like text) use something like:
    <pre><code>
    .x-grid3-row-selected .x-grid3-cell-inner {
        color: #FFCC00;
    }
    </code></pre>
     * @type String
     */
    selectedRowClass : 'x-grid3-row-selected',

    // private
    borderWidth : 2,
    tdClass : 'x-grid3-cell',
    hdCls : 'x-grid3-hd',
    markDirty : true,

    /**
     * @cfg {Number} cellSelectorDepth The number of levels to search for cells in event delegation (defaults to <tt>4</tt>)
     */
    cellSelectorDepth : 4,
    /**
     * @cfg {Number} rowSelectorDepth The number of levels to search for rows in event delegation (defaults to <tt>10</tt>)
     */
    rowSelectorDepth : 10,

    /**
     * @cfg {Number} rowBodySelectorDepth The number of levels to search for row bodies in event delegation (defaults to <tt>10</tt>)
     */
    rowBodySelectorDepth : 10,

    /**
     * @cfg {String} cellSelector The selector used to find cells internally (defaults to <tt>'td.x-grid3-cell'</tt>)
     */
    cellSelector : 'td.x-grid3-cell',
    /**
     * @cfg {String} rowSelector The selector used to find rows internally (defaults to <tt>'div.x-grid3-row'</tt>)
     */
    rowSelector : 'div.x-grid3-row',

    /**
     * @cfg {String} rowBodySelector The selector used to find row bodies internally (defaults to <tt>'div.x-grid3-row'</tt>)
     */
    rowBodySelector : 'div.x-grid3-row-body',

    // private
    firstRowCls: 'x-grid3-row-first',
    lastRowCls: 'x-grid3-row-last',
    rowClsRe: /(?:^|\s+)x-grid3-row-(first|last|alt)(?:\s+|$)/g,

    constructor : function(config){
        Ext.apply(this, config);
        // These events are only used internally by the grid components
        this.addEvents(
            /**
             * @event beforerowremoved
             * Internal UI Event. Fired before a row is removed.
             * @param {Ext.grid.GridView} view
             * @param {Number} rowIndex The index of the row to be removed.
             * @param {Ext.data.Record} record The Record to be removed
             */
            'beforerowremoved',
            /**
             * @event beforerowsinserted
             * Internal UI Event. Fired before rows are inserted.
             * @param {Ext.grid.GridView} view
             * @param {Number} firstRow The index of the first row to be inserted.
             * @param {Number} lastRow The index of the last row to be inserted.
             */
            'beforerowsinserted',
            /**
             * @event beforerefresh
             * Internal UI Event. Fired before the view is refreshed.
             * @param {Ext.grid.GridView} view
             */
            'beforerefresh',
            /**
             * @event rowremoved
             * Internal UI Event. Fired after a row is removed.
             * @param {Ext.grid.GridView} view
             * @param {Number} rowIndex The index of the row that was removed.
             * @param {Ext.data.Record} record The Record that was removed
             */
            'rowremoved',
            /**
             * @event rowsinserted
             * Internal UI Event. Fired after rows are inserted.
             * @param {Ext.grid.GridView} view
             * @param {Number} firstRow The index of the first inserted.
             * @param {Number} lastRow The index of the last row inserted.
             */
            'rowsinserted',
            /**
             * @event rowupdated
             * Internal UI Event. Fired after a row has been updated.
             * @param {Ext.grid.GridView} view
             * @param {Number} firstRow The index of the row updated.
             * @param {Ext.data.record} record The Record backing the row updated.
             */
            'rowupdated',
            /**
             * @event refresh
             * Internal UI Event. Fired after the GridView's body has been refreshed.
             * @param {Ext.grid.GridView} view
             */
            'refresh'
        );
        Ext.grid.GridView.superclass.constructor.call(this);
    },

    /* -------------------------------- UI Specific ----------------------------- */

    // private
    initTemplates : function(){
        var ts = this.templates || {};
        if(!ts.master){
            ts.master = new Ext.Template(
                '<div class="x-grid3" hidefocus="true">',
                    '<div class="x-grid3-viewport">',
                        '<div class="x-grid3-header"><div class="x-grid3-header-inner"><div class="x-grid3-header-offset" style="{ostyle}">{header}</div></div><div class="x-clear"></div></div>',
                        '<div class="x-grid3-scroller"><div class="x-grid3-body" style="{bstyle}">{body}</div><a href="#" class="x-grid3-focus" tabIndex="-1"></a></div>',
                    '</div>',
                    '<div class="x-grid3-resize-marker">&#160;</div>',
                    '<div class="x-grid3-resize-proxy">&#160;</div>',
                '</div>'
            );
        }

        if(!ts.header){
            ts.header = new Ext.Template(
                '<table border="0" cellspacing="0" cellpadding="0" style="{tstyle}">',
                '<thead><tr class="x-grid3-hd-row">{cells}</tr></thead>',
                '</table>'
            );
        }

        if(!ts.hcell){
            ts.hcell = new Ext.Template(
                '<td class="x-grid3-hd x-grid3-cell x-grid3-td-{id} {css}" style="{style}"><div {tooltip} {attr} class="x-grid3-hd-inner x-grid3-hd-{id}" unselectable="on" style="{istyle}">', this.grid.enableHdMenu ? '<a class="x-grid3-hd-btn" href="#"></a>' : '',
                '{value}<img class="x-grid3-sort-icon" src="', Ext.BLANK_IMAGE_URL, '" />',
                '</div></td>'
            );
        }

        if(!ts.body){
            ts.body = new Ext.Template('{rows}');
        }

        if(!ts.row){
            ts.row = new Ext.Template(
                '<div class="x-grid3-row {alt}" style="{tstyle}"><table class="x-grid3-row-table" border="0" cellspacing="0" cellpadding="0" style="{tstyle}">',
                '<tbody><tr>{cells}</tr>',
                (this.enableRowBody ? '<tr class="x-grid3-row-body-tr" style="{bodyStyle}"><td colspan="{cols}" class="x-grid3-body-cell" tabIndex="0" hidefocus="on"><div class="x-grid3-row-body">{body}</div></td></tr>' : ''),
                '</tbody></table></div>'
            );
        }

        if(!ts.cell){
            ts.cell = new Ext.Template(
                    '<td class="x-grid3-col x-grid3-cell x-grid3-td-{id} {css}" style="{style}" tabIndex="0" {cellAttr}>',
                    '<div class="x-grid3-cell-inner x-grid3-col-{id}" unselectable="on" {attr}>{value}</div>',
                    '</td>'
                    );
        }

        for(var k in ts){
            var t = ts[k];
            if(t && Ext.isFunction(t.compile) && !t.compiled){
                t.disableFormats = true;
                t.compile();
            }
        }

        this.templates = ts;
        this.colRe = new RegExp('x-grid3-td-([^\\s]+)', '');
    },

    // private
    fly : function(el){
        if(!this._flyweight){
            this._flyweight = new Ext.Element.Flyweight(document.body);
        }
        this._flyweight.dom = el;
        return this._flyweight;
    },

    // private
    getEditorParent : function(){
        return this.scroller.dom;
    },

    // private
    initElements : function(){
        var E = Ext.Element;

        var el = this.grid.getGridEl().dom.firstChild;
        var cs = el.childNodes;

        this.el = new E(el);

        this.mainWrap = new E(cs[0]);
        this.mainHd = new E(this.mainWrap.dom.firstChild);

        if(this.grid.hideHeaders){
            this.mainHd.setDisplayed(false);
        }

        this.innerHd = this.mainHd.dom.firstChild;
        this.scroller = new E(this.mainWrap.dom.childNodes[1]);
        if(this.forceFit){
            this.scroller.setStyle('overflow-x', 'hidden');
        }
        /**
         * <i>Read-only</i>. The GridView's body Element which encapsulates all rows in the Grid.
         * This {@link Ext.Element Element} is only available after the GridPanel has been rendered.
         * @type Ext.Element
         * @property mainBody
         */
        this.mainBody = new E(this.scroller.dom.firstChild);

        this.focusEl = new E(this.scroller.dom.childNodes[1]);
        this.focusEl.swallowEvent('click', true);

        this.resizeMarker = new E(cs[1]);
        this.resizeProxy = new E(cs[2]);
    },

    // private
    getRows : function(){
        return this.hasRows() ? this.mainBody.dom.childNodes : [];
    },

    // finder methods, used with delegation

    // private
    findCell : function(el){
        if(!el){
            return false;
        }
        return this.fly(el).findParent(this.cellSelector, this.cellSelectorDepth);
    },

    /**
     * <p>Return the index of the grid column which contains the passed HTMLElement.</p>
     * See also {@link #findRowIndex}
     * @param {HTMLElement} el The target element
     * @return {Number} The column index, or <b>false</b> if the target element is not within a row of this GridView.
     */
    findCellIndex : function(el, requiredCls){
        var cell = this.findCell(el);
        if(cell && (!requiredCls || this.fly(cell).hasClass(requiredCls))){
            return this.getCellIndex(cell);
        }
        return false;
    },

    // private
    getCellIndex : function(el){
        if(el){
            var m = el.className.match(this.colRe);
            if(m && m[1]){
                return this.cm.getIndexById(m[1]);
            }
        }
        return false;
    },

    // private
    findHeaderCell : function(el){
        var cell = this.findCell(el);
        return cell && this.fly(cell).hasClass(this.hdCls) ? cell : null;
    },

    // private
    findHeaderIndex : function(el){
        return this.findCellIndex(el, this.hdCls);
    },

    /**
     * Return the HtmlElement representing the grid row which contains the passed element.
     * @param {HTMLElement} el The target HTMLElement
     * @return {HTMLElement} The row element, or null if the target element is not within a row of this GridView.
     */
    findRow : function(el){
        if(!el){
            return false;
        }
        return this.fly(el).findParent(this.rowSelector, this.rowSelectorDepth);
    },

    /**
     * <p>Return the index of the grid row which contains the passed HTMLElement.</p>
     * See also {@link #findCellIndex}
     * @param {HTMLElement} el The target HTMLElement
     * @return {Number} The row index, or <b>false</b> if the target element is not within a row of this GridView.
     */
    findRowIndex : function(el){
        var r = this.findRow(el);
        return r ? r.rowIndex : false;
    },

    /**
     * Return the HtmlElement representing the grid row body which contains the passed element.
     * @param {HTMLElement} el The target HTMLElement
     * @return {HTMLElement} The row body element, or null if the target element is not within a row body of this GridView.
     */
    findRowBody : function(el){
        if(!el){
            return false;
        }
        return this.fly(el).findParent(this.rowBodySelector, this.rowBodySelectorDepth);
    },

    // getter methods for fetching elements dynamically in the grid

    /**
     * Return the <tt>&lt;div></tt> HtmlElement which represents a Grid row for the specified index.
     * @param {Number} index The row index
     * @return {HtmlElement} The div element.
     */
    getRow : function(row){
        return this.getRows()[row];
    },

    /**
     * Returns the grid's <tt>&lt;td></tt> HtmlElement at the specified coordinates.
     * @param {Number} row The row index in which to find the cell.
     * @param {Number} col The column index of the cell.
     * @return {HtmlElement} The td at the specified coordinates.
     */
    getCell : function(row, col){
        return this.getRow(row).getElementsByTagName('td')[col];
    },

    /**
     * Return the <tt>&lt;td></tt> HtmlElement which represents the Grid's header cell for the specified column index.
     * @param {Number} index The column index
     * @return {HtmlElement} The td element.
     */
    getHeaderCell : function(index){
        return this.mainHd.dom.getElementsByTagName('td')[index];
    },

    // manipulating elements

    // private - use getRowClass to apply custom row classes
    addRowClass : function(row, cls){
        var r = this.getRow(row);
        if(r){
            this.fly(r).addClass(cls);
        }
    },

    // private
    removeRowClass : function(row, cls){
        var r = this.getRow(row);
        if(r){
            this.fly(r).removeClass(cls);
        }
    },

    // private
    removeRow : function(row){
        Ext.removeNode(this.getRow(row));
        this.syncFocusEl(row);
    },

    // private
    removeRows : function(firstRow, lastRow){
        var bd = this.mainBody.dom;
        for(var rowIndex = firstRow; rowIndex <= lastRow; rowIndex++){
            Ext.removeNode(bd.childNodes[firstRow]);
        }
        this.syncFocusEl(firstRow);
    },

    // scrolling stuff

    // private
    getScrollState : function(){
        var sb = this.scroller.dom;
        return {left: sb.scrollLeft, top: sb.scrollTop};
    },

    // private
    restoreScroll : function(state){
        var sb = this.scroller.dom;
        sb.scrollLeft = state.left;
        sb.scrollTop = state.top;
    },

    /**
     * Scrolls the grid to the top
     */
    scrollToTop : function(){
        this.scroller.dom.scrollTop = 0;
        this.scroller.dom.scrollLeft = 0;
    },

    // private
    syncScroll : function(){
        this.syncHeaderScroll();
        var mb = this.scroller.dom;
        this.grid.fireEvent('bodyscroll', mb.scrollLeft, mb.scrollTop);
    },

    // private
    syncHeaderScroll : function(){
        var mb = this.scroller.dom;
        this.innerHd.scrollLeft = mb.scrollLeft;
        this.innerHd.scrollLeft = mb.scrollLeft; // second time for IE (1/2 time first fails, other browsers ignore)
    },

    // private
    updateSortIcon : function(col, dir){
        var sc = this.sortClasses;
        var hds = this.mainHd.select('td').removeClass(sc);
        hds.item(col).addClass(sc[dir == 'DESC' ? 1 : 0]);
    },

    // private
    updateAllColumnWidths : function(){
        var tw   = this.getTotalWidth(),
            clen = this.cm.getColumnCount(),
            ws   = [],
            len,
            i;

        for(i = 0; i < clen; i++){
            ws[i] = this.getColumnWidth(i);
        }

        this.innerHd.firstChild.style.width = this.getOffsetWidth();
        this.innerHd.firstChild.firstChild.style.width = tw;
        this.mainBody.dom.style.width = tw;

        for(i = 0; i < clen; i++){
            var hd = this.getHeaderCell(i);
            hd.style.width = ws[i];
        }

        var ns = this.getRows(), row, trow;
        for(i = 0, len = ns.length; i < len; i++){
            row = ns[i];
            row.style.width = tw;
            if(row.firstChild){
                row.firstChild.style.width = tw;
                trow = row.firstChild.rows[0];
                for (var j = 0; j < clen; j++) {
                   trow.childNodes[j].style.width = ws[j];
                }
            }
        }

        this.onAllColumnWidthsUpdated(ws, tw);
    },

    // private
    updateColumnWidth : function(col, width){
        var w = this.getColumnWidth(col);
        var tw = this.getTotalWidth();
        this.innerHd.firstChild.style.width = this.getOffsetWidth();
        this.innerHd.firstChild.firstChild.style.width = tw;
        this.mainBody.dom.style.width = tw;
        var hd = this.getHeaderCell(col);
        hd.style.width = w;

        var ns = this.getRows(), row;
        for(var i = 0, len = ns.length; i < len; i++){
            row = ns[i];
            row.style.width = tw;
            if(row.firstChild){
                row.firstChild.style.width = tw;
                row.firstChild.rows[0].childNodes[col].style.width = w;
            }
        }

        this.onColumnWidthUpdated(col, w, tw);
    },

    // private
    updateColumnHidden : function(col, hidden){
        var tw = this.getTotalWidth();
        this.innerHd.firstChild.style.width = this.getOffsetWidth();
        this.innerHd.firstChild.firstChild.style.width = tw;
        this.mainBody.dom.style.width = tw;
        var display = hidden ? 'none' : '';

        var hd = this.getHeaderCell(col);
        hd.style.display = display;

        var ns = this.getRows(), row;
        for(var i = 0, len = ns.length; i < len; i++){
            row = ns[i];
            row.style.width = tw;
            if(row.firstChild){
                row.firstChild.style.width = tw;
                row.firstChild.rows[0].childNodes[col].style.display = display;
            }
        }

        this.onColumnHiddenUpdated(col, hidden, tw);
        delete this.lastViewWidth; // force recalc
        this.layout();
    },

    /**
     * @private
     * Renders all of the rows to a string buffer and returns the string. This is called internally
     * by renderRows and performs the actual string building for the rows - it does not inject HTML into the DOM.
     * @param {Array} columns The column data acquired from getColumnData.
     * @param {Array} records The array of records to render
     * @param {Ext.data.Store} store The store to render the rows from
     * @param {Number} startRow The index of the first row being rendered. Sometimes we only render a subset of
     * the rows so this is used to maintain logic for striping etc
     * @param {Number} colCount The total number of columns in the column model
     * @param {Boolean} stripe True to stripe the rows
     * @return {String} A string containing the HTML for the rendered rows
     */
    doRender : function(columns, records, store, startRow, colCount, stripe) {
        var templates    = this.templates,
            cellTemplate = templates.cell,
            rowTemplate  = templates.row,
            last         = colCount - 1;

        var tstyle = 'width:' + this.getTotalWidth() + ';';

        // buffers
        var rowBuffer = [],
            colBuffer = [],
            rowParams = {tstyle: tstyle},
            meta      = {},
            column,
            record;

        //build up each row's HTML
        for (var j = 0, len = records.length; j < len; j++) {
            record    = records[j];
            colBuffer = [];

            var rowIndex = j + startRow;

            //build up each column's HTML
            for (var i = 0; i < colCount; i++) {
                column = columns[i];

                meta.id    = column.id;
                meta.css   = i === 0 ? 'x-grid3-cell-first ' : (i == last ? 'x-grid3-cell-last ' : '');
                meta.attr  = meta.cellAttr = '';
                meta.style = column.style;
                meta.value = column.renderer.call(column.scope, record.data[column.name], meta, record, rowIndex, i, store);

                if (Ext.isEmpty(meta.value)) {
                    meta.value = '&#160;';
                }

                if (this.markDirty && record.dirty && Ext.isDefined(record.modified[column.name])) {
                    meta.css += ' x-grid3-dirty-cell';
                }

                colBuffer[colBuffer.length] = cellTemplate.apply(meta);
            }

            //set up row striping and row dirtiness CSS classes
            var alt = [];

            if (stripe && ((rowIndex + 1) % 2 === 0)) {
                alt[0] = 'x-grid3-row-alt';
            }

            if (record.dirty) {
                alt[1] = ' x-grid3-dirty-row';
            }

            rowParams.cols = colCount;

            if (this.getRowClass) {
                alt[2] = this.getRowClass(record, rowIndex, rowParams, store);
            }

            rowParams.alt   = alt.join(' ');
            rowParams.cells = colBuffer.join('');

            rowBuffer[rowBuffer.length] = rowTemplate.apply(rowParams);
        }

        return rowBuffer.join('');
    },

    // private
    processRows : function(startRow, skipStripe) {
        if (!this.ds || this.ds.getCount() < 1) {
            return;
        }

        var rows = this.getRows(),
            len  = rows.length,
            i, r;

        skipStripe = skipStripe || !this.grid.stripeRows;
        startRow   = startRow   || 0;

        for (i = 0; i<len; i++) {
            r = rows[i];
            if (r) {
                r.rowIndex = i;
                if (!skipStripe) {
                    r.className = r.className.replace(this.rowClsRe, ' ');
                    if ((i + 1) % 2 === 0){
                        r.className += ' x-grid3-row-alt';
                    }
                }
            }
        }

        // add first/last-row classes
        if (startRow === 0) {
            Ext.fly(rows[0]).addClass(this.firstRowCls);
        }

        Ext.fly(rows[rows.length - 1]).addClass(this.lastRowCls);
    },

    afterRender : function(){
        if(!this.ds || !this.cm){
            return;
        }
        this.mainBody.dom.innerHTML = this.renderRows() || '&#160;';
        this.processRows(0, true);

        if(this.deferEmptyText !== true){
            this.applyEmptyText();
        }
        this.grid.fireEvent('viewready', this.grid);
    },

    /**
     * @private
     * Renders each of the UI elements in turn. This is called internally, once, by this.render. It does not
     * render rows from the store, just the surrounding UI elements. It also sets up listeners on the UI elements
     * and sets up options like column menus, moving and resizing.
     */
    renderUI : function() {
        var templates = this.templates,
            header    = this.renderHeaders(),
            body      = templates.body.apply({rows:'&#160;'});

        var html = templates.master.apply({
            body  : body,
            header: header,
            ostyle: 'width:' + this.getOffsetWidth() + ';',
            bstyle: 'width:' + this.getTotalWidth()  + ';'
        });

        var g = this.grid;

        g.getGridEl().dom.innerHTML = html;

        this.initElements();

        // get mousedowns early
        Ext.fly(this.innerHd).on('click', this.handleHdDown, this);

        this.mainHd.on({
            scope    : this,
            mouseover: this.handleHdOver,
            mouseout : this.handleHdOut,
            mousemove: this.handleHdMove
        });

        this.scroller.on('scroll', this.syncScroll,  this);
        if (g.enableColumnResize !== false) {
            this.splitZone = new Ext.grid.GridView.SplitDragZone(g, this.mainHd.dom);
        }

        if (g.enableColumnMove) {
            this.columnDrag = new Ext.grid.GridView.ColumnDragZone(g, this.innerHd);
            this.columnDrop = new Ext.grid.HeaderDropZone(g, this.mainHd.dom);
        }

        if (g.enableHdMenu !== false) {
            this.hmenu = new Ext.menu.Menu({id: g.id + '-hctx'});
            this.hmenu.add(
                {itemId:'asc',  text: this.sortAscText,  cls: 'xg-hmenu-sort-asc'},
                {itemId:'desc', text: this.sortDescText, cls: 'xg-hmenu-sort-desc'}
            );

            if (g.enableColumnHide !== false) {
                this.colMenu = new Ext.menu.Menu({id:g.id + '-hcols-menu'});
                this.colMenu.on({
                    scope     : this,
                    beforeshow: this.beforeColMenuShow,
                    itemclick : this.handleHdMenuClick
                });
                this.hmenu.add('-', {
                    itemId:'columns',
                    hideOnClick: false,
                    text: this.columnsText,
                    menu: this.colMenu,
                    iconCls: 'x-cols-icon'
                });
            }

            this.hmenu.on('itemclick', this.handleHdMenuClick, this);
        }

        if (g.trackMouseOver) {
            this.mainBody.on({
                scope    : this,
                mouseover: this.onRowOver,
                mouseout : this.onRowOut
            });
        }

        if (g.enableDragDrop || g.enableDrag) {
            this.dragZone = new Ext.grid.GridDragZone(g, {
                ddGroup : g.ddGroup || 'GridDD'
            });
        }

        this.updateHeaderSortState();
    },

    // private
    processEvent : function(name, e) {
        var t = e.getTarget(),
            g = this.grid,
            header = this.findHeaderIndex(t);
        g.fireEvent(name, e);
        if (header !== false) {
            g.fireEvent('header' + name, g, header, e);
        } else {
            var row = this.findRowIndex(t),
                cell,
                body;
            if (row !== false) {
                g.fireEvent('row' + name, g, row, e);
                cell = this.findCellIndex(t);
                if (cell !== false) {
                    g.fireEvent('cell' + name, g, row, cell, e);
                } else {
                    body = this.findRowBody(t);
                    if (body) {
                        g.fireEvent('rowbody' + name, g, row, e);
                    }
                }
            } else {
                g.fireEvent('container' + name, g, e);
            }
        }
    },

    // private
    layout : function() {
        if(!this.mainBody){
            return; // not rendered
        }
        var g = this.grid;
        var c = g.getGridEl();
        var csize = c.getSize(true);
        var vw = csize.width;

        if(!g.hideHeaders && (vw < 20 || csize.height < 20)){ // display: none?
            return;
        }

        if(g.autoHeight){
            this.scroller.dom.style.overflow = 'visible';
            if(Ext.isWebKit){
                this.scroller.dom.style.position = 'static';
            }
        }else{
            this.el.setSize(csize.width, csize.height);

            var hdHeight = this.mainHd.getHeight();
            var vh = csize.height - (hdHeight);

            this.scroller.setSize(vw, vh);
            if(this.innerHd){
                this.innerHd.style.width = (vw)+'px';
            }
        }
        if(this.forceFit){
            if(this.lastViewWidth != vw){
                this.fitColumns(false, false);
                this.lastViewWidth = vw;
            }
        }else {
            this.autoExpand();
            this.syncHeaderScroll();
        }
        this.onLayout(vw, vh);
    },

    // template functions for subclasses and plugins
    // these functions include precalculated values
    onLayout : function(vw, vh){
        // do nothing
    },

    onColumnWidthUpdated : function(col, w, tw){
        //template method
    },

    onAllColumnWidthsUpdated : function(ws, tw){
        //template method
    },

    onColumnHiddenUpdated : function(col, hidden, tw){
        // template method
    },

    updateColumnText : function(col, text){
        // template method
    },

    afterMove : function(colIndex){
        // template method
    },

    /* ----------------------------------- Core Specific -------------------------------------------*/
    // private
    init : function(grid){
        this.grid = grid;

        this.initTemplates();
        this.initData(grid.store, grid.colModel);
        this.initUI(grid);
    },

    // private
    getColumnId : function(index){
      return this.cm.getColumnId(index);
    },

    // private
    getOffsetWidth : function() {
        return (this.cm.getTotalWidth() + this.getScrollOffset()) + 'px';
    },

    getScrollOffset: function(){
        return Ext.num(this.scrollOffset, Ext.getScrollBarWidth());
    },

    /**
     * @private
     * Renders the header row using the 'header' template. Does not inject the HTML into the DOM, just
     * returns a string.
     * @return {String} Rendered header row
     */
    renderHeaders : function() {
        var cm   = this.cm,
            ts   = this.templates,
            ct   = ts.hcell,
            cb   = [],
            p    = {},
            len  = cm.getColumnCount(),
            last = len - 1;

        for (var i = 0; i < len; i++) {
            p.id = cm.getColumnId(i);
            p.value = cm.getColumnHeader(i) || '';
            p.style = this.getColumnStyle(i, true);
            p.tooltip = this.getColumnTooltip(i);
            p.css = i === 0 ? 'x-grid3-cell-first ' : (i == last ? 'x-grid3-cell-last ' : '');

            if (cm.config[i].align == 'right') {
                p.istyle = 'padding-right:16px';
            } else {
                delete p.istyle;
            }
            cb[cb.length] = ct.apply(p);
        }
        return ts.header.apply({cells: cb.join(''), tstyle:'width:'+this.getTotalWidth()+';'});
    },

    // private
    getColumnTooltip : function(i){
        var tt = this.cm.getColumnTooltip(i);
        if(tt){
            if(Ext.QuickTips.isEnabled()){
                return 'ext:qtip="'+tt+'"';
            }else{
                return 'title="'+tt+'"';
            }
        }
        return '';
    },

    // private
    beforeUpdate : function(){
        this.grid.stopEditing(true);
    },

    // private
    updateHeaders : function(){
        this.innerHd.firstChild.innerHTML = this.renderHeaders();
        this.innerHd.firstChild.style.width = this.getOffsetWidth();
        this.innerHd.firstChild.firstChild.style.width = this.getTotalWidth();
    },

    /**
     * Focuses the specified row.
     * @param {Number} row The row index
     */
    focusRow : function(row){
        this.focusCell(row, 0, false);
    },

    /**
     * Focuses the specified cell.
     * @param {Number} row The row index
     * @param {Number} col The column index
     */
    focusCell : function(row, col, hscroll){
        this.syncFocusEl(this.ensureVisible(row, col, hscroll));
        if(Ext.isGecko){
            this.focusEl.focus();
        }else{
            this.focusEl.focus.defer(1, this.focusEl);
        }
    },

    resolveCell : function(row, col, hscroll){
        if(!Ext.isNumber(row)){
            row = row.rowIndex;
        }
        if(!this.ds){
            return null;
        }
        if(row < 0 || row >= this.ds.getCount()){
            return null;
        }
        col = (col !== undefined ? col : 0);

        var rowEl = this.getRow(row),
            cm = this.cm,
            colCount = cm.getColumnCount(),
            cellEl;
        if(!(hscroll === false && col === 0)){
            while(col < colCount && cm.isHidden(col)){
                col++;
            }
            cellEl = this.getCell(row, col);
        }

        return {row: rowEl, cell: cellEl};
    },

    getResolvedXY : function(resolved){
        if(!resolved){
            return null;
        }
        var s = this.scroller.dom, c = resolved.cell, r = resolved.row;
        return c ? Ext.fly(c).getXY() : [this.el.getX(), Ext.fly(r).getY()];
    },

    syncFocusEl : function(row, col, hscroll){
        var xy = row;
        if(!Ext.isArray(xy)){
            row = Math.min(row, Math.max(0, this.getRows().length-1));
            if (isNaN(row)) {
                return;
            }
            xy = this.getResolvedXY(this.resolveCell(row, col, hscroll));
        }
        this.focusEl.setXY(xy||this.scroller.getXY());
    },

    ensureVisible : function(row, col, hscroll){
        var resolved = this.resolveCell(row, col, hscroll);
        if(!resolved || !resolved.row){
            return;
        }

        var rowEl = resolved.row,
            cellEl = resolved.cell,
            c = this.scroller.dom,
            ctop = 0,
            p = rowEl,
            stop = this.el.dom;

        while(p && p != stop){
            ctop += p.offsetTop;
            p = p.offsetParent;
        }

        ctop -= this.mainHd.dom.offsetHeight;
        stop = parseInt(c.scrollTop, 10);

        var cbot = ctop + rowEl.offsetHeight,
            ch = c.clientHeight,
            sbot = stop + ch;


        if(ctop < stop){
          c.scrollTop = ctop;
        }else if(cbot > sbot){
            c.scrollTop = cbot-ch;
        }

        if(hscroll !== false){
            var cleft = parseInt(cellEl.offsetLeft, 10);
            var cright = cleft + cellEl.offsetWidth;

            var sleft = parseInt(c.scrollLeft, 10);
            var sright = sleft + c.clientWidth;
            if(cleft < sleft){
                c.scrollLeft = cleft;
            }else if(cright > sright){
                c.scrollLeft = cright-c.clientWidth;
            }
        }
        return this.getResolvedXY(resolved);
    },

    // private
    insertRows : function(dm, firstRow, lastRow, isUpdate) {
        var last = dm.getCount() - 1;
        if( !isUpdate && firstRow === 0 && lastRow >= last) {
            this.fireEvent('beforerowsinserted', this, firstRow, lastRow);
                this.refresh();
            this.fireEvent('rowsinserted', this, firstRow, lastRow);
        } else {
            if (!isUpdate) {
                this.fireEvent('beforerowsinserted', this, firstRow, lastRow);
            }
            var html = this.renderRows(firstRow, lastRow),
                before = this.getRow(firstRow);
            if (before) {
                if(firstRow === 0){
                    Ext.fly(this.getRow(0)).removeClass(this.firstRowCls);
                }
                Ext.DomHelper.insertHtml('beforeBegin', before, html);
            } else {
                var r = this.getRow(last - 1);
                if(r){
                    Ext.fly(r).removeClass(this.lastRowCls);
                }
                Ext.DomHelper.insertHtml('beforeEnd', this.mainBody.dom, html);
            }
            if (!isUpdate) {
                this.fireEvent('rowsinserted', this, firstRow, lastRow);
                this.processRows(firstRow);
            } else if (firstRow === 0 || firstRow >= last) {
                //ensure first/last row is kept after an update.
                Ext.fly(this.getRow(firstRow)).addClass(firstRow === 0 ? this.firstRowCls : this.lastRowCls);
            }
        }
        this.syncFocusEl(firstRow);
    },

    // private
    deleteRows : function(dm, firstRow, lastRow){
        if(dm.getRowCount()<1){
            this.refresh();
        }else{
            this.fireEvent('beforerowsdeleted', this, firstRow, lastRow);

            this.removeRows(firstRow, lastRow);

            this.processRows(firstRow);
            this.fireEvent('rowsdeleted', this, firstRow, lastRow);
        }
    },

    // private
    getColumnStyle : function(col, isHeader){
        var style = !isHeader ? (this.cm.config[col].css || '') : '';
        style += 'width:'+this.getColumnWidth(col)+';';
        if(this.cm.isHidden(col)){
            style += 'display:none;';
        }
        var align = this.cm.config[col].align;
        if(align){
            style += 'text-align:'+align+';';
        }
        return style;
    },

    // private
    getColumnWidth : function(col){
        var w = this.cm.getColumnWidth(col);
        if(Ext.isNumber(w)){
            return (Ext.isBorderBox || (Ext.isWebKit && !Ext.isSafari2) ? w : (w - this.borderWidth > 0 ? w - this.borderWidth : 0)) + 'px';
        }
        return w;
    },

    // private
    getTotalWidth : function(){
        return this.cm.getTotalWidth()+'px';
    },

    // private
    fitColumns : function(preventRefresh, onlyExpand, omitColumn){
        var cm = this.cm, i;
        var tw = cm.getTotalWidth(false);
        var aw = this.grid.getGridEl().getWidth(true)-this.getScrollOffset();

        if(aw < 20){ // not initialized, so don't screw up the default widths
            return;
        }
        var extra = aw - tw;

        if(extra === 0){
            return false;
        }

        var vc = cm.getColumnCount(true);
        var ac = vc-(Ext.isNumber(omitColumn) ? 1 : 0);
        if(ac === 0){
            ac = 1;
            omitColumn = undefined;
        }
        var colCount = cm.getColumnCount();
        var cols = [];
        var extraCol = 0;
        var width = 0;
        var w;
        for (i = 0; i < colCount; i++){
            if(!cm.isHidden(i) && !cm.isFixed(i) && i !== omitColumn){
                w = cm.getColumnWidth(i);
                cols.push(i);
                extraCol = i;
                cols.push(w);
                width += w;
            }
        }
        var frac = (aw - cm.getTotalWidth())/width;
        while (cols.length){
            w = cols.pop();
            i = cols.pop();
            cm.setColumnWidth(i, Math.max(this.grid.minColumnWidth, Math.floor(w + w*frac)), true);
        }

        if((tw = cm.getTotalWidth(false)) > aw){
            var adjustCol = ac != vc ? omitColumn : extraCol;
             cm.setColumnWidth(adjustCol, Math.max(1,
                     cm.getColumnWidth(adjustCol)- (tw-aw)), true);
        }

        if(preventRefresh !== true){
            this.updateAllColumnWidths();
        }


        return true;
    },

    // private
    autoExpand : function(preventUpdate){
        var g = this.grid, cm = this.cm;
        if(!this.userResized && g.autoExpandColumn){
            var tw = cm.getTotalWidth(false);
            var aw = this.grid.getGridEl().getWidth(true)-this.getScrollOffset();
            if(tw != aw){
                var ci = cm.getIndexById(g.autoExpandColumn);
                var currentWidth = cm.getColumnWidth(ci);
                var cw = Math.min(Math.max(((aw-tw)+currentWidth), g.autoExpandMin), g.autoExpandMax);
                if(cw != currentWidth){
                    cm.setColumnWidth(ci, cw, true);
                    if(preventUpdate !== true){
                        this.updateColumnWidth(ci, cw);
                    }
                }
            }
        }
    },

    /**
     * @private
     * Returns an array of column configurations - one for each column
     * @return {Array} Array of column config objects. This includes the column name, renderer, id style and renderer
     */
    getColumnData : function(){
        // build a map for all the columns
        var cs       = [],
            cm       = this.cm,
            colCount = cm.getColumnCount();

        for (var i = 0; i < colCount; i++) {
            var name = cm.getDataIndex(i);

            cs[i] = {
                name    : (!Ext.isDefined(name) ? this.ds.fields.get(i).name : name),
                renderer: cm.getRenderer(i),
                scope   : cm.getRendererScope(i),
                id      : cm.getColumnId(i),
                style   : this.getColumnStyle(i)
            };
        }

        return cs;
    },

    // private
    renderRows : function(startRow, endRow){
        // pull in all the crap needed to render rows
        var g = this.grid, cm = g.colModel, ds = g.store, stripe = g.stripeRows;
        var colCount = cm.getColumnCount();

        if(ds.getCount() < 1){
            return '';
        }

        var cs = this.getColumnData();

        startRow = startRow || 0;
        endRow = !Ext.isDefined(endRow) ? ds.getCount()-1 : endRow;

        // records to render
        var rs = ds.getRange(startRow, endRow);

        return this.doRender(cs, rs, ds, startRow, colCount, stripe);
    },

    // private
    renderBody : function(){
        var markup = this.renderRows() || '&#160;';
        return this.templates.body.apply({rows: markup});
    },

    // private
    refreshRow : function(record){
        var ds = this.ds, index;
        if(Ext.isNumber(record)){
            index = record;
            record = ds.getAt(index);
            if(!record){
                return;
            }
        }else{
            index = ds.indexOf(record);
            if(index < 0){
                return;
            }
        }
        this.insertRows(ds, index, index, true);
        this.getRow(index).rowIndex = index;
        this.onRemove(ds, record, index+1, true);
        this.fireEvent('rowupdated', this, index, record);
    },

    /**
     * Refreshs the grid UI
     * @param {Boolean} headersToo (optional) True to also refresh the headers
     */
    refresh : function(headersToo){
        this.fireEvent('beforerefresh', this);
        this.grid.stopEditing(true);

        var result = this.renderBody();
        this.mainBody.update(result).setWidth(this.getTotalWidth());
        if(headersToo === true){
            this.updateHeaders();
            this.updateHeaderSortState();
        }
        this.processRows(0, true);
        this.layout();
        this.applyEmptyText();
        this.fireEvent('refresh', this);
    },

    /**
     * @private
     * Displays the configured emptyText if there are currently no rows to display
     */
    applyEmptyText : function(){
        if(this.emptyText && !this.hasRows()){
            this.mainBody.update('<div class="x-grid-empty">' + this.emptyText + '</div>');
        }
    },

    /**
     * @private
     * Adds sorting classes to the column headers based on the bound store's sortInfo. Fires the 'sortchange' event
     * if the sorting has changed since this function was last run.
     */
    updateHeaderSortState : function(){
        var state = this.ds.getSortState();
        if (!state) {
            return;
        }

        if (!this.sortState || (this.sortState.field != state.field || this.sortState.direction != state.direction)) {
            this.grid.fireEvent('sortchange', this.grid, state);
        }

        this.sortState = state;

        var sortColumn = this.cm.findColumnIndex(state.field);
        if (sortColumn != -1){
            var sortDir = state.direction;
            this.updateSortIcon(sortColumn, sortDir);
        }
    },

    /**
     * @private
     * Removes any sorting indicator classes from the column headers
     */
    clearHeaderSortState : function(){
        if (!this.sortState) {
            return;
        }
        this.grid.fireEvent('sortchange', this.grid, null);
        this.mainHd.select('td').removeClass(this.sortClasses);
        delete this.sortState;
    },

    // private
    destroy : function(){
        if (this.scrollToTopTask && this.scrollToTopTask.cancel){
            this.scrollToTopTask.cancel();
        }
        if(this.colMenu){
            Ext.menu.MenuMgr.unregister(this.colMenu);
            this.colMenu.destroy();
            delete this.colMenu;
        }
        if(this.hmenu){
            Ext.menu.MenuMgr.unregister(this.hmenu);
            this.hmenu.destroy();
            delete this.hmenu;
        }

        this.initData(null, null);
        this.purgeListeners();
        Ext.fly(this.innerHd).un("click", this.handleHdDown, this);

        if(this.grid.enableColumnMove){
            Ext.destroy(
                this.columnDrag.el,
                this.columnDrag.proxy.ghost,
                this.columnDrag.proxy.el,
                this.columnDrop.el,
                this.columnDrop.proxyTop,
                this.columnDrop.proxyBottom,
                this.columnDrag.dragData.ddel,
                this.columnDrag.dragData.header
            );
            if (this.columnDrag.proxy.anim) {
                Ext.destroy(this.columnDrag.proxy.anim);
            }
            delete this.columnDrag.proxy.ghost;
            delete this.columnDrag.dragData.ddel;
            delete this.columnDrag.dragData.header;
            this.columnDrag.destroy();
            delete Ext.dd.DDM.locationCache[this.columnDrag.id];
            delete this.columnDrag._domRef;

            delete this.columnDrop.proxyTop;
            delete this.columnDrop.proxyBottom;
            this.columnDrop.destroy();
            delete Ext.dd.DDM.locationCache["gridHeader" + this.grid.getGridEl().id];
            delete this.columnDrop._domRef;
            delete Ext.dd.DDM.ids[this.columnDrop.ddGroup];
        }

        if (this.splitZone){ // enableColumnResize
            this.splitZone.destroy();
            delete this.splitZone._domRef;
            delete Ext.dd.DDM.ids["gridSplitters" + this.grid.getGridEl().id];
        }

        Ext.fly(this.innerHd).removeAllListeners();
        Ext.removeNode(this.innerHd);
        delete this.innerHd;

        Ext.destroy(
            this.el,
            this.mainWrap,
            this.mainHd,
            this.scroller,
            this.mainBody,
            this.focusEl,
            this.resizeMarker,
            this.resizeProxy,
            this.activeHdBtn,
            this.dragZone,
            this.splitZone,
            this._flyweight
        );

        delete this.grid.container;

        if(this.dragZone){
            this.dragZone.destroy();
        }

        Ext.dd.DDM.currentTarget = null;
        delete Ext.dd.DDM.locationCache[this.grid.getGridEl().id];

        Ext.EventManager.removeResizeListener(this.onWindowResize, this);
    },

    // private
    onDenyColumnHide : function(){

    },

    // private
    render : function(){
        if(this.autoFill){
            var ct = this.grid.ownerCt;
            if (ct && ct.getLayout()){
                ct.on('afterlayout', function(){
                    this.fitColumns(true, true);
                    this.updateHeaders();
                }, this, {single: true});
            }else{
                this.fitColumns(true, true);
            }
        }else if(this.forceFit){
            this.fitColumns(true, false);
        }else if(this.grid.autoExpandColumn){
            this.autoExpand(true);
        }

        this.renderUI();
    },

    /* --------------------------------- Model Events and Handlers --------------------------------*/
    // private
    initData : function(ds, cm){
        if(this.ds){
            this.ds.un('load', this.onLoad, this);
            this.ds.un('datachanged', this.onDataChange, this);
            this.ds.un('add', this.onAdd, this);
            this.ds.un('remove', this.onRemove, this);
            this.ds.un('update', this.onUpdate, this);
            this.ds.un('clear', this.onClear, this);
            if(this.ds !== ds && this.ds.autoDestroy){
                this.ds.destroy();
            }
        }
        if(ds){
            ds.on({
                scope: this,
                load: this.onLoad,
                datachanged: this.onDataChange,
                add: this.onAdd,
                remove: this.onRemove,
                update: this.onUpdate,
                clear: this.onClear
            });
        }
        this.ds = ds;

        if(this.cm){
            this.cm.un('configchange', this.onColConfigChange, this);
            this.cm.un('widthchange', this.onColWidthChange, this);
            this.cm.un('headerchange', this.onHeaderChange, this);
            this.cm.un('hiddenchange', this.onHiddenChange, this);
            this.cm.un('columnmoved', this.onColumnMove, this);
        }
        if(cm){
            delete this.lastViewWidth;
            cm.on({
                scope: this,
                configchange: this.onColConfigChange,
                widthchange: this.onColWidthChange,
                headerchange: this.onHeaderChange,
                hiddenchange: this.onHiddenChange,
                columnmoved: this.onColumnMove
            });
        }
        this.cm = cm;
    },

    // private
    onDataChange : function(){
        this.refresh();
        this.updateHeaderSortState();
        this.syncFocusEl(0);
    },

    // private
    onClear : function(){
        this.refresh();
        this.syncFocusEl(0);
    },

    // private
    onUpdate : function(ds, record){
        this.refreshRow(record);
    },

    // private
    onAdd : function(ds, records, index){
        this.insertRows(ds, index, index + (records.length-1));
    },

    // private
    onRemove : function(ds, record, index, isUpdate){
        if(isUpdate !== true){
            this.fireEvent('beforerowremoved', this, index, record);
        }
        this.removeRow(index);
        if(isUpdate !== true){
            this.processRows(index);
            this.applyEmptyText();
            this.fireEvent('rowremoved', this, index, record);
        }
    },

    // private
    onLoad : function(){
        if (Ext.isGecko){
            if (!this.scrollToTopTask) {
                this.scrollToTopTask = new Ext.util.DelayedTask(this.scrollToTop, this);
            }
            this.scrollToTopTask.delay(1);
        }else{
            this.scrollToTop();
        }
    },

    // private
    onColWidthChange : function(cm, col, width){
        this.updateColumnWidth(col, width);
    },

    // private
    onHeaderChange : function(cm, col, text){
        this.updateHeaders();
    },

    // private
    onHiddenChange : function(cm, col, hidden){
        this.updateColumnHidden(col, hidden);
    },

    // private
    onColumnMove : function(cm, oldIndex, newIndex){
        this.indexMap = null;
        var s = this.getScrollState();
        this.refresh(true);
        this.restoreScroll(s);
        this.afterMove(newIndex);
        this.grid.fireEvent('columnmove', oldIndex, newIndex);
    },

    // private
    onColConfigChange : function(){
        delete this.lastViewWidth;
        this.indexMap = null;
        this.refresh(true);
    },

    /* -------------------- UI Events and Handlers ------------------------------ */
    // private
    initUI : function(grid){
        grid.on('headerclick', this.onHeaderClick, this);
    },

    // private
    initEvents : function(){
    },

    // private
    onHeaderClick : function(g, index){
        if(this.headersDisabled || !this.cm.isSortable(index)){
            return;
        }
        g.stopEditing(true);
        g.store.sort(this.cm.getDataIndex(index));
    },

    // private
    onRowOver : function(e, t){
        var row;
        if((row = this.findRowIndex(t)) !== false){
            this.addRowClass(row, 'x-grid3-row-over');
        }
    },

    // private
    onRowOut : function(e, t){
        var row;
        if((row = this.findRowIndex(t)) !== false && !e.within(this.getRow(row), true)){
            this.removeRowClass(row, 'x-grid3-row-over');
        }
    },

    // private
    handleWheel : function(e){
        e.stopPropagation();
    },

    // private
    onRowSelect : function(row){
        this.addRowClass(row, this.selectedRowClass);
    },

    // private
    onRowDeselect : function(row){
        this.removeRowClass(row, this.selectedRowClass);
    },

    // private
    onCellSelect : function(row, col){
        var cell = this.getCell(row, col);
        if(cell){
            this.fly(cell).addClass('x-grid3-cell-selected');
        }
    },

    // private
    onCellDeselect : function(row, col){
        var cell = this.getCell(row, col);
        if(cell){
            this.fly(cell).removeClass('x-grid3-cell-selected');
        }
    },

    // private
    onColumnSplitterMoved : function(i, w){
        this.userResized = true;
        var cm = this.grid.colModel;
        cm.setColumnWidth(i, w, true);

        if(this.forceFit){
            this.fitColumns(true, false, i);
            this.updateAllColumnWidths();
        }else{
            this.updateColumnWidth(i, w);
            this.syncHeaderScroll();
        }

        this.grid.fireEvent('columnresize', i, w);
    },

    // private
    handleHdMenuClick : function(item){
        var index = this.hdCtxIndex,
            cm = this.cm,
            ds = this.ds,
            id = item.getItemId();
        switch(id){
            case 'asc':
                ds.sort(cm.getDataIndex(index), 'ASC');
                break;
            case 'desc':
                ds.sort(cm.getDataIndex(index), 'DESC');
                break;
            default:
                index = cm.getIndexById(id.substr(4));
                if(index != -1){
                    if(item.checked && cm.getColumnsBy(this.isHideableColumn, this).length <= 1){
                        this.onDenyColumnHide();
                        return false;
                    }
                    cm.setHidden(index, item.checked);
                }
        }
        return true;
    },

    // private
    isHideableColumn : function(c){
        return !c.hidden;
    },

    // private
    beforeColMenuShow : function(){
        var cm = this.cm,  colCount = cm.getColumnCount();
        this.colMenu.removeAll();
        for(var i = 0; i < colCount; i++){
            if(cm.config[i].hideable !== false){
                this.colMenu.add(new Ext.menu.CheckItem({
                    itemId: 'col-'+cm.getColumnId(i),
                    text: cm.getColumnHeader(i),
                    checked: !cm.isHidden(i),
                    hideOnClick:false,
                    disabled: cm.config[i].hideable === false
                }));
            }
        }
    },

    // private
    handleHdDown : function(e, t){
        if(Ext.fly(t).hasClass('x-grid3-hd-btn')){
            e.stopEvent();
            var hd = this.findHeaderCell(t);
            Ext.fly(hd).addClass('x-grid3-hd-menu-open');
            var index = this.getCellIndex(hd);
            this.hdCtxIndex = index;
            var ms = this.hmenu.items, cm = this.cm;
            ms.get('asc').setDisabled(!cm.isSortable(index));
            ms.get('desc').setDisabled(!cm.isSortable(index));
            this.hmenu.on('hide', function(){
                Ext.fly(hd).removeClass('x-grid3-hd-menu-open');
            }, this, {single:true});
            this.hmenu.show(t, 'tl-bl?');
        }
    },

    // private
    handleHdOver : function(e, t){
        var hd = this.findHeaderCell(t);
        if(hd && !this.headersDisabled){
            this.activeHdRef = t;
            this.activeHdIndex = this.getCellIndex(hd);
            var fly = this.fly(hd);
            this.activeHdRegion = fly.getRegion();
            if(!this.cm.isMenuDisabled(this.activeHdIndex)){
                fly.addClass('x-grid3-hd-over');
                this.activeHdBtn = fly.child('.x-grid3-hd-btn');
                if(this.activeHdBtn){
                    this.activeHdBtn.dom.style.height = (hd.firstChild.offsetHeight-1)+'px';
                }
            }
        }
    },

    // private
    handleHdMove : function(e, t){
        var hd = this.findHeaderCell(this.activeHdRef);
        if(hd && !this.headersDisabled){
            var hw = this.splitHandleWidth || 5,
                r = this.activeHdRegion,
                x = e.getPageX(),
                ss = hd.style,
                cur = '';
            if(this.grid.enableColumnResize !== false){
                if(x - r.left <= hw && this.cm.isResizable(this.activeHdIndex-1)){
                    cur = Ext.isAir ? 'move' : Ext.isWebKit ? 'e-resize' : 'col-resize'; // col-resize not always supported
                }else if(r.right - x <= (!this.activeHdBtn ? hw : 2) && this.cm.isResizable(this.activeHdIndex)){
                    cur = Ext.isAir ? 'move' : Ext.isWebKit ? 'w-resize' : 'col-resize';
                }
            }
            ss.cursor = cur;
        }
    },

    // private
    handleHdOut : function(e, t){
        var hd = this.findHeaderCell(t);
        if(hd && (!Ext.isIE || !e.within(hd, true))){
            this.activeHdRef = null;
            this.fly(hd).removeClass('x-grid3-hd-over');
            hd.style.cursor = '';
        }
    },

    // private
    hasRows : function(){
        var fc = this.mainBody.dom.firstChild;
        return fc && fc.nodeType == 1 && fc.className != 'x-grid-empty';
    },

    // back compat
    bind : function(d, c){
        this.initData(d, c);
    }
});


// private
// This is a support class used internally by the Grid components
Ext.grid.GridView.SplitDragZone = Ext.extend(Ext.dd.DDProxy, {
    
    constructor: function(grid, hd){
        this.grid = grid;
        this.view = grid.getView();
        this.marker = this.view.resizeMarker;
        this.proxy = this.view.resizeProxy;
        Ext.grid.GridView.SplitDragZone.superclass.constructor.call(this, hd,
            'gridSplitters' + this.grid.getGridEl().id, {
            dragElId : Ext.id(this.proxy.dom), resizeFrame:false
        });
        this.scroll = false;
        this.hw = this.view.splitHandleWidth || 5;
    },

    b4StartDrag : function(x, y){
        this.dragHeadersDisabled = this.view.headersDisabled;
        this.view.headersDisabled = true;
        var h = this.view.mainWrap.getHeight();
        this.marker.setHeight(h);
        this.marker.show();
        this.marker.alignTo(this.view.getHeaderCell(this.cellIndex), 'tl-tl', [-2, 0]);
        this.proxy.setHeight(h);
        var w = this.cm.getColumnWidth(this.cellIndex),
            minw = Math.max(w-this.grid.minColumnWidth, 0);
        this.resetConstraints();
        this.setXConstraint(minw, 1000);
        this.setYConstraint(0, 0);
        this.minX = x - minw;
        this.maxX = x + 1000;
        this.startPos = x;
        Ext.dd.DDProxy.prototype.b4StartDrag.call(this, x, y);
    },

    allowHeaderDrag : function(e){
        return true;
    },

    handleMouseDown : function(e){
        var t = this.view.findHeaderCell(e.getTarget());
        if(t && this.allowHeaderDrag(e)){
            var xy = this.view.fly(t).getXY(), 
                x = xy[0], 
                y = xy[1],
                exy = e.getXY(), ex = exy[0],
                w = t.offsetWidth, adjust = false;
                
            if((ex - x) <= this.hw){
                adjust = -1;
            }else if((x+w) - ex <= this.hw){
                adjust = 0;
            }
            if(adjust !== false){
                this.cm = this.grid.colModel;
                var ci = this.view.getCellIndex(t);
                if(adjust == -1){
                  if (ci + adjust < 0) {
                    return;
                  }
                    while(this.cm.isHidden(ci+adjust)){
                        --adjust;
                        if(ci+adjust < 0){
                            return;
                        }
                    }
                }
                this.cellIndex = ci+adjust;
                this.split = t.dom;
                if(this.cm.isResizable(this.cellIndex) && !this.cm.isFixed(this.cellIndex)){
                    Ext.grid.GridView.SplitDragZone.superclass.handleMouseDown.apply(this, arguments);
                }
            }else if(this.view.columnDrag){
                this.view.columnDrag.callHandleMouseDown(e);
            }
        }
    },

    endDrag : function(e){
        this.marker.hide();
        var v = this.view,
            endX = Math.max(this.minX, e.getPageX()),
            diff = endX - this.startPos,
            disabled = this.dragHeadersDisabled;
            
        v.onColumnSplitterMoved(this.cellIndex, this.cm.getColumnWidth(this.cellIndex)+diff);
        setTimeout(function(){
            v.headersDisabled = disabled;
        }, 50);
    },

    autoOffset : function(){
        this.setDelta(0,0);
    }
});
