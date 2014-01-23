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
 * Tests Ext.data.Store functionality
 * @author Ed Spencer
 */
(function() {
    var suite  = Ext.test.session.getSuite('Ext.form.FormPanel'),
        assert = Y.Assert;
    
    function buildForm(config) {
        return new Ext.form.FormPanel(config);
    };
    
    suite.add(new Y.Test.Case({
        name: 'initialization',
        
        testCreatesForm: function() {
            var form = buildForm();
            
            assert.isTrue(form.form instanceof Ext.form.BasicForm);
        },
        
        testInitsItems: function() {
            var FormPanel = Ext.form.FormPanel,
                proto     = FormPanel.prototype,
                oldInit   = proto.initItems,
                wasCalled = false;
            
            proto.initItems = function() {
                wasCalled = true;
            };
            
            var form = buildForm();
            assert.isTrue(wasCalled);
            
            proto.initItems = oldInit;
        },
        
        testStartsMonitoring: function() {
            var FormPanel = Ext.form.FormPanel,
                proto     = FormPanel.prototype,
                oldFunc   = proto.startMonitoring,
                wasCalled = false;
            
            proto.startMonitoring = function() {
                wasCalled = true;
            };
            
            var form = buildForm({
                monitorValid: true, 
                renderTo    : Ext.getBody()
            });
            
            form.render();
            assert.isTrue(wasCalled);
            
            proto.startMonitoring = oldFunc;
            form.destroy();
        }
    }));
    
    suite.add(new Y.Test.Case({
        name: 'destruction',
        
        testStopMonitoring: function() {
            var FormPanel = Ext.form.FormPanel,
                proto     = FormPanel.prototype,
                oldFunc   = proto.stopMonitoring,
                wasCalled = false;
            
            proto.stopMonitoring = function() {
                wasCalled = true;
            };
            
            var form = buildForm({
                monitorValid: true, 
                renderTo    : Ext.getBody()
            });
            
            form.render();
            form.destroy();
            assert.isTrue(wasCalled);
            
            proto.stopMonitoring = oldFunc;
        }
    }));
    
    suite.add(new Y.Test.Case({
        name: 'initFields',
        
        testIsField: function() {
            var mockField = {
                setValue    : Ext.emptyFn,
                getValue    : Ext.emptyFn,
                markInvalid : Ext.emptyFn,
                clearInvalid: Ext.emptyFn
            };
            
            var form = buildForm();
            
            assert.isTrue(form.isField(mockField));
        }
    }));
})();