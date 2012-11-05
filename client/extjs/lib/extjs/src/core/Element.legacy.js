/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.Element
 */
Ext.Element.addMethods({
    /**
     * Measures the element's content height and updates height to match. Note: this function uses setTimeout so
     * the new height may not be available immediately.
     * @param {Boolean} animate (optional) Animate the transition (defaults to false)
     * @param {Float} duration (optional) Length of the animation in seconds (defaults to .35)
     * @param {Function} onComplete (optional) Function to call when animation completes
     * @param {String} easing (optional) Easing method to use (defaults to easeOut)
     * @return {Ext.Element} this
     */
    autoHeight : function(animate, duration, onComplete, easing){
        var oldHeight = this.getHeight();
        this.clip();
        this.setHeight(1); // force clipping
        setTimeout(function(){
            var height = parseInt(this.dom.scrollHeight, 10); // parseInt for Safari
            if(!animate){
                this.setHeight(height);
                this.unclip();
                if(typeof onComplete == "function"){
                    onComplete();
                }
            }else{
                this.setHeight(oldHeight); // restore original height
                this.setHeight(height, animate, duration, function(){
                    this.unclip();
                    if(typeof onComplete == "function") onComplete();
                }.createDelegate(this), easing);
            }
        }.createDelegate(this), 0);
        return this;
    }
});