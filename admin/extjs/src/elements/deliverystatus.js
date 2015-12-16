/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.elements.deliverystatus');

/**
 * @static
 * @return {String} label
 */
MShop.elements.deliverystatus.renderer = function(value) {

    var store = MShop.elements.deliverystatus.getStore();
    var data = store.getAt(store.find('value', value));

    if(data) {
        return data.get('label');
    }

    return value;
};

/**
 * @static
 * @return {Ext.data.ArrayStore}
 */
MShop.elements.deliverystatus.getStore = function() {

    if(!MShop.elements.deliverystatus._store) {

        MShop.elements.deliverystatus._store = new Ext.data.ArrayStore({
            idIndex : 0,
            fields : [{
                name : 'value',
                type : 'integer'
            }, {
                name : 'label',
                type : 'string'
            }],
            data : [
                [-1, MShop.I18n.dt('admin', 'stat:unfinished')],
                [0, MShop.I18n.dt('admin', 'stat:deleted')],
                [1, MShop.I18n.dt('admin', 'stat:pending')],
                [2, MShop.I18n.dt('admin', 'stat:progress')],
                [3, MShop.I18n.dt('admin', 'stat:dispatched')],
                [4, MShop.I18n.dt('admin', 'stat:delivered')],
                [5, MShop.I18n.dt('admin', 'stat:lost')],
                [6, MShop.I18n.dt('admin', 'stat:refused')],
                [7, MShop.I18n.dt('admin', 'stat:returned')]]
        });
    }

    return MShop.elements.deliverystatus._store;
};
