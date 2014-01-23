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
Ext.ux.PanelResizer = Ext.extend(Ext.util.Observable, {
    minHeight: 0,
    maxHeight:10000000,

    constructor: function(config){
        Ext.apply(this, config);
        this.events = {};
        Ext.ux.PanelResizer.superclass.constructor.call(this, config);
    },

    init : function(p){
        this.panel = p;

        if(this.panel.elements.indexOf('footer')==-1){
            p.elements += ',footer';
        }
        p.on('render', this.onRender, this);
    },

    onRender : function(p){
        this.handle = p.footer.createChild({cls:'x-panel-resize'});

        this.tracker = new Ext.dd.DragTracker({
            onStart: this.onDragStart.createDelegate(this),
            onDrag: this.onDrag.createDelegate(this),
            onEnd: this.onDragEnd.createDelegate(this),
            tolerance: 3,
            autoStart: 300
        });
        this.tracker.initEl(this.handle);
        p.on('beforedestroy', this.tracker.destroy, this.tracker);
    },

	// private
    onDragStart: function(e){
        this.dragging = true;
        this.startHeight = this.panel.el.getHeight();
        this.fireEvent('dragstart', this, e);
    },

	// private
    onDrag: function(e){
        this.panel.setHeight((this.startHeight-this.tracker.getOffset()[1]).constrain(this.minHeight, this.maxHeight));
        this.fireEvent('drag', this, e);
    },

	// private
    onDragEnd: function(e){
        this.dragging = false;
        this.fireEvent('dragend', this, e);
    }
});
Ext.preg('panelresizer', Ext.ux.PanelResizer);