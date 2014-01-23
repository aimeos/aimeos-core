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
Ext.test.session.addTest('Ext.data.Field', {
    name: 'Test data field creation',
    
    setUp: function(){
        this.t = Ext.data.Types;
        this.st = Ext.data.SortTypes;
    },
    
    tearDown: function(){
        delete this.t;
        delete this.st;
    },
    
    test_alias: function(){
        var f;
        
        f = new Ext.data.Field({type: null});
        Y.Assert.areEqual(this.t.AUTO, f.type, 'Test empty alias');
        f = new Ext.data.Field({type: 'auto'});
        Y.Assert.areEqual(this.t.AUTO, f.type, 'Test auto alias');
        
        f = new Ext.data.Field({type: 'int'});
        Y.Assert.areEqual(this.t.INT, f.type, 'Test int alias');
        f = new Ext.data.Field({type: 'integer'});
        Y.Assert.areEqual(this.t.INT, f.type, 'Test integer alias');
        
        f = new Ext.data.Field({type: 'float'});
        Y.Assert.areEqual(this.t.FLOAT, f.type, 'Test float alias');
        f = new Ext.data.Field({type: 'number'});
        Y.Assert.areEqual(this.t.FLOAT, f.type, 'Test number alias');
        
        f = new Ext.data.Field({type: 'bool'});
        Y.Assert.areEqual(this.t.BOOL, f.type, 'Test bool alias');
        f = new Ext.data.Field({type: 'boolean'});
        Y.Assert.areEqual(this.t.BOOL, f.type, 'Test boolean alias');
        
        f = new Ext.data.Field({type: 'string'});
        Y.Assert.areEqual(this.t.STRING, f.type, 'Test string alias');
        
        f = new Ext.data.Field({type: 'date'});
        Y.Assert.areEqual(this.t.DATE, f.type, 'Test date alias');
    },
    
    test_type: function(){
        var f;
        
        f = new Ext.data.Field({type: this.t.INT});
        Y.Assert.areEqual(this.t.INT, f.type, 'Test int type');
        
        f = new Ext.data.Field({type: this.t.AUTO});
        Y.Assert.areEqual(this.t.AUTO, f.type, 'Test auto type');
        
        f = new Ext.data.Field({type: this.t.STRING});
        Y.Assert.areEqual(this.t.STRING, f.type, 'Test string type');
    },
    
    test_sortType: function(){
        var f,
            s1 = function(v){
                return v * v;    
            },
            s2 = function(v){
                return Math.sqrt(v);
            };
        
        f = new Ext.data.Field({type: 'auto'});
        Y.Assert.areEqual(this.st.none, f.sortType, 'Test sort type defaults for auto');
        
        f = new Ext.data.Field({type: 'int'});
        Y.Assert.areEqual(this.st.none, f.sortType, 'Test sort type defaults for int');
        
        f = new Ext.data.Field({type: 'float'});
        Y.Assert.areEqual(this.st.none, f.sortType, 'Test sort type defaults for float');
        
        f = new Ext.data.Field({type: 'bool'});
        Y.Assert.areEqual(this.st.none, f.sortType, 'Test sort type defaults for bool');
        
        f = new Ext.data.Field({type: 'string'});
        Y.Assert.areEqual(this.st.asUCString, f.sortType, 'Test sort type defaults for string');
        
        f = new Ext.data.Field({type: 'date'});
        Y.Assert.areEqual(this.st.asDate, f.sortType, 'Test sort type defaults for date');
        
        f = new Ext.data.Field({
            type: 'auto',
            sortType: 'asDate'
        });
        Y.Assert.areEqual(this.st.asDate, f.sortType, 'Test with custom string sortType');
        
        f = new Ext.data.Field({
            type: 'int',
            sortType: s1
        });
        Y.Assert.areEqual(s1, f.sortType, 'Test with custom function sortType');
        
        f = new Ext.data.Field({
            type: 'date',
            sortType: s2
        });
        Y.Assert.areEqual(s2, f.sortType, 'Test with another custom function sortType');
    },
    
    test_convert: function(){
        var f,
            c1 = function(v){
                return v / 3;
            },
            c2 = function(v){
                return v * 4;    
            };
            
        f = new Ext.data.Field({type: 'auto'});
        Y.Assert.areEqual(this.t.AUTO.convert, f.convert, 'Test convert for auto');
        
        f = new Ext.data.Field({type: 'int'});
        Y.Assert.areEqual(this.t.INT.convert, f.convert, 'Test convert for int');
        
        f = new Ext.data.Field({type: 'float'});
        Y.Assert.areEqual(this.t.FLOAT.convert, f.convert, 'Test convert for float');
        
        f = new Ext.data.Field({type: 'bool'});
        Y.Assert.areEqual(this.t.BOOL.convert, f.convert, 'Test convert for bool');
        
        f = new Ext.data.Field({type: 'string'});
        Y.Assert.areEqual(this.t.STRING.convert, f.convert, 'Test convert for string');
        
        f = new Ext.data.Field({type: 'date'});
        Y.Assert.areEqual(this.t.DATE.convert, f.convert, 'Test convert for date');
        
        f = new Ext.data.Field({
            type: 'int',
            convert: c1
        });
        Y.Assert.areEqual(c1, f.convert, 'Test with custom function convert');
        
        f = new Ext.data.Field({
            type: 'date',
            convert: c2
        });
        Y.Assert.areEqual(c2, f.convert, 'Test with another custom function convert');
    }
});
