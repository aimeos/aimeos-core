/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://www.arcavias.com/en/license
 */


Ext.ns('MShop.elements');

MShop.elements.ImportButton = Ext.extend(Ext.Button, {

	/**
	 * @cfg {Object} importMethod (required)
	 */
	importMethod: null,

	initComponent: function() {
		this.scope = this;
		this.handler = this.onFileSelect;

		this.plugins = this.plugins || [];
		this.browsePlugin = new Ext.ux.file.BrowsePlugin();
		this.plugins.push(this.browsePlugin);

		this.loadMask = new Ext.LoadMask(Ext.getBody(), {msg: _('Loading'), msgCls: 'x-mask-loading'});

		MShop.elements.ImportButton.superclass.initComponent.call(this);
	},

	/**
	 * @private
	 */
	onFileSelect: function(fileSelector) {
		this.loadMask.show();

		var uploader = new Ext.ux.file.Uploader({
			fileSelector: fileSelector,
			url: MShop.config.smd.target,
			methodName: this.importMethod,
			allowHTML5Uploads: false,
			HTML4params: {
				'params' : Ext.encode({
					site: MShop.config.site['locale.site.code']
				})
			}
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

		Ext.MessageBox.alert(
			_('Upload failed'),
			_('Could not upload file. Please notify your administrator.')).setIcon(Ext.MessageBox.ERROR);
	},

	/**
	 * @private
	 */
	onUploadSucess: function(uploader, record, response) {
		this.loadMask.hide();

		Ext.MessageBox.alert(
			_('Upload successful'),
			_('The texts of your uploaded file will be imported within a few minutes. You can check the status of the import in the "Job" panel of the "Overview" tab.') );
	}
});

Ext.reg('MShop.elements.importbutton', MShop.elements.ImportButton);
