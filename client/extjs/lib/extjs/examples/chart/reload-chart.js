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
function generateData(){
    var data = [];
    for(var i = 0; i < 12; ++i){
        data.push([Date.monthNames[i], (Math.floor(Math.random() *  11) + 1) * 100]);
    }
    return data;
}

Ext.onReady(function(){
    var store = new Ext.data.ArrayStore({
        fields: ['month', 'hits'],
        data: generateData()
    });
    
    new Ext.Panel({
        width: 700,
        height: 400,
        renderTo: document.body,
        title: 'Column Chart with Reload - Hits per Month',
        tbar: [{
            text: 'Load new data set',
            handler: function(){
                store.loadData(generateData());
            }
        }],
        items: {
            xtype: 'columnchart',
            store: store,
            yField: 'hits',
	    url: '../../resources/charts.swf',
            xField: 'month',
            xAxis: new Ext.chart.CategoryAxis({
                title: 'Month'
            }),
            yAxis: new Ext.chart.NumericAxis({
                title: 'Hits'
            }),
            extraStyle: {
               xAxis: {
                    labelRotation: -90
                }
            }
        }
    });
});
