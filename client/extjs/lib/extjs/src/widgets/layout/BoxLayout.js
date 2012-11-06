/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.layout.BoxLayout
 * @extends Ext.layout.ContainerLayout
 * <p>Base Class for HBoxLayout and VBoxLayout Classes. Generally it should not need to be used directly.</p>
 */
Ext.layout.BoxLayout = Ext.extend(Ext.layout.ContainerLayout, {
    /**
     * @cfg {Object} defaultMargins
     * <p>If the individual contained items do not have a <tt>margins</tt>
     * property specified, the default margins from this property will be
     * applied to each item.</p>
     * <br><p>This property may be specified as an object containing margins
     * to apply in the format:</p><pre><code>
{
    top: (top margin),
    right: (right margin),
    bottom: (bottom margin),
    left: (left margin)
}</code></pre>
     * <p>This property may also be specified as a string containing
     * space-separated, numeric margin values. The order of the sides associated
     * with each value matches the way CSS processes margin values:</p>
     * <div class="mdetail-params"><ul>
     * <li>If there is only one value, it applies to all sides.</li>
     * <li>If there are two values, the top and bottom borders are set to the
     * first value and the right and left are set to the second.</li>
     * <li>If there are three values, the top is set to the first value, the left
     * and right are set to the second, and the bottom is set to the third.</li>
     * <li>If there are four values, they apply to the top, right, bottom, and
     * left, respectively.</li>
     * </ul></div>
     * <p>Defaults to:</p><pre><code>
     * {top:0, right:0, bottom:0, left:0}
     * </code></pre>
     */
    defaultMargins : {left:0,top:0,right:0,bottom:0},
    /**
     * @cfg {String} padding
     * <p>Sets the padding to be applied to all child items managed by this layout.</p>
     * <p>This property must be specified as a string containing
     * space-separated, numeric padding values. The order of the sides associated
     * with each value matches the way CSS processes padding values:</p>
     * <div class="mdetail-params"><ul>
     * <li>If there is only one value, it applies to all sides.</li>
     * <li>If there are two values, the top and bottom borders are set to the
     * first value and the right and left are set to the second.</li>
     * <li>If there are three values, the top is set to the first value, the left
     * and right are set to the second, and the bottom is set to the third.</li>
     * <li>If there are four values, they apply to the top, right, bottom, and
     * left, respectively.</li>
     * </ul></div>
     * <p>Defaults to: <code>"0"</code></p>
     */
    padding : '0',
    // documented in subclasses
    pack : 'start',

    // private
    monitorResize : true,
    type: 'box',
    scrollOffset : 0,
    extraCls : 'x-box-item',
    targetCls : 'x-box-layout-ct',
    innerCls : 'x-box-inner',

    constructor : function(config){
        Ext.layout.BoxLayout.superclass.constructor.call(this, config);

        if (Ext.isString(this.defaultMargins)) {
            this.defaultMargins = this.parseMargins(this.defaultMargins);
        }
    },

    /**
     * @private
     * Runs the child box calculations and caches them in childBoxCache. Subclasses can used these cached values
     * when laying out
     */
    onLayout: function(container, target) {
        Ext.layout.BoxLayout.superclass.onLayout.call(this, container, target);

        var items = this.getVisibleItems(container),
            tSize = this.getLayoutTargetSize();

        /**
         * @private
         * @property layoutTargetLastSize
         * @type Object
         * Private cache of the last measured size of the layout target. This should never be used except by
         * BoxLayout subclasses during their onLayout run.
         */
        this.layoutTargetLastSize = tSize;

        /**
         * @private
         * @property childBoxCache
         * @type Array
         * Array of the last calculated height, width, top and left positions of each visible rendered component
         * within the Box layout.
         */
        this.childBoxCache = this.calculateChildBoxes(items, tSize);

        this.updateInnerCtSize(tSize, this.childBoxCache);
        this.updateChildBoxes(this.childBoxCache.boxes);

        // Putting a box layout into an overflowed container is NOT correct and will make a second layout pass necessary.
        this.handleTargetOverflow(tSize, container, target);
    },

    /**
     * Resizes and repositions each child component
     * @param {Array} boxes The box measurements
     */
    updateChildBoxes: function(boxes) {
        for (var i = 0, length = boxes.length; i < length; i++) {
            var box  = boxes[i],
                comp = box.component;

            if (box.dirtySize) {
                comp.setSize(box.width, box.height);
            }
            // Don't set positions to NaN
            if (isNaN(box.left) || isNaN(box.top)) {
                continue;
            }
            comp.setPosition(box.left, box.top);
        }
    },

    /**
     * @private
     * Called by onRender just before the child components are sized and positioned. This resizes the innerCt
     * to make sure all child items fit within it. We call this before sizing the children because if our child
     * items are larger than the previous innerCt size the browser will insert scrollbars and then remove them
     * again immediately afterwards, giving a performance hit.
     * Subclasses should provide an implementation.
     * @param {Object} currentSize The current height and width of the innerCt
     * @param {Array} calculations The new box calculations of all items to be laid out
     */
    updateInnerCtSize: Ext.emptyFn,

    /**
     * @private
     * This should be called after onLayout of any BoxLayout subclass. If the target's overflow is not set to 'hidden',
     * we need to lay out a second time because the scrollbars may have modified the height and width of the layout
     * target. Having a Box layout inside such a target is therefore not recommended.
     * @param {Object} previousTargetSize The size and height of the layout target before we just laid out
     * @param {Ext.Container} container The container
     * @param {Ext.Element} target The target element
     */
    handleTargetOverflow: function(previousTargetSize, container, target) {
        var overflow = target.getStyle('overflow');

        if (overflow && overflow != 'hidden' &&!this.adjustmentPass) {
            var newTargetSize = this.getLayoutTargetSize();
            if (newTargetSize.width != previousTargetSize.width || newTargetSize.height != previousTargetSize.height){
                this.adjustmentPass = true;
                this.onLayout(container, target);
            }
        }

        delete this.adjustmentPass;
    },

    // private
    isValidParent : function(c, target){
        return this.innerCt && c.getPositionEl().dom.parentNode == this.innerCt.dom;
    },

    /**
     * @private
     * Returns all items that are both rendered and visible
     * @return {Array} All matching items
     */
    getVisibleItems: function(ct) {
        var ct  = ct || this.container,
            t   = ct.getLayoutTarget(),
            cti = ct.items.items,
            len = cti.length,

            i, c, items = [];

        for (i = 0; i < len; i++) {
            if((c = cti[i]).rendered && this.isValidParent(c, t) && c.hidden !== true  && c.collapsed !== true){
                items.push(c);
            }
        }

        return items;
    },

    // private
    renderAll : function(ct, target){
        if(!this.innerCt){
            // the innerCt prevents wrapping and shuffling while
            // the container is resizing
            this.innerCt = target.createChild({cls:this.innerCls});
            this.padding = this.parseMargins(this.padding);
        }
        Ext.layout.BoxLayout.superclass.renderAll.call(this, ct, this.innerCt);
    },

    getLayoutTargetSize : function(){
        var target = this.container.getLayoutTarget(), ret;
        if (target) {
            ret = target.getViewSize();

            // IE in strict mode will return a width of 0 on the 1st pass of getViewSize.
            // Use getStyleSize to verify the 0 width, the adjustment pass will then work properly
            // with getViewSize
            if (Ext.isIE && Ext.isStrict && ret.width == 0){
                ret =  target.getStyleSize();
            }

            ret.width -= target.getPadding('lr');
            ret.height -= target.getPadding('tb');
        }
        return ret;
    },

    // private
    renderItem : function(c){
        if(Ext.isString(c.margins)){
            c.margins = this.parseMargins(c.margins);
        }else if(!c.margins){
            c.margins = this.defaultMargins;
        }
        Ext.layout.BoxLayout.superclass.renderItem.apply(this, arguments);
    }
});

/**
 * @class Ext.layout.VBoxLayout
 * @extends Ext.layout.BoxLayout
 * <p>A layout that arranges items vertically down a Container. This layout optionally divides available vertical
 * space between child items containing a numeric <code>flex</code> configuration.</p>
 * This layout may also be used to set the widths of child items by configuring it with the {@link #align} option.
 */
Ext.layout.VBoxLayout = Ext.extend(Ext.layout.BoxLayout, {
    /**
     * @cfg {String} align
     * Controls how the child items of the container are aligned. Acceptable configuration values for this
     * property are:
     * <div class="mdetail-params"><ul>
     * <li><b><tt>left</tt></b> : <b>Default</b><div class="sub-desc">child items are aligned horizontally
     * at the <b>left</b> side of the container</div></li>
     * <li><b><tt>center</tt></b> : <div class="sub-desc">child items are aligned horizontally at the
     * <b>mid-width</b> of the container</div></li>
     * <li><b><tt>stretch</tt></b> : <div class="sub-desc">child items are stretched horizontally to fill
     * the width of the container</div></li>
     * <li><b><tt>stretchmax</tt></b> : <div class="sub-desc">child items are stretched horizontally to
     * the size of the largest item.</div></li>
     * </ul></div>
     */
    align : 'left', // left, center, stretch, strechmax
    type: 'vbox',

    /**
     * @cfg {String} pack
     * Controls how the child items of the container are packed together. Acceptable configuration values
     * for this property are:
     * <div class="mdetail-params"><ul>
     * <li><b><tt>start</tt></b> : <b>Default</b><div class="sub-desc">child items are packed together at
     * <b>top</b> side of container</div></li>
     * <li><b><tt>center</tt></b> : <div class="sub-desc">child items are packed together at
     * <b>mid-height</b> of container</div></li>
     * <li><b><tt>end</tt></b> : <div class="sub-desc">child items are packed together at <b>bottom</b>
     * side of container</div></li>
     * </ul></div>
     */

    /**
     * @cfg {Number} flex
     * This configuation option is to be applied to <b>child <tt>items</tt></b> of the container managed
     * by this layout. Each child item with a <tt>flex</tt> property will be flexed <b>vertically</b>
     * according to each item's <b>relative</b> <tt>flex</tt> value compared to the sum of all items with
     * a <tt>flex</tt> value specified.  Any child items that have either a <tt>flex = 0</tt> or
     * <tt>flex = undefined</tt> will not be 'flexed' (the initial size will not be changed).
     */

    /**
     * @private
     * See parent documentation
     */
    updateInnerCtSize: function(tSize, calcs) {
        var innerCtHeight = tSize.height,
            innerCtWidth  = calcs.meta.maxWidth + this.padding.left + this.padding.right;

        if (this.align == 'stretch') {
            innerCtWidth = tSize.width;
        } else if (this.align == 'center') {
            innerCtWidth = Math.max(tSize.width, innerCtWidth);
        }

        //we set the innerCt size first because if our child items are larger than the previous innerCt size
        //the browser will insert scrollbars and then remove them again immediately afterwards
        this.innerCt.setSize(innerCtWidth || undefined, innerCtHeight || undefined);
    },

    /**
     * @private
     * Calculates the size and positioning of each item in the VBox. This iterates over all of the rendered,
     * visible items and returns a height, width, top and left for each, as well as a reference to each. Also
     * returns meta data such as maxHeight which are useful when resizing layout wrappers such as this.innerCt.
     * @param {Array} visibleItems The array of all rendered, visible items to be calculated for
     * @param {Object} targetSize Object containing target size and height
     * @return {Object} Object containing box measurements for each child, plus meta data
     */
    calculateChildBoxes: function(visibleItems, targetSize) {
        var visibleCount = visibleItems.length,

            padding      = this.padding,
            topOffset    = padding.top,
            leftOffset   = padding.left,
            paddingVert  = topOffset  + padding.bottom,
            paddingHoriz = leftOffset + padding.right,

            width        = targetSize.width - this.scrollOffset,
            height       = targetSize.height,
            availWidth   = Math.max(0, width - paddingHoriz),

            isStart      = this.pack == 'start',
            isCenter     = this.pack == 'center',
            isEnd        = this.pack == 'end',

            nonFlexHeight= 0,
            maxWidth     = 0,
            totalFlex    = 0,

            //used to cache the calculated size and position values for each child item
            boxes        = [],

            //used in the for loops below, just declared here for brevity
            child, childWidth, childHeight, childSize, childMargins, canLayout, i, calcs, flexedHeight, horizMargins, stretchWidth;

            //gather the total flex of all flexed items and the width taken up by fixed width items
            for (i = 0; i < visibleCount; i++) {
                child = visibleItems[i];
                childHeight = child.height;
                childWidth  = child.width;
                canLayout   = !child.hasLayout && Ext.isFunction(child.doLayout);


                // Static height (numeric) requires no calcs
                if (!Ext.isNumber(childHeight)) {

                    // flex and not 'auto' height
                    if (child.flex && !childHeight) {
                        totalFlex += child.flex;

                    // Not flexed or 'auto' height or undefined height
                    } else {
                        //Render and layout sub-containers without a flex or width defined, as otherwise we
                        //don't know how wide the sub-container should be and cannot calculate flexed widths
                        if (!childHeight && canLayout) {
                            child.doLayout();
                        }

                        childSize = child.getSize();
                        childWidth = childSize.width;
                        childHeight = childSize.height;
                    }
                }

                childMargins = child.margins;

                nonFlexHeight += (childHeight || 0) + childMargins.top + childMargins.bottom;

                // Max width for align - force layout of non-layed out subcontainers without a numeric width
                if (!Ext.isNumber(childWidth)) {
                    if (canLayout) {
                        child.doLayout();
                    }
                    childWidth = child.getWidth();
                }

                maxWidth = Math.max(maxWidth, childWidth + childMargins.left + childMargins.right);

                //cache the size of each child component
                boxes.push({
                    component: child,
                    height   : childHeight || undefined,
                    width    : childWidth || undefined
                });
            }

            //the height available to the flexed items
            var availableHeight = Math.max(0, (height - nonFlexHeight - paddingVert));

            if (isCenter) {
                topOffset += availableHeight / 2;
            } else if (isEnd) {
                topOffset += availableHeight;
            }

            //temporary variables used in the flex height calculations below
            var remainingHeight = availableHeight,
                remainingFlex   = totalFlex;

            //calculate the height of each flexed item, and the left + top positions of every item
            for (i = 0; i < visibleCount; i++) {
                child = visibleItems[i];
                calcs = boxes[i];

                childMargins = child.margins;
                horizMargins = childMargins.left + childMargins.right;

                topOffset   += childMargins.top;

                if (isStart && child.flex && !child.height) {
                    flexedHeight     = Math.ceil((child.flex / remainingFlex) * remainingHeight);
                    remainingHeight -= flexedHeight;
                    remainingFlex   -= child.flex;

                    calcs.height = flexedHeight;
                    calcs.dirtySize = true;
                }

                calcs.left = leftOffset + childMargins.left;
                calcs.top  = topOffset;

                switch (this.align) {
                    case 'stretch':
                        stretchWidth = availWidth - horizMargins;
                        calcs.width  = stretchWidth.constrain(child.minWidth || 0, child.maxWidth || 1000000);
                        calcs.dirtySize = true;
                        break;
                    case 'stretchmax':
                        stretchWidth = maxWidth - horizMargins;
                        calcs.width  = stretchWidth.constrain(child.minWidth || 0, child.maxWidth || 1000000);
                        calcs.dirtySize = true;
                        break;
                    case 'center':
                        var diff = availWidth - calcs.width - horizMargins;
                        if (diff > 0) {
                            calcs.left = leftOffset + horizMargins + (diff / 2);
                        }
                }

                topOffset += calcs.height + childMargins.bottom;
            }

        return {
            boxes: boxes,
            meta : {
                maxWidth: maxWidth
            }
        };
    }
});

Ext.Container.LAYOUTS.vbox = Ext.layout.VBoxLayout;

/**
 * @class Ext.layout.HBoxLayout
 * @extends Ext.layout.BoxLayout
 * <p>A layout that arranges items horizontally across a Container. This layout optionally divides available horizontal
 * space between child items containing a numeric <code>flex</code> configuration.</p>
 * This layout may also be used to set the heights of child items by configuring it with the {@link #align} option.
 */
Ext.layout.HBoxLayout = Ext.extend(Ext.layout.BoxLayout, {
    /**
     * @cfg {String} align
     * Controls how the child items of the container are aligned. Acceptable configuration values for this
     * property are:
     * <div class="mdetail-params"><ul>
     * <li><b><tt>top</tt></b> : <b>Default</b><div class="sub-desc">child items are aligned vertically
     * at the <b>top</b> of the container</div></li>
     * <li><b><tt>middle</tt></b> : <div class="sub-desc">child items are aligned vertically in the
     * <b>middle</b> of the container</div></li>
     * <li><b><tt>stretch</tt></b> : <div class="sub-desc">child items are stretched vertically to fill
     * the height of the container</div></li>
     * <li><b><tt>stretchmax</tt></b> : <div class="sub-desc">child items are stretched vertically to
     * the height of the largest item.</div></li>
     */
    align: 'top', // top, middle, stretch, strechmax

    type : 'hbox',

    /**
     * @private
     * See parent documentation
     */
    updateInnerCtSize: function(tSize, calcs) {
        var innerCtWidth  = tSize.width,
            innerCtHeight = calcs.meta.maxHeight + this.padding.top + this.padding.bottom;

        if (this.align == 'stretch') {
            innerCtHeight = tSize.height;
        } else if (this.align == 'middle') {
            innerCtHeight = Math.max(tSize.height, innerCtHeight);
        }

        this.innerCt.setSize(innerCtWidth || undefined, innerCtHeight || undefined);
    },

    /**
     * @cfg {String} pack
     * Controls how the child items of the container are packed together. Acceptable configuration values
     * for this property are:
     * <div class="mdetail-params"><ul>
     * <li><b><tt>start</tt></b> : <b>Default</b><div class="sub-desc">child items are packed together at
     * <b>left</b> side of container</div></li>
     * <li><b><tt>center</tt></b> : <div class="sub-desc">child items are packed together at
     * <b>mid-width</b> of container</div></li>
     * <li><b><tt>end</tt></b> : <div class="sub-desc">child items are packed together at <b>right</b>
     * side of container</div></li>
     * </ul></div>
     */
    /**
     * @cfg {Number} flex
     * This configuation option is to be applied to <b>child <tt>items</tt></b> of the container managed
     * by this layout. Each child item with a <tt>flex</tt> property will be flexed <b>horizontally</b>
     * according to each item's <b>relative</b> <tt>flex</tt> value compared to the sum of all items with
     * a <tt>flex</tt> value specified.  Any child items that have either a <tt>flex = 0</tt> or
     * <tt>flex = undefined</tt> will not be 'flexed' (the initial size will not be changed).
     */

    /**
     * @private
     * Calculates the size and positioning of each item in the HBox. This iterates over all of the rendered,
     * visible items and returns a height, width, top and left for each, as well as a reference to each. Also
     * returns meta data such as maxHeight which are useful when resizing layout wrappers such as this.innerCt.
     * @param {Array} visibleItems The array of all rendered, visible items to be calculated for
     * @param {Object} targetSize Object containing target size and height
     * @return {Object} Object containing box measurements for each child, plus meta data
     */
    calculateChildBoxes: function(visibleItems, targetSize) {
        var visibleCount = visibleItems.length,

            padding      = this.padding,
            topOffset    = padding.top,
            leftOffset   = padding.left,
            paddingVert  = topOffset  + padding.bottom,
            paddingHoriz = leftOffset + padding.right,

            width        = targetSize.width - this.scrollOffset,
            height       = targetSize.height,
            availHeight  = Math.max(0, height - paddingVert),

            isStart      = this.pack == 'start',
            isCenter     = this.pack == 'center',
            isEnd        = this.pack == 'end',
            // isRestore    = ['stretch', 'stretchmax'].indexOf(this.align) == -1,

            nonFlexWidth = 0,
            maxHeight    = 0,
            totalFlex    = 0,

            //used to cache the calculated size and position values for each child item
            boxes        = [],

            //used in the for loops below, just declared here for brevity
            child, childWidth, childHeight, childSize, childMargins, canLayout, i, calcs, flexedWidth, vertMargins, stretchHeight;

            //gather the total flex of all flexed items and the width taken up by fixed width items
            for (i = 0; i < visibleCount; i++) {
                child       = visibleItems[i];
                childHeight = child.height;
                childWidth  = child.width;
                canLayout   = !child.hasLayout && Ext.isFunction(child.doLayout);

                // Static width (numeric) requires no calcs
                if (!Ext.isNumber(childWidth)) {

                    // flex and not 'auto' width
                    if (child.flex && !childWidth) {
                        totalFlex += child.flex;

                    // Not flexed or 'auto' width or undefined width
                    } else {
                        //Render and layout sub-containers without a flex or width defined, as otherwise we
                        //don't know how wide the sub-container should be and cannot calculate flexed widths
                        if (!childWidth && canLayout) {
                            child.doLayout();
                        }

                        childSize   = child.getSize();
                        childWidth  = childSize.width;
                        childHeight = childSize.height;
                    }
                }

                childMargins = child.margins;

                nonFlexWidth += (childWidth || 0) + childMargins.left + childMargins.right;

                // Max height for align - force layout of non-layed out subcontainers without a numeric height
                if (!Ext.isNumber(childHeight)) {
                    if (canLayout) {
                        child.doLayout();
                    }
                    childHeight = child.getHeight();
                }

                maxHeight = Math.max(maxHeight, childHeight + childMargins.top + childMargins.bottom);

                //cache the size of each child component
                boxes.push({
                    component: child,
                    height   : childHeight || undefined,
                    width    : childWidth || undefined
                });
            }

            //the width available to the flexed items
            var availableWidth = Math.max(0, (width - nonFlexWidth - paddingHoriz));

            if (isCenter) {
                leftOffset += availableWidth / 2;
            } else if (isEnd) {
                leftOffset += availableWidth;
            }

            //temporary variables used in the flex width calculations below
            var remainingWidth = availableWidth,
                remainingFlex  = totalFlex;

            //calculate the widths of each flexed item, and the left + top positions of every item
            for (i = 0; i < visibleCount; i++) {
                child = visibleItems[i];
                calcs = boxes[i];

                childMargins = child.margins;
                vertMargins  = childMargins.top + childMargins.bottom;

                leftOffset  += childMargins.left;

                if (isStart && child.flex && !child.width) {
                    flexedWidth     = Math.ceil((child.flex / remainingFlex) * remainingWidth);
                    remainingWidth -= flexedWidth;
                    remainingFlex  -= child.flex;

                    calcs.width = flexedWidth;
                    calcs.dirtySize = true;
                }

                calcs.left = leftOffset;
                calcs.top  = topOffset + childMargins.top;

                switch (this.align) {
                    case 'stretch':
                        stretchHeight = availHeight - vertMargins;
                        calcs.height  = stretchHeight.constrain(child.minHeight || 0, child.maxHeight || 1000000);
                        calcs.dirtySize = true;
                        break;
                    case 'stretchmax':
                        stretchHeight = maxHeight - vertMargins;
                        calcs.height  = stretchHeight.constrain(child.minHeight || 0, child.maxHeight || 1000000);
                        calcs.dirtySize = true;
                        break;
                    case 'middle':
                        var diff = availHeight - calcs.height - vertMargins;
                        if (diff > 0) {
                            calcs.top = topOffset + vertMargins + (diff / 2);
                        }
                }
                leftOffset += calcs.width + childMargins.right;
            }

        return {
            boxes: boxes,
            meta : {
                maxHeight: maxHeight
            }
        };
    }
});

Ext.Container.LAYOUTS.hbox = Ext.layout.HBoxLayout;
