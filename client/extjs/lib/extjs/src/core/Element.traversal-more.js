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
     * Creates a {@link Ext.CompositeElement} for child nodes based on the passed CSS selector (the selector should not contain an id).
     * @param {String} selector The CSS selector
     * @param {Boolean} unique (optional) True to create a unique Ext.Element for each child (defaults to false, which creates a single shared flyweight object)
     * @return {CompositeElement/CompositeElementLite} The composite element
     */
    select : function(selector, unique){
        return Ext.Element.select(selector, unique, this.dom);
    }
});