/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.list.ColumnResizer
 * @extends Ext.util.Observable
 * <p>Supporting Class for Ext.list.ListView</p>
 * @constructor
 * @param {Object} config
 */
Ext.list.ColumnResizer = Ext.extend(Ext.util.Observable, {
    /**
     * @cfg {Number} minPct The minimum percentage to allot for any column (defaults to <tt>.05</tt>)
     */
    minPct: .05,

    constructor: function(config){
        Ext.apply(this, config);
        Ext.list.ColumnResizer.superclass.constructor.call(this);
    },
    init : function(listView){
        this.view = listView;
        listView.on('render', this.initEvents, this);
    },

    initEvents : function(view){
        view.mon(view.innerHd, 'mousemove', this.handleHdMove, this);
        this.tracker = new Ext.dd.DragTracker({
            onBeforeStart: this.onBeforeStart.createDelegate(this),
            onStart: this.onStart.createDelegate(this),
            onDrag: this.onDrag.createDelegate(this),
            onEnd: this.onEnd.createDelegate(this),
            tolerance: 3,
            autoStart: 300
        });
        this.tracker.initEl(view.innerHd);
        view.on('beforedestroy', this.tracker.destroy, this.tracker);
    },

    handleHdMove : function(e, t){
        var hw = 5,
            x = e.getPageX(),
            hd = e.getTarget('em', 3, true);
        if(hd){
            var r = hd.getRegion(),
                ss = hd.dom.style,
                pn = hd.dom.parentNode;

            if(x - r.left <= hw && pn != pn.parentNode.firstChild){
                this.activeHd = Ext.get(pn.previousSibling.firstChild);
                ss.cursor = Ext.isWebKit ? 'e-resize' : 'col-resize';
            } else if(r.right - x <= hw && pn != pn.parentNode.lastChild.previousSibling){
                this.activeHd = hd;
                ss.cursor = Ext.isWebKit ? 'w-resize' : 'col-resize';
            } else{
                delete this.activeHd;
                ss.cursor = '';
            }
        }
    },

    onBeforeStart : function(e){
        this.dragHd = this.activeHd;
        return !!this.dragHd;
    },

    onStart: function(e){
        this.view.disableHeaders = true;
        this.proxy = this.view.el.createChild({cls:'x-list-resizer'});
        this.proxy.setHeight(this.view.el.getHeight());

        var x = this.tracker.getXY()[0],
            w = this.view.innerHd.getWidth();

        this.hdX = this.dragHd.getX();
        this.hdIndex = this.view.findHeaderIndex(this.dragHd);

        this.proxy.setX(this.hdX);
        this.proxy.setWidth(x-this.hdX);

        this.minWidth = w*this.minPct;
        this.maxWidth = w - (this.minWidth*(this.view.columns.length-1-this.hdIndex));
    },

    onDrag: function(e){
        var cursorX = this.tracker.getXY()[0];
        this.proxy.setWidth((cursorX-this.hdX).constrain(this.minWidth, this.maxWidth));
    },

    onEnd: function(e){
        /* calculate desired width by measuring proxy and then remove it */
        var nw = this.proxy.getWidth();
        this.proxy.remove();

        var index = this.hdIndex,
            vw = this.view,
            cs = vw.columns,
            len = cs.length,
            w = this.view.innerHd.getWidth(),
            minPct = this.minPct * 100,
            pct = Math.ceil((nw * vw.maxWidth) / w),
            diff = (cs[index].width * 100) - pct,
            eachItem = Math.floor(diff / (len-1-index)),
            mod = diff - (eachItem * (len-1-index));

        for(var i = index+1; i < len; i++){
            var cw = (cs[i].width * 100) + eachItem,
                ncw = Math.max(minPct, cw);
            if(cw != ncw){
                mod += cw - ncw;
            }
            cs[i].width = ncw / 100;
        }
        cs[index].width = pct / 100;
        cs[index+1].width += (mod / 100);
        delete this.dragHd;
        vw.setHdWidths();
        vw.refresh();
        setTimeout(function(){
            vw.disableHeaders = false;
        }, 100);
    }
});

// Backwards compatibility alias
Ext.ListView.ColumnResizer = Ext.list.ColumnResizer;