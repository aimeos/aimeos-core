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
Ext.ns('Ext.ux');

Ext.ux.Tabs = Ext.extend(Ext.util.Observable, {
	// Configuration options
    activeTab: 0,
    
	// Our class constructor
    constructor : function(element, config) {
        Ext.apply(this, config);
        Ext.ux.Tabs.superclass.constructor.call(this);
        
        this.addEvents(
            'beforetabchange',
            'tabchange'
        );
        
        this.el = Ext.get(element);
        this.init();
    },
    
    init : function() {
        var me = this;

		this.el.addClass('ux-tabs-container');
		
		this.tabStrip = this.el.child('ul');
		this.tabStrip.addClass('ux-tabs-strip');
		
		this.tabStrip.on('click', this.onStripClick, this, {delegate: 'a'});
		
		this.tabs = this.tabStrip.select('> li');
		this.cards = this.el.select('> div');
		
		this.cardsContainer = this.el.createChild({
			cls: 'ux-tabs-cards'
		});		
		this.cardsContainer.setWidth(this.el.getWidth());
		
		this.cards.addClass('ux-tabs-card');
		this.cards.appendTo(this.cardsContainer);
		
		this.el.createChild({
			cls: 'ux-tabs-clearfix'
		});
		
		this.setActiveTab(this.activeTab || 0);
	},
	
	onStripClick : function(ev, t) {
		if(t && t.href && t.href.indexOf('#')) {
			ev.preventDefault();			
			this.setActiveTab(t.href.split('#')[1]);
		}
	},
	
	setActiveTab : function(tab) {
		var card;		
		if(Ext.isString(tab)) {
			card = Ext.get(tab);
			tab = this.tabStrip.child('a[href=#' + tab + ']').parent();
		}
		else if (Ext.isNumber(tab)) {
			tab = this.tabs.item(tab);
			card = Ext.get(tab.first().dom.href.split('#')[1]);
		}
		
		if(tab && card && this.fireEvent('beforetabchange', tab, card) !== false) {
			card.radioClass('ux-tabs-card-active');
			tab.radioClass('ux-tabs-tab-active');
			this.fireEvent('tabchange', tab, card);
		}
	}
});