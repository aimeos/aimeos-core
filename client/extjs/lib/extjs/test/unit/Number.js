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
Ext.test.session.addTest('Number', {

    name: 'Global Number Decorators',
    
    planned: 15,
    
    // 15
    test_constrain: function(){
        Y.Assert.areEqual(1, (1).constrain(1, 1), 'Test where the number being constrained is equal to both the min and the max');
        Y.Assert.areEqual(5, (5).constrain(1, 5), 'Test where the number being constrained is equal to the max');
        Y.Assert.areEqual(3, (3).constrain(3, 5), 'Test where the number being constrained is equal to the min');
        
        Y.Assert.areEqual(3, (3).constrain(1, 5), 'Test with an integer within the constraints');
        Y.Assert.areEqual(-3, (-3).constrain(-5, -1), 'Test with a negative integer within the constraints');
        Y.Assert.areEqual(3.3, (3.3).constrain(3.1, 3.5), 'Test with a float within the constraints');
        Y.Assert.areEqual(-3.3, (-3.3).constrain(-3.5, -3.1), 'Test with a negative float within the constraints');
        
        Y.Assert.areEqual(5, (100).constrain(1, 5), 'Test with an integer over the maximum of the constraint');
        Y.Assert.areEqual(3, (1).constrain(3, 5), 'Test with an integer under the maximum of the constraint');
        Y.Assert.areEqual(-50, (-5).constrain(-100, -50), 'Test with a negative integer over the maximum of the constraint');
        Y.Assert.areEqual(-5, (-100).constrain(-5, -3), 'Test with a negative integer under the maximum of the constraint');
        
        Y.Assert.areEqual(4.1, (6.7).constrain(3.1, 4.1), 'Test with a float over the maximum of the constraint');
        Y.Assert.areEqual(6.7, (3.1).constrain(6.7, 12.4), 'Test with a float under the maximum of the constraint');
        Y.Assert.areEqual(-50.5, (-3.1).constrain(-100.5, -50.5), 'Test with a negative float over the maximum of the constraint');
        Y.Assert.areEqual(-5.4, (-100.7).constrain(-5.4, -3.1), 'Test with a negative float under the maximum of the constraint');
    }
    
});
