/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 * $Id: MediaField.js 14263 2011-12-11 16:36:17Z nsendetzky $
 */


Ext.ns('MShop.panel.media');

MShop.panel.media.MediaField = Ext.extend(Ext.form.Field, {
    /**
     * @cfg {bool}
     */
    border: true,
    /**
     * @cfg {String}
     */
    defaultImage: '',
    
    cls: 'mshop-admin-media-item-preview',
    
    defaultAutoCreate : {tag:'input', type:'hidden'},
    handleMouseEvents: true,
    
    initComponent: function() {
        this.scope = this;
        this.handler = this.onFileSelect;
        
        this.plugins = this.plugins || [];
        this.browsePlugin = new Ext.ux.file.BrowsePlugin({
            dropElSelector: 'div[class^=x-panel-body]'
        });
        this.plugins.push(this.browsePlugin);
        
        MShop.panel.media.MediaField.superclass.initComponent.call(this);
        
        this.imageSrc = this.defaultImage;
        if(this.border === true) {
            this.width = this.width;
            this.height = this.height;
        }
    },
    
    onRender: function(ct, position) {
        MShop.panel.media.MediaField.superclass.onRender.call(this, ct, position);
        
        // the container for the browe button
        this.buttonCt = Ext.DomHelper.insertFirst(ct, '<div>&nbsp;</div>', true);
        this.buttonCt.applyStyles({
            border: this.border === true ? '1px solid #B5B8C8' : '0'
        });
        this.buttonCt.setSize(this.width, this.height);
        
        this.loadMask = new Ext.LoadMask(this.buttonCt, {msg: _('Loading'), msgCls: 'x-mask-loading'});
        
        // the click to edit text container
        var clickToEditText = _('Click to edit');
        this.textCt = Ext.DomHelper.insertFirst(this.buttonCt, '<div class="x-ux-from-imagefield-text">' + clickToEditText + '</div>', true);
        this.textCt.setSize(this.width, this.height/3);
        var tm = Ext.util.TextMetrics.createInstance(this.textCt);
        tm.setFixedWidth(this.width);
        this.textCt.setStyle({top: ((this.height - tm.getHeight(clickToEditText)) / 2) + 'px'});
        
        // the image container
        // NOTE: this will atm. always be the default image for the first few miliseconds
        this.imageCt = Ext.DomHelper.insertFirst(this.buttonCt, '<img class="' + this.cls + '" src="' + MShop.config.baseurl.content + '/' + this.imageSrc + '"/>' , true);
        this.imageCt.setOpacity(0.2);
        this.imageCt.setStyle({
            position: 'absolute',
            top: '18px'
        });
        
        Ext.apply(this.browsePlugin, {
            buttonCt: this.buttonCt,
            renderTo: this.buttonCt
        });
    },
    
    afterRender: function() {
        MShop.panel.media.MediaField.superclass.afterRender.apply(this, arguments);
        
        this.itemUi = this.findParentBy(function(c){
            return c.isXType(MShop.panel.media.ItemUi, false);
        });
    },
    
    getValue: function() {
        var value = MShop.panel.media.MediaField.superclass.getValue.call(this);
        return value;
    },
    
    setValue: function(value) {
        MShop.panel.media.MediaField.superclass.setValue.call(this, value);
        
        if (! value || value == this.defaultImage) {
            this.imageSrc = this.defaultImage;
        } else {
            this.imageSrc = value;
        }
        this.updateImage();
    },
    
    /**
     * @private
     */
    onFileSelect: function(fileSelector) {
    	
        this.loadMask.show();
        
        var params = {
        	'site' : MShop.config.site["locale.site.code"],
        	'domain' : this.itemUi.domain
        };
        
        if( !this.itemUi.record.phantom ) {
	        params['media.id'] = this.itemUi.record.id;
        }
        
        var uploader = new Ext.ux.file.Uploader({
            fileSelector: fileSelector,
            url: MShop.config.smd.target,
            methodName: 'Media.uploadItem',
            allowHTML5Uploads: false,
            HTML4params: { 'params' : Ext.encode( params ) }
        });
        uploader.on('uploadcomplete', this.onUploadSucess, this);
        uploader.on('uploadfailure', this.onUploadFail, this);
        
        uploader.upload(fileSelector.getFileList()[0]);
    },
    
    /**
     * @private
     */
    onUploadFail: function() {
        this.loadMask.hide();
        Ext.MessageBox.alert(_('Upload Failed'), _('Could not upload image. Please notify your Administrator')).setIcon(Ext.MessageBox.ERROR);
    },
    
    onUploadSucess: function(uploader, record, response) {
        for (var field in response) {
            if (field.match(/\.status|\.label|\.typeid|\.langid/) && this.itemUi.record.get(field)) {
                continue;
            }
            
            // bullshit! -> sequence updateRecord fn?
            this.itemUi.record.data[field] = response[field];
            
            var formField = this.itemUi.mainForm.getForm().findField(field);
            if (formField && response[field]) {
                formField.setValue(response[field]);
            }
        }
        
        //this.setValue(response['media.preview']);
    },
    
    updateImage: function() {
        // only update when new image differs from current
        if(this.imageCt.dom.src.substr(-1 * this.imageSrc.length) != this.imageSrc) {
            var ct = this.imageCt.up('div');
            var img = Ext.DomHelper.insertAfter(this.imageCt, '<img class="' + this.cls + '" src="' + MShop.config.baseurl.content + this.imageSrc + '"/>' , true);
            // replace image after load
            img.on('load', function(){
                this.imageCt.remove();
                this.imageCt = img;
                this.textCt.setVisible(this.imageSrc == this.defaultImage);
                this.imageCt.setOpacity(this.imageSrc == this.defaultImage ? 0.2 : 1);
                this.loadMask.hide();
            }, this);
            img.on('error', function() {
                Ext.MessageBox.alert(_('Image Failed'), _('Could not load file. Please notify your Administrator')).setIcon(Ext.MessageBox.ERROR);
                this.loadMask.hide();
            }, this);
        }
    }
});

Ext.reg('MShop.panel.media.mediafield', MShop.panel.media.MediaField);
