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

    // This function renders a block of buttons
    function renderButtons(title){

        Ext.getBody().createChild({tag: 'h2', html: title});

        new ButtonPanel(
            'Text Only',
            [{
                text: 'Add User'
            },{
                text: 'Add User',
                scale: 'medium'
            },{
                text: 'Add User',
                scale: 'large'
            }]
        );

        new ButtonPanel(
            'Icon Only',
            [{
                iconCls: 'add16'
            },{
                iconCls: 'add24',
                scale: 'medium'
            },{
                iconCls: 'add',
                scale: 'large'
            }]
        );

        new ButtonPanel(
            'Icon and Text (left)',
            [{
                text: 'Add User',
                iconCls: 'add16'
            },{
                text: 'Add User',
                iconCls: 'add24',
                scale: 'medium'
            },{
                text: 'Add User',
                iconCls: 'add',
                scale: 'large'
            }]
        );

        new ButtonPanel(
            'Icon and Text (top)',
            [{
                text: 'Add User',
                iconCls: 'add16',
                iconAlign: 'top'
            },{
                text: 'Add User',
                iconCls: 'add24',
                scale: 'medium',
                iconAlign: 'top'
            },{
                text: 'Add User',
                iconCls: 'add',
                scale: 'large',
                iconAlign: 'top'
            }]
        );

        new ButtonPanel(
            'Icon and Text (right)',
            [{
                text: 'Add User',
                iconCls: 'add16',
                iconAlign: 'right'
            },{
                text: 'Add User',
                iconCls: 'add24',
                scale: 'medium',
                iconAlign: 'right'
            },{
                text: 'Add User',
                iconCls: 'add',
                scale: 'large',
                iconAlign: 'right'
            }]
        );

        new ButtonPanel(
            'Icon and Text (bottom)',
            [{
                text: 'Add User',
                iconCls: 'add16',
                iconAlign: 'bottom'
            },{
                text: 'Add User',
                iconCls: 'add24',
                scale: 'medium',
                iconAlign: 'bottom'
            },{
                text: 'Add User',
                iconCls: 'add',
                scale: 'large',
                iconAlign: 'bottom'
            }]
        );
    }

    renderButtons('Normal Buttons');

    ButtonPanel.override({
        enableToggle: true
    });

    renderButtons('Toggle Buttons');

    ButtonPanel.override({
        enableToggle : undefined,
        menu : {items: [{text:'Menu Item 1'},{text:'Menu Item 2'},{text:'Menu Item 3'}]}
    });

    renderButtons('Menu Buttons');

    ButtonPanel.override({
        split: true,
        defaultType: 'splitbutton'
    });

    renderButtons('Split Buttons');

    ButtonPanel.override({
        split: false,
        defaultType: 'button',
        arrowAlign: 'bottom'
    });

    renderButtons('Menu Buttons (Arrow on bottom)');

    ButtonPanel.override({
        split: true,
        defaultType: 'splitbutton'
    });

    renderButtons('Split Buttons (Arrow on bottom)');
});

// Helper class for organizing the buttons
ButtonPanel = Ext.extend(Ext.Panel, {
    layout:'table',
    defaultType: 'button',
    baseCls: 'x-plain',
    cls: 'btn-panel',
    renderTo : 'docbody',
    menu: undefined,
    split: false,

    layoutConfig: {
        columns:3
    },

    constructor: function(desc, buttons){
        // apply test configs
        for(var i = 0, b; b = buttons[i]; i++){
            b.menu = this.menu;
            b.enableToggle = this.enableToggle;
            b.split = this.split;
            b.arrowAlign = this.arrowAlign;
        }
        var items = [{
            xtype: 'box',
            autoEl: {tag: 'h3', html: desc, style:"padding:15px 0 3px;"},
            colspan: 3
        }].concat(buttons);

        ButtonPanel.superclass.constructor.call(this, {
            items: items
        });
    }
});