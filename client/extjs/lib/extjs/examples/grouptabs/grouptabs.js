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
Ext.onReady(function() {
	Ext.QuickTips.init();
    
    // create some portlet tools using built in Ext tool ids
    var tools = [{
        id:'gear',
        handler: function(){
            Ext.Msg.alert('Message', 'The Settings tool was clicked.');
        }
    },{
        id:'close',
        handler: function(e, target, panel){
            panel.ownerCt.remove(panel, true);
        }
    }];

    var viewport = new Ext.Viewport({
        layout:'fit',
        items:[{
            xtype: 'grouptabpanel',
    		tabWidth: 130,
    		activeGroup: 0,
    		items: [{
    			mainItem: 1,
    			items: [{
    				title: 'Tickets',
                    layout: 'fit',
                    iconCls: 'x-icon-tickets',
                    tabTip: 'Tickets tabtip',
                    style: 'padding: 10px;',
    				items: [new SampleGrid([0,1,2,3,4])]
    			}, 
                {
                    xtype: 'portal',
                    title: 'Dashboard',
                    tabTip: 'Dashboard tabtip',
                    items:[{
                        columnWidth:.33,
                        style:'padding:10px 0 10px 10px',
                        items:[{
                            title: 'Grid in a Portlet',
                            layout:'fit',
                            tools: tools,
                            items: new SampleGrid([0, 2, 3])
                        },{
                            title: 'Another Panel 1',
                            tools: tools,
                            html: Ext.example.shortBogusMarkup
                        }]
                    },{
                        columnWidth:.33,
                        style:'padding:10px 0 10px 10px',
                        items:[{
                            title: 'Panel 2',
                            tools: tools,
                            html: Ext.example.shortBogusMarkup
                        },{
                            title: 'Another Panel 2',
                            tools: tools,
                            html: Ext.example.shortBogusMarkup
                        }]
                    },{
                        columnWidth:.33,
                        style:'padding:10px',
                        items:[{
                            title: 'Panel 3',
                            tools: tools,
                            html: Ext.example.shortBogusMarkup
                        },{
                            title: 'Another Panel 3',
                            tools: tools,
                            html: Ext.example.shortBogusMarkup
                        }]
                    }]                    
                }, {
    				title: 'Subscriptions',
                    iconCls: 'x-icon-subscriptions',
                    tabTip: 'Subscriptions tabtip',
                    style: 'padding: 10px;',
					layout: 'fit',
    				items: [{
						xtype: 'tabpanel',
						activeTab: 1,
						items: [{
							title: 'Nested Tabs',
							html: Ext.example.shortBogusMarkup
						}]	
					}]	
    			}, {
    				title: 'Users',
                    iconCls: 'x-icon-users',
                    tabTip: 'Users tabtip',
                    style: 'padding: 10px;',
    				html: Ext.example.shortBogusMarkup			
    			}]
            }, {
                expanded: true,
                items: [{
                    title: 'Configuration',
                    iconCls: 'x-icon-configuration',
                    tabTip: 'Configuration tabtip',
                    style: 'padding: 10px;',
                    html: Ext.example.shortBogusMarkup 
                }, {
                    title: 'Email Templates',
                    iconCls: 'x-icon-templates',
                    tabTip: 'Templates tabtip',
                    style: 'padding: 10px;',
                    html: Ext.example.shortBogusMarkup
                }]
            }]
		}]
    });
});
