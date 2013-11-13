Ext.BLANK_IMAGE_URL = 'scripts/extjs/resources/images/default/s.gif';

Docs = {};

ApiPanel = function() {
    ApiPanel.superclass.constructor.call(this, {
        id: 'api-tree',
        region: 'west',
        split: true,
        width: 280,
        minSize: 175,
        maxSize: 500,
        collapsible: true,
        margins: '0 0 5 5',
        cmargins: '0 0 0 0',
        rootVisible: false,
        lines: false,
        autoScroll: true,
        animCollapse: false,
        animate: false,
        collapseMode: 'mini',
        loader: new Ext.tree.TreeLoader({
			preloadChildren: true,
			clearOnLoad: false
		}),
        root: new Ext.tree.AsyncTreeNode({
            text: 'API Documentation',
            id: 'root',
            expanded: true,
            children: [
				Docs.classData
			]
         }),
        collapseFirst: false
    });

    this.getSelectionModel().on('beforeselect', function(sm, node){
        return node.isLeaf();
    });
};

Ext.extend(ApiPanel, Ext.tree.TreePanel, {
    selectClass : function(id){
        if(id){
            this.selectPath(this.getNodeById(id).getPath());
        }
    }
});


DocPanel = Ext.extend(Ext.Panel, {
    closable: true,
    autoScroll:true,

    initComponent: function(){
        var ps     = this.title.split('_');
        this.title = ps[ps.length-1];

        DocPanel.superclass.initComponent.call(this);
    },

	scrollToSection: function(id){
		var el = Ext.getDom(id);
		if(el){
			var top = (Ext.fly(el).getOffsetsTo(this.body)[1]) + this.body.dom.scrollTop;
			this.body.scrollTo('top', top - 25, {
				duration: .5
			});
        }
	}
});


MainPanel = function(){
    MainPanel.superclass.constructor.call(this, {
        id:'doc-body',
        region:'center',
        margins:'0 5 5 0',
        resizeTabs: true,
        minTabWidth: 135,
        tabWidth: 135,
        plugins: new Ext.ux.TabCloseMenu(),
        enableTabScroll: true,
        activeTab: 0,

        items: {
            id:'welcome-panel',
            title: 'API Home',
            autoLoad: {
				url: 'welcome.html'
			},
            iconCls:'icon-docs',
            autoScroll: true,
			tbar: []
        }
    });
};

Ext.extend(MainPanel, Ext.TabPanel, {

    initEvents : function(){
        MainPanel.superclass.initEvents.call(this);
        this.body.on('click', this.onClick, this);
    },

    onClick: function(e, target){
        if(target = e.getTarget('a:not(.exi)', 3)){
			e.stopEvent();
			
			var internal      = target.href.substring(location.href.length, 
													target.href.length);
			var internalSplit = internal.split('#');
			
			var internalLink, internalNode;
			
			api.root.cascade(function(n){
				if(n.attributes.href == internalSplit[0]) {
					internalLink = internal;
					internalNode = n;
					return false;
				}
			});
		
            if(internalLink){
                var member = Ext.fly(target).getAttributeNS('ext', 'member');
                this.loadClass(internalNode.attributes.href, internalNode.attributes.id, internalNode.attributes.text);
				
				var anchor = internalSplit[1];
				var id     = Ext.DomQuery.selectNode('a[@name="' + anchor + '"]', Ext.getDom(this.getActiveTab().getEl()));
                this.getActiveTab().scrollToSection(id);
            }else if(target.className == 'inner-link' || internal.charAt(0) == '#'){
				var anchor = target.href.split('#')[1];
				var id     = Ext.DomQuery.selectNode('a[@name="' + anchor + '"]', Ext.getDom(this.getActiveTab().getEl()));
                this.getActiveTab().scrollToSection(id);
            }else{
                window.open(target.href);
            }
        }else if(target = e.getTarget('.micon', 2)){
            e.stopEvent();
            var tr = Ext.fly(target.parentNode);
            if(tr.hasClass('expandable')){
                tr.toggleClass('expanded');
            }
        }
    },

    loadClass : function(href, cls, title, member){
        var id = 'docs-' + cls;
        var tab = this.getComponent(id);
        if(tab){
            this.setActiveTab(tab);
            if(member){
                tab.scrollToMember(member);
            }
        }else{
            var autoLoad = {url: href};
            if(member){
                autoLoad.callback = function(){
                    Ext.getCmp(id).scrollToMember(member);
                }
            }
            var p = this.add(new DocPanel({
                id: id,
                cclass: cls,
				title: title,
                autoLoad: autoLoad,
                iconCls: Docs.icons[cls]
            }));
            this.setActiveTab(p);
        }
    }
});


Ext.onReady(function(){

    Ext.QuickTips.init();

    api = new ApiPanel();
    var mainPanel = new MainPanel();

    api.on('click', function(node, e){
         if(node.isLeaf()){
            e.stopEvent();
            mainPanel.loadClass(node.attributes.href, node.attributes.id, node.attributes.text);
         }
    });

    mainPanel.on('tabchange', function(tp, tab){
        api.selectClass(tab.cclass); 
    });

    var hd = new Ext.Panel({
        border: false,
        layout:'anchor',
        region:'north',
        cls: 'docs-header',
        height:60,
        items: [{
            xtype:'box',
            el:'header',
            border:false,
            anchor: 'none -25'
        },
        new Ext.Toolbar({
            cls:'top-toolbar',
            items:[ ' ',
			new Ext.form.TextField({
				width: 200,
				emptyText:'Filter',
				listeners:{
					render: function(f){
						f.el.on('keydown', filterTree, f, {buffer: 350});
					}
				}
			}), ' ', ' ',
			{
                iconCls: 'icon-expand-all',
				tooltip: 'Expand All',
                handler: function(){ api.root.expand(true); }
            }, '-', {
                iconCls: 'icon-collapse-all',
                tooltip: 'Collapse All',
                handler: function(){ api.root.collapse(true); }
            }]
        })]
    })

    var viewport = new Ext.Viewport({
        layout:'border',
        items:[ hd, api, mainPanel ]
    });

    api.expandPath('/root/apidocs');

    // allow for link in
    var page = window.location.href.split('?')[1];
    if(page){
        var ps = Ext.urlDecode(page);
        var cls = ps['class'];
        mainPanel.loadClass(cls + '.html', cls, ps.member);
    }
    
    viewport.doLayout();
	
	setTimeout(function(){
        Ext.get('loading').remove();
        Ext.get('loading-mask').fadeOut({
			remove:true
		});
    }, 250);
	
	var filter = new Ext.tree.TreeFilter(api, {
		clearBlank: true,
		autoClear: true
	});
	var hiddenPkgs = [];
	var markCount  = [];
	function filterTree(e){
		var text = e.target.value;
		Ext.each(hiddenPkgs, function(n){
			n.ui.show();
		});
		if(!text){
			filter.clear();
			markCount = [];
			return;
		}
		api.expandAll();
		
		var re = new RegExp('^(.*)' + Ext.escapeRe(text), 'i');
		filter.filterBy(function(n){
			var test = re.test(n.text);
			if (test) {
				markToRoot(n, api.root);
			}
			
			return !n.attributes.leaf || test;
		});
		
		// hide empty packages that weren't filtered
		api.root.cascade(function(n){
			if(!markCount[n.id] && n != api.root){
				n.ui.hide();
				hiddenPkgs.push(n);
			}
		});
	}
	
	function markToRoot(n, root) {
		if(markCount[n.id])
			return;
			
		markCount[n.id] = 1;
		
		if(n.parentNode != null)
			markToRoot(n.parentNode, root);
	}
});

Ext.Ajax.on('requestcomplete', function(ajax, xhr, o){
    if(typeof urchinTracker == 'function' && o && o.url){
        urchinTracker(o.url);
    }
});