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
    Ext.QuickTips.init();

    // simple array store
    var store = new Ext.data.ArrayStore({
        fields: ['abbr', 'state', 'nick'],
        data : Ext.exampledata.states // from states.js
    });
    var combo = new Ext.form.ComboBox({
        store: store,
        displayField:'state',
        typeAhead: true,
        mode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        emptyText:'Select a state...',
        selectOnFocus:true,
        applyTo: 'local-states'
    });
    
    //Simple arrays can be used directly as the store config.  1-dimensional arrays
    //will automatically be expanded (each array item will be the combo value and text)
    //and for multi-dimensional arrays, the value in index 0 of each item will be assumed
    //to be the value, while the value at index 1 is assumed to be the text.  For example,
    //[['AL', 'Alabama'],['AK', 'Alaska'], etc.]. Any other values beyond index 1 within
    //each item will be ignored using this approach.
    var comboFromArray = new Ext.form.ComboBox({
        store: Ext.exampledata.states,
        typeAhead: true,
        forceSelection: true,
        triggerAction: 'all',
        emptyText:'Select a state...',
        selectOnFocus:true,
        applyTo: 'array-states'
    });

    var comboWithTooltip = new Ext.form.ComboBox({
        tpl: '<tpl for="."><div ext:qtip="{state}. {nick}" class="x-combo-list-item">{state}</div></tpl>',
        store: store,
        displayField:'state',
        typeAhead: true,
        forceSelection: true,
        mode: 'local',
        triggerAction: 'all',
        emptyText:'Select a state...',
        selectOnFocus:true,
        applyTo: 'local-states-with-qtip'
    });

    var converted = new Ext.form.ComboBox({
        typeAhead: true,
        triggerAction: 'all',
        transform:'state',
        width:135,
        forceSelection:true
    });
    
//  Create code view Panels. Ignore.
    new Ext.Panel({
    	contentEl: 'state-combo-code',
    	width: Ext.getBody().child('p').getWidth(),
    	title: 'View code to create this combo',
    	hideCollapseTool: true,
    	titleCollapse: true,
    	collapsible: true,
    	collapsed: true,
    	renderTo: 'state-combo-code-panel'
    });
    new Ext.Panel({
    	contentEl: 'state-combo-qtip-code',
    	autoScroll: true,
    	width: Ext.getBody().child('p').getWidth(),
    	title: 'View code to create this combo',
    	hideCollapseTool: true,
    	titleCollapse: true,
    	collapsible: true,
    	collapsed: true,
    	renderTo: 'state-combo-qtip-code-panel'
    });
    new Ext.Panel({
    	contentEl: 'array-combo-code',
    	autoScroll: true,
    	width: Ext.getBody().child('p').getWidth(),
    	title: 'View code to create this combo',
    	hideCollapseTool: true,
    	titleCollapse: true,
    	collapsible: true,
    	collapsed: true,
    	renderTo: 'array-combo-code-panel'
    });
    new Ext.Panel({
    	contentEl: 'transformed-combo-code',
    	autoScroll: true,
    	width: Ext.getBody().child('p').getWidth(),
    	title: 'View code to create this combo',
    	hideCollapseTool: true,
    	titleCollapse: true,
    	collapsible: true,
    	collapsed: true,
    	renderTo: 'transformed-combo-code-panel'
    });

});