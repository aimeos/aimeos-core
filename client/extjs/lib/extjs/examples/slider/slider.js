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
Ext.onReady(function(){

    new Ext.Slider({
        renderTo: 'basic-slider',
        width: 214,
        minValue: 0,
        maxValue: 100
    });

    new Ext.Slider({
        renderTo: 'increment-slider',
        width: 214,
        value:50,
        increment: 10,
        minValue: 0,
        maxValue: 100
    });

    new Ext.Slider({
        renderTo: 'vertical-slider',
        height: 214,
        vertical: true,
        minValue: 0,
        maxValue: 100
    });

    new Ext.Slider({
        renderTo: 'tip-slider',
        width: 214,
        minValue: 0,
        maxValue: 100,
        plugins: new Ext.slider.Tip()
    });

    var tip = new Ext.slider.Tip({
        getText: function(thumb){
            return String.format('<b>{0}% complete</b>', thumb.value);
        }
    });

    new Ext.Slider({
        renderTo: 'custom-tip-slider',
        width: 214,
        increment: 10,
        minValue: 0,
        maxValue: 100,
        plugins: tip
    });

    new Ext.Slider({
        renderTo: 'custom-slider',
        width: 214,
        increment: 10,
        minValue: 0,
        maxValue: 100,
        plugins: new Ext.slider.Tip()
    });
    
    new Ext.slider.MultiSlider({
        renderTo: 'multi-slider-horizontal',
        width   : 214,
        minValue: 0,
        maxValue: 100,
        values  : [10, 50, 90],
        plugins : new Ext.slider.Tip()
    });
    
    new Ext.slider.MultiSlider({
        renderTo : 'multi-slider-vertical',
        vertical : true,
        height   : 214,
        minValue: 0,
        maxValue: 100,
        values  : [10, 50, 90],
        plugins : new Ext.slider.Tip()
    });
});
