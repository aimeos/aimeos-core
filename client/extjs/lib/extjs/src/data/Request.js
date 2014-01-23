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
 * @class Ext.data.Request
 * A simple Request class used internally to the data package to provide more generalized remote-requests
 * to a DataProxy.
 * TODO Not yet implemented.  Implement in Ext.data.Store#execute
 */
Ext.data.Request = function(params) {
    Ext.apply(this, params);
};
Ext.data.Request.prototype = {
    /**
     * @cfg {String} action
     */
    action : undefined,
    /**
     * @cfg {Ext.data.Record[]/Ext.data.Record} rs The Store recordset associated with the request.
     */
    rs : undefined,
    /**
     * @cfg {Object} params HTTP request params
     */
    params: undefined,
    /**
     * @cfg {Function} callback The function to call when request is complete
     */
    callback : Ext.emptyFn,
    /**
     * @cfg {Object} scope The scope of the callback funtion
     */
    scope : undefined,
    /**
     * @cfg {Ext.data.DataReader} reader The DataReader instance which will parse the received response
     */
    reader : undefined
};
