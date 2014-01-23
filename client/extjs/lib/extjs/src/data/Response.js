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
 * @class Ext.data.Response
 * A generic response class to normalize response-handling internally to the framework.
 */
Ext.data.Response = function(params) {
    Ext.apply(this, params);
};
Ext.data.Response.prototype = {
    /**
     * @cfg {String} action {@link Ext.data.Api#actions}
     */
    action: undefined,
    /**
     * @cfg {Boolean} success
     */
    success : undefined,
    /**
     * @cfg {String} message
     */
    message : undefined,
    /**
     * @cfg {Array/Object} data
     */
    data: undefined,
    /**
     * @cfg {Object} raw The raw response returned from server-code
     */
    raw: undefined,
    /**
     * @cfg {Ext.data.Record/Ext.data.Record[]} records related to the Request action
     */
    records: undefined
};
