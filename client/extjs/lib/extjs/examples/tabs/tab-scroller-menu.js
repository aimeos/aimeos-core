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
	// Create our instance of tabScrollerMenu
	var scrollerMenu = new Ext.ux.TabScrollerMenu({
		maxText  : 15,
		pageSize : 5
	});
	new Ext.Window({
		height : 200,
		width  : 400,
		layout : 'fit',
		title  : 'Exercising scrollable tabs with a tabscroller menu',
		items  : {
			xtype           : 'tabpanel',
			activeTab       : 0,
			id              : 'myTPanel',
			enableTabScroll : true,
			resizeTabs      : true,
			minTabWidth     : 75,
			border          : false,
			plugins         : [ scrollerMenu ],
			items           : [
				{
					title : 'our first tab'
				}
			]
		}
	}).show();
	
	// Add a bunch of tabs dynamically
	var tabLimit = 22;
	(function (num) {
		for (var i = 1; i <= tabLimit; i++) {
			var title = 'Tab # ' + i;
			Ext.getCmp('myTPanel').add({
				title    : title,
				html     : 'Hi, i am tab ' + i,
				tabTip   : title,
				closable : true
			});
		}
	}).defer(1000);

});