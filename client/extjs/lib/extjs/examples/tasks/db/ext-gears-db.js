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
Ext.data.GearsDB = Ext.extend(Ext.data.SqlDB, {
	// abstract methods
    open : function(db, cb, scope){
        this.conn = google.gears.factory.create('beta.database', '1.0');
        this.conn.open(db);
        this.openState = true;
		Ext.callback(cb, scope, [this]);
		this.fireEvent('open', this);
    },

	close : function(){
        this.conn.close();
        this.fireEvent('close', this);
    },

    exec : function(sql, cb, scope){
        this.conn.execute(sql).close();
        Ext.callback(cb, scope, [true]);
    },

	execBy : function(sql, args, cb, scope){
	    this.conn.execute(sql, args).close();
        Ext.callback(cb, scope, [true]);
    },

	query : function(sql, cb, scope){
        var rs = this.conn.execute(sql);
        var r = this.readResults(rs);
        Ext.callback(cb, scope, [r]);
        return r;
    },

	queryBy : function(sql, args, cb, scope){
        var rs = this.conn.execute(sql, args);
        var r = this.readResults(rs);
        Ext.callback(cb, scope, [r]);
        return r;
    },

    readResults : function(rs){
        var r = [];
        if(rs){
            var c = rs.fieldCount();
            // precache field names
            var fs = [];
            for(var i = 0; i < c; i++){
                fs[i] = rs.fieldName(i);
            }
            // read the data
            while(rs.isValidRow()){
                var o = {};
                for(var i = 0; i < c; i++){
                    o[fs[i]] = rs.field(i);
                }
                r[r.length] = o;
                rs.next();
            }
            rs.close();
        }
        return r;
    },

    // protected/inherited method
    isOpen : function(){
		return this.openState;
	},

	getTable : function(name, keyName){
		return new Ext.data.SqlDB.Table(this, name, keyName);
	}
});