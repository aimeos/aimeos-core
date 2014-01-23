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
Ext.test.session.addTest('Array', {

    name: 'Global Array Decorators',
    
    planned: 17,
    
    setUp: function(){
        this.Cls = Ext.extend(Object, {});
    },
    
    // 12
    test_indexOf: function(){
        Y.Assert.areEqual(-1, [].indexOf(1), 'Test with an empty array');
        Y.Assert.areEqual(-1, [0, 1, 2].indexOf(3), 'Test with numbers where the item should not exist');
        Y.Assert.areEqual(1, [0, 1, 2].indexOf(1), 'Test with numbers where the item should exist');
        Y.Assert.areEqual(3, [0, 3, 2, 1, 4, 5, 6, 7, 1, 2].indexOf(1), 'Test with numbers where the item exists a number of times');
        Y.Assert.areEqual(-1, ['x', 'y', 'z'].indexOf('X'), 'Test with strings where the item should not exist');
        Y.Assert.areEqual(0, ['a', 'x', 'y', 'z'].indexOf('a'), 'Test with strings where the item should exist');
        Y.Assert.areEqual(-1, [0, 1, 2].indexOf('1'), 'Test to ensure type coercion doesn\'t occur');
        
        var c1 = new this.Cls(), 
            c2 = new this.Cls(), 
            c3 = new this.Cls(), 
            c4 = new this.Cls();
        
        Y.Assert.areEqual(-1, [c1, c2, c3, c4].indexOf(new this.Cls()), 'Test with object instances, item should not exist');
        Y.Assert.areEqual(2, [c1, c2, c3, c4].indexOf(c3), 'Test with object instances, item should exist');
        
        //test the from parameter
        Y.Assert.areEqual(-1, [1, 2, 3, 4, 5].indexOf(1, 3), 'Test where the item exists past the from parameter');
        Y.Assert.areEqual(-1, [1, 2, 3, 4, 5].indexOf(1, 50), 'Test where the item exists, but the index is greater than the array');
        Y.Assert.areEqual(7, [1, 2, 3, 4, 5, 6, 7, 3].indexOf(3, 4), 'Test where the item more than once, the from should refer to the second item');
    },
    
    // 5
    test_remove: function(){
        var arr = [];
        
        arr.remove(1);
        Y.ArrayAssert.isEmpty(arr, 'Test with an empty array');
        
        arr = [1, 2, 3];
        arr.remove(1);
        Y.ArrayAssert.itemsAreEqual([2, 3], arr, 'Test with a simple removal');
        
        arr = [1, 2, 3, 1];
        arr.remove(1);
        Y.ArrayAssert.itemsAreEqual([2, 3, 1], arr, 'Test where the item exists more than once');
        
        arr = [1, 2, 3, 4];
        arr.remove(100);
        Y.ArrayAssert.itemsAreEqual([1, 2, 3, 4], arr, 'Test where the item doesn\'t exist');
        
        var c1 = new this.Cls(), 
            c2 = new this.Cls(), 
            c3 = new this.Cls();
        
        arr = [c1, c2, c3];
        arr.remove(c2);
        Y.ArrayAssert.itemsAreEqual([c1, c3], arr, 'Test with object instances');
    }
    
});
