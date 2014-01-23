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
Imgorg.TagWindow = Ext.extend(Ext.Window, {
    title: 'Choose Tag',
    layout: 'fit',
    closeAction: 'hide',
    width: 300,
    modal: true,
    
    initComponent: function() {
        Ext.apply(this, {
            items: [{
                autoHeight: true,
                xtype: 'form',
                id: 'tag-select',
                bodyStyle: 'padding:15px',
                labelWidth: 50,
                items: [{
                    anchor: '95%',
                    fieldLabel: 'Tag',
                    xtype: 'img-tagcombo',
                    name: 'tag',
                    allowBlank: false
                }]
            }],
            buttons: [{
                text: 'Tag Images',
                handler: this.tagImages,
                scope: this
            },{
                text: 'Cancel',
                handler: function() {
                    this.hide();
                },
                scope: this
            }]
        });
        Imgorg.TagWindow.superclass.initComponent.call(this);
    },
    
        
    tagImages: function() {
        var af = this.getComponent('tag-select').getForm();
        if (af.isValid()) {
            if (this.selectedRecords) {
                var imageIds = [];
                for (var i = 0; i < this.selectedRecords.length; i++) {
                    var r = this.selectedRecords[i];
                    imageIds.push(r.data.dbid || r.data.id);
                }
                var fld = af.findField('tag');
                var tag = fld.getRawValue();
                var idx = fld.store.find('text', tag);
                if (idx != -1) {
                    rec = fld.store.getAt(idx);
                    tag = rec.data.id;
                }
                Imgorg.ss.Images.tagImage({
                    images: imageIds,
                    tag: tag
                });
            }
            this.hide();
        }
    }
});

