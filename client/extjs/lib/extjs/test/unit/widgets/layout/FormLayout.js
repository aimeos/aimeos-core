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
    var suite  = Ext.test.session.getSuite('Ext.layout.FormLayout'),
        assert = Y.Assert;
    
    function buildLayout(config) {
        var layout = new Ext.layout.FormLayout(config || {});
        
        //give a mock container
        layout.container = {
            itemCls: 'ctCls'
        };
        
        return layout;
    };
    
    suite.add(new Y.Test.Case({
        name: 'getTemplateArgs',
        
        setUp: function() {
            this.layout = buildLayout({
                labelStyle  : 'color: red;',
                elementStyle: 'padding-left:0;'
            });
            
            //mock fields
            this.field1 = {
                id        : 'myField',
                itemCls   : 'myCls',
                clearCls  : 'myClearCls',
                fieldLabel: 'A Label',
                labelStyle: 'border-top: 10px;'
            };
            
            this.field2 = {
                id            : 'myField2',
                fieldLabel    : 'My Label',
                labelSeparator: '@'
            };
            
            this.field3 = Ext.apply({}, {
                fieldLabel: 'Third label',
                hideLabel : true
            }, this.field2);
            
            this.args1 = this.layout.getTemplateArgs(this.field1);
            this.args2 = this.layout.getTemplateArgs(this.field2);
            this.args3 = this.layout.getTemplateArgs(this.field3);
        },
        
        testId: function() {
            assert.areEqual('myField',  this.args1.id);
            assert.areEqual('myField2', this.args2.id);
        },
        
        testLabel: function() {
            assert.areEqual('A Label',     this.args1.label);
            assert.areEqual('My Label',    this.args2.label);
            assert.areEqual('Third label', this.args3.label);
        },
        
        //adds field's label style to layout's general label style
        testLabelStyle: function() {
            assert.areEqual('color: red;border-top: 10px;', this.args1.labelStyle);
        },
        
        testElementStyle: function() {
            assert.areEqual('padding-left:0;', this.args1.elementStyle);
        },
        
        testLabelSeparator: function() {
            assert.areEqual(':', this.args1.labelSeparator);
            assert.areEqual('@', this.args2.labelSeparator);
            assert.areEqual('',  this.args3.labelSeparator);
        },
        
        testItemCls: function() {
            assert.areEqual('myCls', this.args1.itemCls);
            assert.areEqual('ctCls', this.args2.itemCls);
            assert.areEqual('ctCls x-hide-label', this.args3.itemCls);
        },
        
        testClearCls: function() {
            assert.areEqual('myClearCls',        this.args1.clearCls);
            assert.areEqual('x-form-clear-left', this.args2.clearCls);            
        }
    }));
})();