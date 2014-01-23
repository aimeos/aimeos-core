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
 * Creates a window with 40 tabs, profiles initial rendering speed and resize rendering speed
 */

function addWindowPerformanceTest() {
        Ext.test.profiler.add({
        name      : 'AnchorLayout',
        skipVersions: ['2.2.0', '3.1.0'],
        iterations: 1000,
        newWindow : true,

        beforeAll : function() {
            this.previousBuffer = Ext.Container.bufferResize;
            Ext.Container.bufferResize = false;
        },

        afterAll : function() {
            Ext.Container.bufferResize = this.previousBuffer;
        },

        execute   : function() {


            this.win = new Ext.Viewport({
                layout: 'fit',
                items: [{
                    xtype: 'container',
                    layout: 'form',
                    items: [{
                        xtype: 'textfield',
                        width: 100
                    }, {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        anchor: '100%'
                    },
                    {
                        xtype: 'textfield',
                        width: 100
                    }]
                }]
            });

        },

        afterEach: function() {
          this.win.destroy();
        }
    });
/*
    Ext.test.profiler.add({
        name      : 'ColumnLayout',
        skipVersions: ['2.2.0'],
        iterations: 50,
        newWindow : true,

        beforeAll : function() {
            this.previousBuffer = Ext.Container.bufferResize;
            Ext.Container.bufferResize = false;
        },

        afterAll : function() {
            Ext.Container.bufferResize = this.previousBuffer;
        },

        execute   : function() {

            this.win = new Ext.Viewport({
                layout: 'fit',
                items: [{
                    xtype: 'container',
                    layout: 'column',
                    defaults: {
                        height: 30
                    },
                    items: [{
                        xtype: 'container',
                        width: 100
                    }, {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        columnWidth: .05
                    },
                    {
                        xtype: 'container',
                        width: 100
                    }]
                }]
            });

        },

        afterEach: function() {
          this.win.destroy();
        }
    });

    Ext.test.profiler.add({
        name      : 'HBoxLayout',
        skipVersions: ['2.2.0'],
        iterations: 50,
        newWindow : true,

        beforeAll : function() {
            this.previousBuffer = Ext.Container.bufferResize;
            Ext.Container.bufferResize = false;
        },

        afterAll : function() {
            Ext.Container.bufferResize = this.previousBuffer;
        },

        execute   : function() {

            this.win = new Ext.Viewport({
                layout: 'fit',
                items: [{
                    xtype: 'container',
                    layout: 'hbox',
                    layoutConfig: {
                        align: 'stretch'
                    },
                    items: [{
                        xtype: 'container',
                        width: 100
                    }, {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        flex: 1
                    },
                    {
                        xtype: 'container',
                        flex: 2
                    },
                    {
                        xtype: 'container',
                        flex: 3
                    },
                    {
                        xtype: 'container',
                        width: 100
                    }]
                }]
            });

      } ,

        afterEach: function() {
          this.win.destroy();
        }
    });
*/
};

if (typeof Ext != 'undefined' && typeof Ext.test != 'undefined') {
    addWindowPerformanceTest();
} else {
    Ext.onReady(addWindowPerformanceTest);
}
