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
Ext.test.session.addTest('Function', {

    name: 'Global Function Decorators',
    
    planned: 71,
    
    // 3
    test_createCallback: function(){
        var fn = function(a, b){
            Y.Assert.areEqual('a', a, 'Test passed param');
            Y.Assert.areEqual('b', b, 'Test passed param');
            return 'x';
        };
        
        var cb = fn.createCallback('a', 'b'),
            rt = cb(); // does not accept params
        Y.Assert.areEqual('x', rt, 'Test return value of callback');
    },
    
    // 24
    test_createDelegate: function(){
        var scope = {
            foo: 'bar'
        };
        
        var fn = function(a, b, c){
            Y.Assert.areEqual(scope, this, 'Test if "this" is correct');
            Y.Assert.areEqual('bar', this.foo, 'Test if property matches');
            Y.Assert.areEqual('a', a, 'Test passed param');
            Y.Assert.areEqual('b', b, 'Test passed param');
            Y.Assert.areEqual('c', c, 'Test passed param');
            return 'x';
        };
        
        var cb = fn.createDelegate(scope, ['b', 'c'], true /* appendArgs: true appends these to passed params */),
            rt = cb('a'); // a b c
        Y.Assert.areEqual('x', rt, 'Test return value');
        
        
        var cbTwo = fn.createDelegate(scope, ['a', 'b'], 0);
        rt = cbTwo('c'); // a b c
        Y.Assert.areEqual('x', rt, 'Test return value');
        
        
        var cbThree = fn.createDelegate(scope, ['b'], 1 /* this replaces at pos 1 */);
        rt = cbThree('a', 'c'); // a b c
        Y.Assert.areEqual('x', rt, 'Test return value');
        
        var cbFour = fn.createDelegate(scope, ['a', 'b', 'c']);
        rt = cbFour('x', 'y'); // overridden with a b c
        Y.Assert.areEqual('x', rt, 'Test return value');
    },
    
    // 19
    test_createInterceptor: function(){
        var scope = {
            foo: 'bar',
            n: 0
        };
        
        var fn = function(a, b){
            Y.Assert.areEqual(scope, this, 'Test if "this" is correct');
            Y.Assert.areEqual('bar', this.foo, 'Test if property matches');
            Y.Assert.areEqual('a', a, 'Test passed param');
            Y.Assert.areEqual('b', b, 'Test passed param');
            this.n++;
            return 'x';
        };
        
        // normal
        var rt = fn.call(scope, 'a', 'b'); // n 1
        Y.Assert.areEqual('x', rt, 'Test the return value');
        
        var cb = fn.createDelegate(scope).createInterceptor(function(a, b, z){
            Y.Assert.areEqual(scope, this, 'Test if "this" is correct');
            Y.Assert.areEqual('bar', this.foo, 'Test if property matches');
            Y.Assert.areEqual('a', a, 'Test passed param');
            Y.Assert.areEqual('b', b, 'Test passed param');
            return z === undefined;
        }, scope);
        // intercepted, but allowed to continue
        rt = cb('a', 'b'); // n 2
        Y.Assert.areEqual('x', rt, 'Test the return value');
        
        // intercepted, and cancelled
        cb('a', 'b', 'z');
        Y.Assert.areEqual(2, scope.n, 'Test the interceptor call count');
    },
    
    // 16
    test_createSequence: function(){
        var scope = {
            foo: 'bar',
            seq: 0
        };
        
        var fn = function(a, b){
            Y.Assert.areEqual(scope, this, 'Test if "this" is correct');
            Y.Assert.areEqual('bar', this.foo, 'Test if property matches');
            Y.Assert.areEqual('a', a, 'Test passed param');
            Y.Assert.areEqual('b', b, 'Test passed param');
            this.seq++;
            return 'x';
        };
        
        var rt = fn.call(scope, 'a', 'b'); // seq 1
        Y.Assert.areEqual('x', rt, 'Test the return value');
        Y.Assert.areEqual(1, scope.seq, 'Test the counter');
        
        var cb = fn.createDelegate(scope).createSequence(fn, scope);
        rt = cb('a', 'b'); // seq 2, 3
        Y.Assert.areEqual('x', rt, 'Test the return value');
        Y.Assert.areEqual(3, scope.seq, 'Test the number of times the sequence was called');
    },
    
    // 9
    test_defer: function(){
        var scope = {
            foo: 'bar',
            n: 0
        };
        
        var fn = function(a, b){
            Y.Assert.areEqual(scope, this, 'Test if "this" is correct');
            Y.Assert.areEqual('bar', this.foo, 'Test if property matches');
            Y.Assert.areEqual('a', a, 'Test passed param');
            Y.Assert.areEqual('b', b, 'Test passed param');
            this.n++;
        };
        
        fn.defer(1, scope, ['a', 'b']);
        fn.defer(2, scope, ['a', 'b']);
        
        setTimeout(function(){
            Y.Assert.areEqual(2, scope.n, 'Test if the counter matches the call timer count');
        }, 4);
    }
    
});
