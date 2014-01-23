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

    var spot = new Ext.ux.Spotlight({
        easing: 'easeOut',
        duration: .3
    });

    var DemoPanel = Ext.extend(Ext.Panel, {
        title: 'Demo Panel',
        frame: true,
        width: 200,
        height: 150,
        html: 'Some panel content goes here!',
        bodyStyle: 'padding:10px 15px;',

        toggle: function(on){
            this.buttons[0].setDisabled(!on);
        }
    });

    var p1, p2, p3;
    var updateSpot = function(id){
        if(typeof id == 'string'){
            spot.show(id);
        }else if (!id && spot.active){
            spot.hide();
        }
        p1.toggle(id==p1.id);
        p2.toggle(id==p2.id);
        p3.toggle(id==p3.id);
    };

    new Ext.Panel({
        renderTo: Ext.getBody(),
        layout: 'table',
        id: 'demo-ct',
        border: false,
        layoutConfig: {
            columns: 3
        },
        items: [p1 = new DemoPanel({
            id: 'panel1',
            buttons: [{
                text: 'Next Panel',
                handler: updateSpot.createDelegate(this, ['panel2'])
            }]
        }),
        p2 = new DemoPanel({
            id: 'panel2',
            buttons: [{
                text: 'Next Panel',
                handler: updateSpot.createDelegate(this, ['panel3'])
            }]
        }),
        p3 = new DemoPanel({
            id: 'panel3',
            buttons: [{
                text: 'Done',
                handler: updateSpot.createDelegate(this, [false])
            }]
        })]
    });

    new Ext.Button({
        text: 'Start',
        renderTo: 'start-ct',
        handler: updateSpot.createDelegate(this, ['panel1'])
    });

    updateSpot(false);
});