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
 * @class Ext.menu.MenuMgr
 * Provides a common registry of all menu items on a page so that they can be easily accessed by id.
 * @singleton
 */
Ext.menu.MenuMgr = function(){
   var menus, 
       active, 
       map,
       groups = {}, 
       attached = false, 
       lastShow = new Date();
   

   // private - called when first menu is created
   function init(){
       menus = {};
       active = new Ext.util.MixedCollection();
       map = Ext.getDoc().addKeyListener(27, hideAll);
       map.disable();
   }

   // private
   function hideAll(){
       if(active && active.length > 0){
           var c = active.clone();
           c.each(function(m){
               m.hide();
           });
           return true;
       }
       return false;
   }

   // private
   function onHide(m){
       active.remove(m);
       if(active.length < 1){
           map.disable();
           Ext.getDoc().un("mousedown", onMouseDown);
           attached = false;
       }
   }

   // private
   function onShow(m){
       var last = active.last();
       lastShow = new Date();
       active.add(m);
       if(!attached){
           map.enable();
           Ext.getDoc().on("mousedown", onMouseDown);
           attached = true;
       }
       if(m.parentMenu){
          m.getEl().setZIndex(parseInt(m.parentMenu.getEl().getStyle("z-index"), 10) + 3);
          m.parentMenu.activeChild = m;
       }else if(last && !last.isDestroyed && last.isVisible()){
          m.getEl().setZIndex(parseInt(last.getEl().getStyle("z-index"), 10) + 3);
       }
   }

   // private
   function onBeforeHide(m){
       if(m.activeChild){
           m.activeChild.hide();
       }
       if(m.autoHideTimer){
           clearTimeout(m.autoHideTimer);
           delete m.autoHideTimer;
       }
   }

   // private
   function onBeforeShow(m){
       var pm = m.parentMenu;
       if(!pm && !m.allowOtherMenus){
           hideAll();
       }else if(pm && pm.activeChild){
           pm.activeChild.hide();
       }
   }

   // private
   function onMouseDown(e){
       if(lastShow.getElapsed() > 50 && active.length > 0 && !e.getTarget(".x-menu")){
           hideAll();
       }
   }

   return {

       /**
        * Hides all menus that are currently visible
        * @return {Boolean} success True if any active menus were hidden.
        */
       hideAll : function(){
            return hideAll();
       },

       // private
       register : function(menu){
           if(!menus){
               init();
           }
           menus[menu.id] = menu;
           menu.on({
               beforehide: onBeforeHide,
               hide: onHide,
               beforeshow: onBeforeShow,
               show: onShow
           });
       },

        /**
         * Returns a {@link Ext.menu.Menu} object
         * @param {String/Object} menu The string menu id, an existing menu object reference, or a Menu config that will
         * be used to generate and return a new Menu instance.
         * @return {Ext.menu.Menu} The specified menu, or null if none are found
         */
       get : function(menu){
           if(typeof menu == "string"){ // menu id
               if(!menus){  // not initialized, no menus to return
                   return null;
               }
               return menus[menu];
           }else if(menu.events){  // menu instance
               return menu;
           }else if(typeof menu.length == 'number'){ // array of menu items?
               return new Ext.menu.Menu({items:menu});
           }else{ // otherwise, must be a config
               return Ext.create(menu, 'menu');
           }
       },

       // private
       unregister : function(menu){
           delete menus[menu.id];
           menu.un("beforehide", onBeforeHide);
           menu.un("hide", onHide);
           menu.un("beforeshow", onBeforeShow);
           menu.un("show", onShow);
       },

       // private
       registerCheckable : function(menuItem){
           var g = menuItem.group;
           if(g){
               if(!groups[g]){
                   groups[g] = [];
               }
               groups[g].push(menuItem);
           }
       },

       // private
       unregisterCheckable : function(menuItem){
           var g = menuItem.group;
           if(g){
               groups[g].remove(menuItem);
           }
       },
       
       // private
       onCheckChange: function(item, state){
           if(item.group && state){
               var group = groups[item.group],
                   i = 0,
                   len = group.length,
                   current;
                   
               for(; i < len; i++){
                   current = group[i];
                   if(current != item){
                       current.setChecked(false);
                   }
               }
           }
       },

       getCheckedItem : function(groupId){
           var g = groups[groupId];
           if(g){
               for(var i = 0, l = g.length; i < l; i++){
                   if(g[i].checked){
                       return g[i];
                   }
               }
           }
           return null;
       },

       setCheckedItem : function(groupId, itemId){
           var g = groups[groupId];
           if(g){
               for(var i = 0, l = g.length; i < l; i++){
                   if(g[i].id == itemId){
                       g[i].setChecked(true);
                   }
               }
           }
           return null;
       }
   };
}();
