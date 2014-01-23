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
Ext.onReady(function() {
    var SaleRecord = Ext.data.Record.create([
        {name: 'person',   type: 'string'},
        {name: 'product',  type: 'string'},
        {name: 'city',     type: 'string'},
        {name: 'state',    type: 'string'},
        {name: 'month',    type: 'int'},
        {name: 'quarter',  type: 'int'},
        {name: 'year',     type: 'int'},
        {name: 'quantity', type: 'int'},
        {name: 'value',    type: 'int'}
    ]);
    
    var myStore = new Ext.data.Store({
        url: 'simple.json',
        autoLoad: true,
        reader: new Ext.data.JsonReader({
            root: 'rows',
            idProperty: 'id'
        }, SaleRecord)
    });
    
    var pivotGrid = new Ext.grid.PivotGrid({
        title     : 'PivotGrid example',
        width     : 800,
        height    : 259,
        renderTo  : 'docbody',
        store     : myStore,
        aggregator: 'sum',
        measure   : 'value',
        
        viewConfig: {
            title: 'Sales Performance'
        },
        
        leftAxis: [
            {
                width: 80,
                dataIndex: 'person'
            },
            {
                width: 90,
                dataIndex: 'product'
            }
        ],
        
        topAxis: [
            {
                dataIndex: 'year'
            },
            {
                dataIndex: 'city'
            }
        ]
    });
});