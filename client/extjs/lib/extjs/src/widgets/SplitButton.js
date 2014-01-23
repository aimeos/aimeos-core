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
 * @class Ext.SplitButton
 * @extends Ext.Button
 * A split button that provides a built-in dropdown arrow that can fire an event separately from the default
 * click event of the button.  Typically this would be used to display a dropdown menu that provides additional
 * options to the primary button action, but any custom handler can provide the arrowclick implementation.  Example usage:
 * <pre><code>
// display a dropdown menu:
new Ext.SplitButton({
	renderTo: 'button-ct', // the container id
   	text: 'Options',
   	handler: optionsHandler, // handle a click on the button itself
   	menu: new Ext.menu.Menu({
        items: [
        	// these items will render as dropdown menu items when the arrow is clicked:
	        {text: 'Item 1', handler: item1Handler},
	        {text: 'Item 2', handler: item2Handler}
        ]
   	})
});

// Instead of showing a menu, you provide any type of custom
// functionality you want when the dropdown arrow is clicked:
new Ext.SplitButton({
	renderTo: 'button-ct',
   	text: 'Options',
   	handler: optionsHandler,
   	arrowHandler: myCustomHandler
});
</code></pre>
 * @cfg {Function} arrowHandler A function called when the arrow button is clicked (can be used instead of click event)
 * @cfg {String} arrowTooltip The title attribute of the arrow
 * @constructor
 * Create a new menu button
 * @param {Object} config The config object
 * @xtype splitbutton
 */
Ext.SplitButton = Ext.extend(Ext.Button, {
	// private
    arrowSelector : 'em',
    split: true,

    // private
    initComponent : function(){
        Ext.SplitButton.superclass.initComponent.call(this);
        /**
         * @event arrowclick
         * Fires when this button's arrow is clicked
         * @param {MenuButton} this
         * @param {EventObject} e The click event
         */
        this.addEvents("arrowclick");
    },

    // private
    onRender : function(){
        Ext.SplitButton.superclass.onRender.apply(this, arguments);
        if(this.arrowTooltip){
            this.el.child(this.arrowSelector).dom[this.tooltipType] = this.arrowTooltip;
        }
    },

    /**
     * Sets this button's arrow click handler.
     * @param {Function} handler The function to call when the arrow is clicked
     * @param {Object} scope (optional) Scope for the function passed above
     */
    setArrowHandler : function(handler, scope){
        this.arrowHandler = handler;
        this.scope = scope;
    },

    getMenuClass : function(){
        return 'x-btn-split' + (this.arrowAlign == 'bottom' ? '-bottom' : '');
    },

    isClickOnArrow : function(e){
	if (this.arrowAlign != 'bottom') {
	    var visBtn = this.el.child('em.x-btn-split');
	    var right = visBtn.getRegion().right - visBtn.getPadding('r');
	    return e.getPageX() > right;
	} else {
	    return e.getPageY() > this.btnEl.getRegion().bottom;
	}
    },

    // private
    onClick : function(e, t){
        e.preventDefault();
        if(!this.disabled){
            if(this.isClickOnArrow(e)){
                if(this.menu && !this.menu.isVisible() && !this.ignoreNextClick){
                    this.showMenu();
                }
                this.fireEvent("arrowclick", this, e);
                if(this.arrowHandler){
                    this.arrowHandler.call(this.scope || this, this, e);
                }
            }else{
                this.doToggle();
                this.fireEvent("click", this, e);
                if(this.handler){
                    this.handler.call(this.scope || this, this, e);
                }
            }
        }
    },

    // private
    isMenuTriggerOver : function(e){
        return this.menu && e.target.tagName == this.arrowSelector;
    },

    // private
    isMenuTriggerOut : function(e, internal){
        return this.menu && e.target.tagName != this.arrowSelector;
    }
});

Ext.reg('splitbutton', Ext.SplitButton);