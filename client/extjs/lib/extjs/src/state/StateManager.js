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
 * @class Ext.state.Manager
 * This is the global state manager. By default all components that are "state aware" check this class
 * for state information if you don't pass them a custom state provider. In order for this class
 * to be useful, it must be initialized with a provider when your application initializes. Example usage:
 <pre><code>
// in your initialization function
init : function(){
   Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
   var win = new Window(...);
   win.restoreState();
}
 </code></pre>
 * @singleton
 */
Ext.state.Manager = function(){
    var provider = new Ext.state.Provider();

    return {
        /**
         * Configures the default state provider for your application
         * @param {Provider} stateProvider The state provider to set
         */
        setProvider : function(stateProvider){
            provider = stateProvider;
        },

        /**
         * Returns the current value for a key
         * @param {String} name The key name
         * @param {Mixed} defaultValue The default value to return if the key lookup does not match
         * @return {Mixed} The state data
         */
        get : function(key, defaultValue){
            return provider.get(key, defaultValue);
        },

        /**
         * Sets the value for a key
         * @param {String} name The key name
         * @param {Mixed} value The state data
         */
         set : function(key, value){
            provider.set(key, value);
        },

        /**
         * Clears a value from the state
         * @param {String} name The key name
         */
        clear : function(key){
            provider.clear(key);
        },

        /**
         * Gets the currently configured state provider
         * @return {Provider} The state provider
         */
        getProvider : function(){
            return provider;
        }
    };
}();