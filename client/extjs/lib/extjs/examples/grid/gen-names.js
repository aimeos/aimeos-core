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
(function(){
    var lasts = ['Jones', 'Smith', 'Lee', 'Wilson', 'Black', 'Williams', 'Lewis', 'Johnson', 'Foot', 'Little', 'Vee', 'Train', 'Hot', 'Mutt'];
    var firsts = ['Fred', 'Julie', 'Bill', 'Ted', 'Jack', 'John', 'Mark', 'Mike', 'Chris', 'Bob', 'Travis', 'Kelly', 'Sara'];
    var lastLen = lasts.length, firstLen = firsts.length;

    Ext.ux.getRandomInt = function(min, max){
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    Ext.ux.generateName = function(){
        var name = firsts[Ext.ux.getRandomInt(0, firstLen-1)] + ' ' + lasts[Ext.ux.getRandomInt(0, lastLen-1)];
        if(Ext.ux.generateName.usedNames[name]){
            return Ext.ux.generateName();
        }
        Ext.ux.generateName.usedNames[name] = true;
        return name;
    }
    Ext.ux.generateName.usedNames = {};

})();
