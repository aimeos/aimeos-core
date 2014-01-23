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
Ext.test.session.addTest( 'ArrayReader', {
    name: 'readRecords',
    setUp: function() {
        this.reader = new Ext.data.ArrayReader({
            idIndex: 1,
            fields: [
               {name: 'floater', type: 'float'},
               {name: 'id'},
               {name: 'totalProp', type: 'integer'},
               {name: 'bool', type: 'boolean'},
               {name: 'msg'}
            ]
        });
        this.data1 = [
            [ 1.23, 1, 6, true, 'hello' ]
        ];
        this.rec1 = this.reader.readRecords(this.data1);
    },
    test_tearDown: function() {
        delete this.reader;
        delete this.data1;
        delete this.rec1;
    },
    test_TotalRecords: function() {
        Y.Assert.areSame(this.rec1.totalRecords, 1);
    },
    test_Records: function() {
        Y.Assert.areSame(this.rec1.records[0].data.floater, this.data1[0][0]);
        Y.Assert.areSame(this.rec1.records[0].data.id, this.data1[0][1]);
        Y.Assert.areSame(this.rec1.records[0].data.totalProp, this.data1[0][2]);
        Y.Assert.areSame(this.rec1.records[0].data.bool, this.data1[0][3]);
        Y.Assert.areSame(this.rec1.records[0].data.msg, this.data1[0][4]);
    }
});
