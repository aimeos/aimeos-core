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
Imgorg.DirectCombo = Ext.extend(Ext.form.ComboBox, {
    displayField: 'text',
    valueField: 'id',
    triggerAction: 'all',
    queryAction: 'name',
    forceSelection: true,
    mode: 'remote',
    
    initComponent: function() {
        this.store = new Ext.data.DirectStore(Ext.apply({
            api: this.api,
            root: '',
            fields: this.fields || ['text', 'id']
        }, this.storeConfig));
        
        Imgorg.DirectCombo.superclass.initComponent.call(this);
    }
});

Imgorg.TagCombo = Ext.extend(Imgorg.DirectCombo,{
    forceSelection: false,
    storeConfig: {
        id: 'tag-store'
    },
    initComponent: function() {
        Ext.apply(this.storeConfig, {
            directFn: Imgorg.ss.Tags.load
        });
        Imgorg.TagCombo.superclass.initComponent.call(this);
    }
});
Ext.reg('img-tagcombo', Imgorg.TagCombo);

Imgorg.TagMultiCombo = Ext.extend(Ext.ux.MultiCombo,{
    listClass: 'label-combo',
    displayField: 'text',
    valueField: 'id',
    
    initComponent: function() {
        this.store = new Ext.data.DirectStore(Ext.apply({
            directFn: Imgorg.ss.Tags.load,
            root: '',
            autoLoad: true,
            fields: this.fields || ['text', 'id']
        }, this.storeConfig));
        this.plugins =new Ext.ux.MultiCombo.Checkable({});
        Imgorg.DirectCombo.superclass.initComponent.call(this);
    }
});
Ext.reg('img-tagmulticombo', Imgorg.TagMultiCombo);

Imgorg.AlbumCombo = Ext.extend(Imgorg.DirectCombo, {
    storeConfig: {
        id: 'album-store'
    },
    initComponent: function() {
        Ext.apply(this.storeConfig, {
            directFn: Imgorg.ss.Albums.getAllInfo
        });
        Imgorg.AlbumCombo.superclass.initComponent.call(this);
    }
});
Ext.reg('img-albumcombo', Imgorg.AlbumCombo);
