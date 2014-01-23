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
    var myStore = new Ext.data.ArrayStore({
        autoLoad: true,
        fields: [
            'economy', 'region', 'year',
            {name: 'procedures', type: 'int'},
            {name: 'time',       type: 'int'}
        ],
        url   : 'countries.json'
    });
    
    var pivotGrid = new Ext.grid.PivotGrid({
        title     : 'Ease of doing business',
        width     : 800,
        height    : 400,
        renderTo  : 'docbody',
        store     : myStore,
        aggregator: 'sum',
        measure   : 'time',
        
        viewConfig: {
            getCellCls: function(value) {
                if (value < 20) {
                    return 'expense-low';
                } else if (value < 75) {
                    return 'expense-medium';
                } else {
                    return 'expense-high';
                }
            }
        },
        
        leftAxis: [
            {
                width: 165,
                dataIndex: 'economy'
            }
        ],
        
        topAxis: [
            {
                dataIndex: 'year'
            }
        ],
        
        //toggles the Region dimension on and off
        tbar: [
            {
                text: 'Toggle Region',
                enableToggle: true,
                toggleHandler: function() {
                    var leftAxis      = pivotGrid.leftAxis,
                        oldDimensions = leftAxis.dimensions,
                        newDimensions = [];
                    
                    if (oldDimensions.length == 1) {
                        newDimensions.push({
                            width: 100,
                            dataIndex: 'region'
                        }); 
                    }
                    
                    newDimensions.push({
                        width: 165,
                        dataIndex: 'economy'
                    });
                    
                    leftAxis.setDimensions(newDimensions);
                    pivotGrid.view.refresh(true);
                }
            }
        ]
    });
});