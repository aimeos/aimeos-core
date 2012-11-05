/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.Direct.Transaction
 * @extends Object
 * <p>Supporting Class for Ext.Direct (not intended to be used directly).</p>
 * @constructor
 * @param {Object} config
 */
Ext.Direct.Transaction = function(config){
    Ext.apply(this, config);
    this.tid = ++Ext.Direct.TID;
    this.retryCount = 0;
};
Ext.Direct.Transaction.prototype = {
    send: function(){
        this.provider.queueTransaction(this);
    },

    retry: function(){
        this.retryCount++;
        this.send();
    },

    getProvider: function(){
        return this.provider;
    }
};