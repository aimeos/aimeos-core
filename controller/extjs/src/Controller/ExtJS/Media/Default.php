<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJs media controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Media_Default
	extends Controller_ExtJS_Abstract
	implements Controller_ExtJS_Common_Interface
{
	private $_manager = null;


	/**
	 * Initializes the media controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context, 'Media' );

		$this->_manager = MShop_Media_Manager_Factory::createManager( $context );
	}


	/**
	 * Deletes an item or a list of items.
	 *
	 * @param stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function deleteItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$config = $this->_getContext()->getConfig();

		$basedir = $config->get( 'controller/extjs/media/default/basedir', '.' );
		$uploaddir = $config->get( 'controller/extjs/media/default/upload/directory', 'upload' );

		$idList = array();
		$manager = $this->_getManager();
		foreach( (array) $params->items as $id )
		{
			$item = $manager->getItem( $id );
			$idList[ $item->getDomain() ][] = $id;

			if( is_file( $basedir . $item->getPreview() ) && strcmp( ltrim( $uploaddir, '/' ), ltrim( $item->getPreview(), '/' ) ) !== 0 && unlink( $basedir . $item->getPreview() ) === false ) {
				$this->_getContext()->getLogger()->log( sprintf( 'Deleting file "%1$s" failed', $basedir . $item->getPreview() ), MW_Logger_Abstract::WARN );
			}

			if( is_file( $basedir . $item->getUrl() ) && unlink( $basedir . $item->getUrl() ) === false ) {
				$this->_getContext()->getLogger()->log( sprintf( 'Deleting file "%1$s" failed', $basedir . $item->getUrl() ), MW_Logger_Abstract::WARN );
			}

			$manager->deleteItem( $id );
		}

		foreach( (array) $idList as $manager => $ids )
		{
			$refDomainListManager = $this->_getDomainManager( $manager )->getSubManager('list');
			$search = $refDomainListManager->createSearch();
			$expr = array(
				$search->compare( '==', $manager.'.list.refid', $ids ),
				$search->compare( '==', $manager.'.list.domain', 'media' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$start = 0;

			do
			{
				$result = $refDomainListManager->searchItems( $search );

				foreach ( $result as $item ) {
					$refDomainListManager->deleteItem( $item->getId() );
				}

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count > 0 );
		}

		return array(
			'success' => true,
		);
	}


	/**
	 * Creates a new media item or updates an existing one or a list thereof.
	 *
	 * @param stdClass $params Associative array containing the text properties
	 */
	public function saveItems( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'items' ) );
		$this->_setLocale( $params->site );

		$ids = array();
		$items = ( !is_array( $params->items ) ? array( $params->items ) : $params->items );

		foreach( $items as $entry )
		{
			$item = $this->_manager->createItem();

			if( isset( $entry->{'media.id'} ) ) { $item->setId( $entry->{'media.id'} ); }
			if( isset( $entry->{'media.typeid'} ) ) { $item->setTypeId( $entry->{'media.typeid'} ); }
			if( isset( $entry->{'media.domain'} ) ) { $item->setDomain( $entry->{'media.domain'} ); }
			if( isset( $entry->{'media.label'} ) ) { $item->setLabel( $entry->{'media.label'} ); }
			if( isset( $entry->{'media.status'} ) ) { $item->setStatus( $entry->{'media.status'} ); }
			if( isset( $entry->{'media.url'} ) ) { $item->setUrl( $entry->{'media.url'} ); }
			if( isset( $entry->{'media.preview'} ) ) { $item->setPreview( $entry->{'media.preview'} ); }
			if( isset( $entry->{'media.mimetype'} ) ) { $item->setMimeType( $entry->{'media.mimetype'} ); }

			if( isset( $entry->{'media.languageid'} ) )
			{
				$langid = ( $entry->{'media.languageid'} != '' ? $entry->{'media.languageid'} : null );
				$item->setLanguageId( $langid );
			}

			$this->_manager->saveItem( $item );

			$ids[] = $item->getId();
		}

		$search = $this->_manager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );
		$items = $this->_toArray( $this->_manager->searchItems( $search ) );

		return array(
			'items' => ( !is_array( $params->items ) ? reset( $items ) : $items ),
			'success' => true,
		);
	}


	public function uploadItem( stdClass $params )
	{
		$this->_checkParams( $params, array( 'site', 'domain' ) );
		$this->_setLocale( $params->site );


		if( ( $fileinfo = reset( $_FILES ) ) === false ) {
			throw new Controller_ExtJS_Exception( 'No file was uploaded' );
		}

		if( $this->_getContext()->getConfig()->get( 'controller/extjs/media/default/enablecheck', true ) ) {
			$this->_checkFileUpload( $fileinfo['tmp_name'], $fileinfo['error'] );
		}


		$filename = md5( $fileinfo['name'] . time() );
		$mimetype = $this->_getMimeType( $fileinfo['tmp_name'] );

		$item = $this->_manager->createItem();
		$item->setDomain( $params->domain );
		$item->setLabel( basename( $fileinfo['name'] ) );
		$item->setMimeType( $mimetype );


		try {
			$item->setPreview( $this->_createImage( 'preview', $fileinfo['tmp_name'], $mimetype, $params->domain, $filename, 360, 280 ) );
		} catch( Exception $e ) {
			$item->setPreview( $this->_getMimeIcon( $mimetype ) );
		}

		if( strncmp( $mimetype, 'image', 5 ) === 0 ) {
			$item->setUrl( $this->_createImage( 'files', $fileinfo['tmp_name'], $mimetype, $params->domain, $filename ) );
		} else {
			$item->setUrl( $this->_copyFile( $fileinfo['tmp_name'], $params->domain, $filename . $this->_getFileExtension( $fileinfo['name'] ) ) );
		}

		if( unlink( $fileinfo['tmp_name'] ) === false ) {
			$this->_getContext()->getLogger()->log( sprintf( 'Deleting file "%1$s" failed', $fileinfo['tmp_name'] ), MW_Logger_Abstract::WARN );
		}


		return (object) $item->toArray();
	}


	/**
	 * Returns the service description of the class.
	 * It describes the class methods and its parameters including their types
	 *
	 * @return array Associative list of class/method names, their parameters and types
	 */
	public function getServiceDescription()
	{
		$smd = parent::getServiceDescription();

		$smd['Media.uploadItem'] = array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "string","name" => "domain","optional" => false ),
				),
				"returns" => "array",
		);

		return $smd;
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return mixed Manager object
	 */
	protected function _getManager()
	{
		return $this->_manager;
	}


	protected function _checkFileUpload( $filename, $errcode )
	{
		if( is_uploaded_file( $filename ) === false ) {
			throw new Controller_ExtJS_Exception( 'File was not uploaded' );
		}

		switch( $errcode )
		{
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new Controller_ExtJS_Exception( 'The uploaded file exceeds the max. allowed filesize' );
			case UPLOAD_ERR_PARTIAL:
				throw new Controller_ExtJS_Exception( 'The uploaded file was only partially uploaded' );
			case UPLOAD_ERR_NO_FILE:
				throw new Controller_ExtJS_Exception( 'No file was uploaded' );
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new Controller_ExtJS_Exception( 'Temporary folder is missing' );
			case UPLOAD_ERR_CANT_WRITE:
				throw new Controller_ExtJS_Exception( 'Failed to write file to disk' );
			case UPLOAD_ERR_EXTENSION:
				throw new Controller_ExtJS_Exception( 'File upload stopped by extension' );
			default:
				throw new Controller_ExtJS_Exception( 'Unknown upload error' );
		}
	}


	protected function _getMimeType( $filename )
	{
		$cmd = $this->_getContext()->getConfig()->get( 'controller/extjs/media/default/command/file', 'file -b --mime-type %1$s' );

		$cmdline = sprintf( $cmd, escapeshellarg( $filename ) );
		$this->_getContext()->getLogger()->log( sprintf( 'Executing: %1$s', $cmdline ), MW_Logger_Abstract::DEBUG );

		$value = 0;
		$msg = array();
		$mimetype = exec(  $cmdline, $msg, $value );

		if( $value != 0 ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Error executing "%1$s"', $cmdline ) );
		}

		if( strpos( $mimetype, '/' ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid mime type "%1$s"', $mimetype ) );
		}

		return $mimetype;
	}


	protected function _getImageType( $filename )
	{
		$cmd = $this->_getContext()->getConfig()->get( 'controller/extjs/media/default/command/identify', 'identify -format "%%m" %1$s' );

		$cmdline = sprintf( $cmd, escapeshellarg( $filename ) );
		$this->_getContext()->getLogger()->log( 'Executing: ' . $cmdline, MW_Logger_Abstract::DEBUG );

		$value = 0;
		$msg = array();
		$type = exec( $cmdline, $msg, $value );

		if( $value != 0 || empty( $msg ) ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Error executing "%1$s', $cmd ) );
		}

		return $type;
	}


	protected function _getAbsoluteDirectory( $subdirectory )
	{
		$config = $this->_getContext()->getConfig();

		if( ( $dir = $config->get( 'controller/extjs/media/default/basedir', null ) ) === null ) {
			throw new Controller_ExtJS_Exception( 'No base directory configured' );
		}

		$dir .= DIRECTORY_SEPARATOR . $subdirectory;
		$perms = $config->get( 'controller/extjs/media/default/upload/dirperms', 0775 );

		if( is_dir( $dir ) === false && mkdir( $dir, $perms, true ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "%1$s" with permissions "%2$o"', $dir, $perms ) );
		}

		return $dir;
	}


	protected function _getFileExtension( $filename )
	{
		$baselen = strlen( $filename );

		if( ( $pos = strrpos( $filename, '.' ) ) === false || $baselen - $pos > 5 ) {
			return '';
		}

		if( ( $fileext = substr( $filename, $pos ) ) === false ) {
			return '';
		}

		return $fileext;
	}


	protected function _getMimeIcon( $mimetype )
	{
		$config = $this->_getContext()->getConfig();

		if( ( $mimedir = $config->get( 'controller/extjs/media/default/mimeicon/directory', null ) ) === null )
		{
			$this->_getContext()->getLogger()->log( 'No directory for mime type images configured' );
			return '';
		}

		$ext = $config->get( 'controller/extjs/media/default/mimeicon/extension', '.png' );
		$abspath = $this->_getAbsoluteDirectory( $mimedir ) . DIRECTORY_SEPARATOR . $mimetype . $ext;
		$mimeicon = $mimedir . DIRECTORY_SEPARATOR . $mimetype . $ext;

		if( is_file( $abspath ) === false ) {
			$mimeicon = $mimedir . DIRECTORY_SEPARATOR . 'unknown' . $ext;
		}

		return $mimeicon;
	}


	protected function _createImage( $type, $src, $mimetype, $subdirectory, $filename, $defaultwidth = null, $defaultheight = null )
	{
		$imgtype = $this->_getImageType( $src );
		$config = $this->_getContext()->getConfig();


		$types = $config->get( 'controller/extjs/media/default/' . $type . '/allowedtypes', array( 'image/jpeg', 'image/png', 'image/gif' ) );

		if( in_array( $mimetype, $types ) === true ) {
			$fileext =  '.' . strtolower( $imgtype );
		} else {
			$fileext = '.' . $config->get( 'controller/extjs/media/default/' . $type . '/defaulttype', 'jpeg' );;
		}


		if( ( $mediadir = $this->_getContext()->getConfig()->get( 'controller/extjs/media/default/upload/directory', null ) ) === null ) {
			throw new Controller_ExtJS_Exception( 'No media directory configured' );
		}

		$filepath = $mediadir . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $subdirectory . DIRECTORY_SEPARATOR . $filename[0] . DIRECTORY_SEPARATOR . $filename[1];
		$dest =  $this->_getAbsoluteDirectory( $filepath ). DIRECTORY_SEPARATOR . $filename . $fileext;


		$maxwidth = $config->get( 'controller/extjs/media/default/' . $type . '/maxwidth', $defaultwidth );
		$maxheight = $config->get( 'controller/extjs/media/default/' . $type . '/maxheight', $defaultheight );

		if( $maxwidth !== null || $maxheight !== null ) {
			$this->_convertImage( $src, $dest, $maxwidth, $maxheight );
		} else if( copy( $src, $dest ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Copying file "%1$s" to "%2$s" failed', $src, $dest ) );
		}


		$perms = $config->get( 'controller/extjs/media/default/upload/fileperms', 0664 );

		if( chmod( $dest, $perms ) === false ) {
			$this->_getContext()->getLogger()->log( sprintf( 'Changing file permissions for "%1$s" to "%1$o" failed', $dest, $perms ), MW_Logger_Abstract::WARN );
		}


		return $filepath . DIRECTORY_SEPARATOR . $filename . $fileext;
	}


	protected function _convertImage( $src, $dest, $maxwidth, $maxheight )
	{
		$maxwidth = ( $maxwidth != null ? (int) $maxwidth : '' );
		$maxheight = ( $maxheight != null ? (int) $maxheight : '' );

		$cmd = $this->_getContext()->getConfig()->get( 'controller/extjs/media/default/command/convert', 'convert -quiet %1$s -resize %3$sx%4$s %2$s' );

		$cmdline = sprintf( $cmd, escapeshellarg( $src ), escapeshellarg( $dest ), (string) $maxwidth, (string) $maxheight );
		$this->_getContext()->getLogger()->log( 'Executing: ' . $cmdline, MW_Logger_Abstract::DEBUG );

		$value = 0;
		$msg = array();
		exec( $cmdline, $msg, $value );

		if( $value != 0 ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Error executing "%1$s"', $cmd ) );
		}
	}


	protected function _copyFile( $src, $subdirectory, $filename )
	{
		$config = $this->_getContext()->getConfig();

		if( ( $mediadir = $this->_getContext()->getConfig()->get( 'controller/extjs/media/default/upload/directory', null ) ) === null ) {
				throw new Controller_ExtJS_Exception( 'No media directory configured' );
		}

		$filepath = $mediadir . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $subdirectory . DIRECTORY_SEPARATOR . $filename[0] . DIRECTORY_SEPARATOR . $filename[1];
		$dest = $this->_getAbsoluteDirectory( $filepath ) . DIRECTORY_SEPARATOR . $filename;

		if( copy( $src, $dest ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Copying file "%1$s" to "%2$s" failed', $src, $dest ) );
		}

		$perms = $this->_getContext()->getConfig()->get( 'controller/extjs/media/default/upload/fileperms', 0664 );

		if( chmod( $dest, $perms ) === false ) {
			$this->_getContext()->getLogger()->log( sprintf( 'Changing file permissions for "%1$s" to "%1$o" failed', $dest, $perms ), MW_Logger_Abstract::WARN );
		}

		return $filepath . DIRECTORY_SEPARATOR . $filename;
	}
}
