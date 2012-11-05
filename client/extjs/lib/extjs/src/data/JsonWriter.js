/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.data.JsonWriter
 * @extends Ext.data.DataWriter
 * DataWriter extension for writing an array or single {@link Ext.data.Record} object(s) in preparation for executing a remote CRUD action.
 */
Ext.data.JsonWriter = Ext.extend(Ext.data.DataWriter, {
    /**
     * @cfg {Boolean} encode <tt>true</tt> to {@link Ext.util.JSON#encode encode} the
     * {@link Ext.data.DataWriter#toHash hashed data}. Defaults to <tt>true</tt>.  When using
     * {@link Ext.data.DirectProxy}, set this to <tt>false</tt> since Ext.Direct.JsonProvider will perform
     * its own json-encoding.  In addition, if you're using {@link Ext.data.HttpProxy}, setting to <tt>false</tt>
     * will cause HttpProxy to transmit data using the <b>jsonData</b> configuration-params of {@link Ext.Ajax#request}
     * instead of <b>params</b>.  When using a {@link Ext.data.Store#restful} Store, some serverside frameworks are
     * tuned to expect data through the jsonData mechanism.  In those cases, one will want to set <b>encode: <tt>false</tt></b>, as in
     * let the lower-level connection object (eg: Ext.Ajax) do the encoding.
     */
    encode : true,
    /**
     * @cfg {Boolean} encodeDelete False to send only the id to the server on delete, true to encode it in an object
     * literal, eg: <pre><code>
{id: 1}
 * </code></pre> Defaults to <tt>false</tt>
     */
    encodeDelete: false,
    
    constructor : function(config){
        Ext.data.JsonWriter.superclass.constructor.call(this, config);    
    },

    /**
     * Final action of a write event.  Apply the written data-object to params.
     * @param {Object} http params-object to write-to.
     * @param {Object} baseParams as defined by {@link Ext.data.Store#baseParams}.  The baseParms must be encoded by the extending class, eg: {@link Ext.data.JsonWriter}, {@link Ext.data.XmlWriter}.
     * @param {Object/Object[]} data Data-object representing compiled Store-recordset.
     */
    render : function(params, baseParams, data) {
        if (this.encode === true) {
            // Encode here now.
            Ext.apply(params, baseParams);
            params[this.meta.root] = Ext.encode(data);
        } else {
            // defer encoding for some other layer, probably in {@link Ext.Ajax#request}.  Place everything into "jsonData" key.
            var jdata = Ext.apply({}, baseParams);
            jdata[this.meta.root] = data;
            params.jsonData = jdata;
        }
    },
    /**
     * Implements abstract Ext.data.DataWriter#createRecord
     * @protected
     * @param {Ext.data.Record} rec
     * @return {Object}
     */
    createRecord : function(rec) {
       return this.toHash(rec);
    },
    /**
     * Implements abstract Ext.data.DataWriter#updateRecord
     * @protected
     * @param {Ext.data.Record} rec
     * @return {Object}
     */
    updateRecord : function(rec) {
        return this.toHash(rec);

    },
    /**
     * Implements abstract Ext.data.DataWriter#destroyRecord
     * @protected
     * @param {Ext.data.Record} rec
     * @return {Object}
     */
    destroyRecord : function(rec){
        if(this.encodeDelete){
            var data = {};
            data[this.meta.idProperty] = rec.id;
            return data;
        }else{
            return rec.id;
        }
    }
});