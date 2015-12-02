<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Media;


/**
 * ExtJs media controller for admin interfaces.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Standard
	extends \Aimeos\Controller\ExtJS\Base
	implements \Aimeos\Controller\ExtJS\Common\Iface
{
	private $manager = null;


	/**
	 * Initializes the media controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context, 'Media' );
	}


	/**
	 * Deletes an item or a list of items.
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return array Associative list with success value
	 */
	public function deleteItems( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'items' ) );
		$this->setLocale( $params->site );

		$idList = array();
		$ids = (array) $params->items;
		$context = $this->getContext();
		$manager = $this->getManager();
		$fs = $context->getFilesystemManager()->get( 'fs-media' );


		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.id', $ids ) );
		$search->setSlice( 0, count( $ids ) );

		foreach( $manager->searchItems( $search ) as $id => $item )
		{
			$idList[$item->getDomain()][] = $id;

			try
			{
				$fs->rm( $item->getUrl() );
				$fs->rm( $item->getPreview() );
			}
			catch( \Exception $e ) {}
		}

		$manager->deleteItems( $ids );


		foreach( $idList as $domain => $domainIds )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );

			$search = $manager->createSearch();
			$expr = array(
				$search->compare( '==', $domain . '.lists.refid', $domainIds ),
				$search->compare( '==', $domain . '.lists.domain', 'media' )
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', $domain . '.lists.id' ) ) );

			$start = 0;

			do
			{
				$result = $manager->searchItems( $search );
				$manager->deleteItems( array_keys( $result ) );

				$count = count( $result );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count >= $search->getSliceSize() );
		}


		$this->clearCache( $ids );

		return array(
			'success' => true,
		);
	}


	/**
	 * Stores an uploaded file
	 *
	 * @param \stdClass $params Associative list of parameters
	 * @return \stdClass Object with success value
	 * @throws \Aimeos\Controller\ExtJS\Exception If an error occurs
	 */
	public function uploadItem( \stdClass $params )
	{
		$this->checkParams( $params, array( 'site', 'domain' ) );
		$this->setLocale( $params->site );


		if( ( $fileinfo = reset( $_FILES ) ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( 'No file was uploaded' );
		}

		$config = $this->getContext()->getConfig();

		/** controller/extjs/media/standard/options
		 * Options used for processing the uploaded media files
		 *
		 * When uploading a file, a preview image for that file is generated if
		 * possible (especially for images). You can configure certain options
		 * for the generated images, namely the quality of those images with
		 *
		 *  array(
		 *  	'image' => array(
		 *  		'jpeg' => array(
		 *  			'quality' => 75
		 *  		),
		 *  		'png' => array(
		 *  			'quality' => 9
		 *  		),
		 *  	)
		 *  )
		 *
		 * @param array Multi-dimendional list of configuration options
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		$options = $config->get( 'controller/extjs/media/standard/options', array() );

		/** controller/extjs/media/standard/enablecheck
		 * Enables checking uploaded files if they are valid and not part of an attack
		 *
		 * This configuration option is for unit testing only! Please don't disable
		 * the checks for uploaded files in production environments as this
		 * would give attackers the possibility to infiltrate your installation!
		 *
		 * @param boolean True to enable, false to disable
		 * @since 2014.03
		 * @category Developer
		 */
		if( $config->get( 'controller/extjs/media/standard/enablecheck', true ) ) {
			$this->checkFileUpload( $fileinfo['tmp_name'], $fileinfo['error'] );
		}


		$filename = md5( $fileinfo['name'] . microtime( true ) );
		$mediaFile = \Aimeos\MW\Media\Factory::get( $fileinfo['tmp_name'], $options );

		$item = $this->getManager()->createItem();
		$item->setDomain( $params->domain );
		$item->setLabel( basename( $fileinfo['name'] ) );
		$item->setMimeType( $mediaFile->getMimetype() );


		if( $mediaFile instanceof \Aimeos\MW\Media\Image\Iface )
		{
			$item->setUrl( $this->storeImage( $mediaFile, 'files', $params->domain, $filename ) );
			$item->setPreview( $this->storeImage( $mediaFile, 'preview', $params->domain, $filename ) );
		}
		else
		{
			$item->setUrl( $this->storeFile( $mediaFile, 'files', $params->domain, $filename ) );
			$item->setPreview( $this->getMimeIcon( $mediaFile->getMimetype() ) );
		}

		unlink( $fileinfo['tmp_name'] );


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
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "string", "name" => "domain", "optional" => false ),
				),
				"returns" => "array",
		);

		return $smd;
	}


	/**
	 * Returns the manager the controller is using.
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected function getManager()
	{
		if( $this->manager === null ) {
			$this->manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'media' );
		}

		return $this->manager;
	}


	/**
	 * Returns the prefix for searching items
	 *
	 * @return string MShop search key prefix
	 */
	protected function getPrefix()
	{
		return 'media';
	}


	/**
	 * Transforms ExtJS values to be suitable for storing them
	 *
	 * @param \stdClass $entry Entry object from ExtJS
	 * @return \stdClass Modified object
	 */
	protected function transformValues( \stdClass $entry )
	{
		if( isset( $entry->{'media.languageid'} ) && $entry->{'media.languageid'} === '' ) {
			$entry->{'media.languageid'} = null;
		}

		return $entry;
	}


	/**
	 * Checks if the file is a valid uploaded file
	 *
	 * @param string $filename Path to the file that should be checked
	 * @param integer $errcode Error code from file upload
	 * @throws \Aimeos\Controller\ExtJS\Exception If file upload isn't valid or the error code represents an error state
	 */
	protected function checkFileUpload( $filename, $errcode )
	{
		if( is_uploaded_file( $filename ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( 'File was not uploaded' );
		}

		switch( $errcode )
		{
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new \Aimeos\Controller\ExtJS\Exception( 'The uploaded file exceeds the max. allowed filesize' );
			case UPLOAD_ERR_PARTIAL:
				throw new \Aimeos\Controller\ExtJS\Exception( 'The uploaded file was only partially uploaded' );
			case UPLOAD_ERR_NO_FILE:
				throw new \Aimeos\Controller\ExtJS\Exception( 'No file was uploaded' );
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new \Aimeos\Controller\ExtJS\Exception( 'Temporary folder is missing' );
			case UPLOAD_ERR_CANT_WRITE:
				throw new \Aimeos\Controller\ExtJS\Exception( 'Failed to write file to disk' );
			case UPLOAD_ERR_EXTENSION:
				throw new \Aimeos\Controller\ExtJS\Exception( 'File upload stopped by extension' );
			default:
				throw new \Aimeos\Controller\ExtJS\Exception( 'Unknown upload error' );
		}
	}


	/**
	 * Returns the file extension for the given mime type.
	 *
	 * @param string $mimetype Mime type like "image/png"
	 * @return string|null File extension including the dot (e.g. ".png") or null if unknown
	 */
	protected function getFileExtension( $mimetype )
	{
		switch( $mimetype )
		{
			case 'application/pdf': return '.pdf';

			case 'image/gif': return '.gif';
			case 'image/jpeg': return '.jpg';
			case 'image/png': return '.png';
			case 'image/tiff': return '.tif';
		}

		return null;
	}


	/**
	 * Returns the relative path to the mime icon for the given mime type.
	 *
	 * @param string $mimetype Mime type like "image/png"
	 * @return string Relative path to the mime icon
	 */
	protected function getMimeIcon( $mimetype )
	{
		$config = $this->getContext()->getConfig();

		/** controller/extjs/media/standard/mimeicon/directory
		 * Directory that contains the icons for the different mime types
		 *
		 * If no preview image can be generated from an uploaded file, an icon
		 * for its mime type is displayed instead. The directory for the mime
		 * icons is structured by the general mime type (e.g. "image") as
		 * sub-directory and the specific name of the mime type (e.g. "jpeg")
		 * as file name.
		 *
		 * Avoid leading and trailing slashes for the upload directory string!
		 *
		 * @param string Path or URL to the base directory
		 * @since 2014.03
		 * @category Developer
		 */
		if( ( $mimedir = $config->get( 'controller/extjs/media/standard/mimeicon/directory' ) ) == null ) {
			return '';
		}

		/** controller/extjs/media/standard/mimeicon/extension
		 * File extension of the mime icon images
		 *
		 * If you would like to use different mime icons that are available in
		 * another file format, you have to change the file extension for the
		 * mime icons to the actual ones.
		 *
		 * Note: The configured file extension needs a leading dot!
		 *
		 * @param string File extension including a leading dot, e.g ".jpg"
		 * @since 2014.03
		 * @category Developer
		 */
		$ext = $config->get( 'controller/extjs/media/standard/mimeicon/extension', '.png' );

		return $mimedir . DIRECTORY_SEPARATOR . $mimetype . $ext;
	}


	/**
	 * Stores a binary file and returns it's new relative file name
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $mediaFile Media object
	 * @param string $type Type of the image like "preview" or "files"
	 * @param string $domain Domain the image belongs to, e.g. "product", "attribute", etc.
	 * @param string $filename Name of the new file without file extension
	 * @return string Relative path to the new file
	 * @throws \Aimeos\Controller\ExtJS\Exception If an error occurs
	 */
	protected function storeFile( \Aimeos\MW\Media\Iface $mediaFile, $type, $domain, $filename )
	{
		$fileext = $this->getFileExtension( $mediaFile->getMimetype() );
		$dest = "${type}/${domain}/${filename[0]}/${filename[1]}/${filename}${fileext}";

		$fs = $this->getContext()->getFilesystemManager()->get( 'fs-media' );
		$fs->writef( $dest, $mediaFile->getFilepath() );

		unlink( $file );

		return $dest;
	}


	/**
	 * Stores a scaled image and returns it's new file name.
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $mediaFile Media object
	 * @param string $type Type of the image like "preview" or "files"
	 * @param string $domain Domain the image belongs to, e.g. "product", "attribute", etc.
	 * @param string $filename Name of the new file without file extension
	 * @return string Relative path to the new file
	 * @throws \Aimeos\Controller\ExtJS\Exception If an error occurs
	 */
	protected function storeImage( \Aimeos\MW\Media\Image\Iface $mediaFile, $type, $domain, $filename )
	{
		$mimetype = $mediaFile->getMimetype();
		$config = $this->getContext()->getConfig();

		/** controller/extjs/media/standard/files/allowedtypes
		 * A list of image mime types that are allowed for uploaded image files
		 *
		 * The list of allowed image types must be explicitly configured for the
		 * uploaded image files. Trying to upload and store an image file not
		 * available in the list of allowed mime types will result in an exception.
		 *
		 * @param array List of image mime types
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */

		/** controller/extjs/media/standard/preview/allowedtypes
		 * A list of image mime types that are allowed for preview image files
		 *
		 * The list of allowed image types must be explicitly configured for the
		 * preview image files. Trying to create a preview image whose mime type
		 * is not available in the list of allowed mime types will result in an
		 * exception.
		 *
		 * @param array List of image mime types
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		$default = array( 'image/jpeg', 'image/png', 'image/gif' );
		$allowed = $config->get( 'controller/extjs/media/standard/' . $type . '/allowedtypes', $default );

		if( in_array( $mimetype, $allowed ) === false )
		{
			if( ( $defaulttype = reset( $allowed ) ) !== false ) {
				$mimetype = $defaulttype;
			} else {
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'No allowed image types configured for "%1$s"', $type ) );
			}
		}


		/** controller/extjs/media/standard/files/maxwidth
		 * Maximum width of the uploaded images
		 *
		 * The uploaded image files are scaled down if their width exceeds the
		 * configured width of pixels. If the image width in smaller than the
		 * configured one, no scaling happens. In case of a value of null or if
		 * no configuration for that option is available, the image width isn't
		 * scaled at all.
		 *
		 * The width/height ratio of the image is always kept.
		 *
		 * @param integer|null Width in pixel or null for no scaling
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */

		/** controller/extjs/media/standard/preview/maxwidth
		 * Maximum width of the preview images
		 *
		 * The preview image files are created with the configured width in
		 * pixel. If the original image width in smaller than the one configured
		 * for the preview image, the width of the original image is used. In
		 * case of a value of null or if no configuration for that option is
		 * available, the width of the preview image is the same as the width of
		 * the original image.
		 *
		 * The width/height ratio of the preview image is always the same as for
		 * the original image.
		 *
		 * @param integer|null Width in pixel or null for no scaling
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		$maxwidth = $config->get( 'controller/extjs/media/standard/' . $type . '/maxwidth', null );

		/** controller/extjs/media/standard/files/maxheight
		 * Maximum height of the uploaded images
		 *
		 * The uploaded image files are scaled down if their height exceeds the
		 * configured height of pixels. If the image height in smaller than the
		 * configured one, no scaling happens. In case of a value of null or if
		 * no configuration for that option is available, the image width isn't
		 * scaled at all.
		 *
		 * The width/height ratio of the image is always kept.
		 *
		 * @param integer|null Height in pixel or null for no scaling
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */

		/** controller/extjs/media/standard/preview/maxheight
		 * Maximum height of the preview images
		 *
		 * The preview image files are created with the configured width in
		 * pixel. If the original image height in smaller than the one configured
		 * for the preview image, the height of the original image is used. In
		 * case of a value of null or if no configuration for that option is
		 * available, the height of the preview image is the same as the height
		 * of the original image.
		 *
		 * The width/height ratio of the preview image is always the same as for
		 * the original image.
		 *
		 * @param integer|null Height in pixel or null for no scaling
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		$maxheight = $config->get( 'controller/extjs/media/standard/' . $type . '/maxheight', null );


		/** controller/extjs/media/default/tempdir
		 * Directory for storing temporary files
		 *
		 * To scale images, temporary files must be created. This configuration
		 * option should point to a directory where the application can store
		 * generated files. If not configured, the temp directory of the
		 * operating system will be used.
		 *
		 * @param string Absolute path to the temp directory
		 * @since 2016.01
		 * @category Developer
		 */
		$tempdir = $config->get( 'controller/extjs/media/default/tempdir', sys_get_temp_dir() );


		if( ( $file = tempnam( $tempdir, 'ai' ) ) === false )
		{
			$msg = sprintf( 'Unable to create file in "%1$s"', sys_get_temp_dir() );
			throw new \Aimeos\Controller\ExtJS\Exception( $msg );
		}

		$mediaFile->scale( $maxwidth, $maxheight );
		$mediaFile->save( $file, $mimetype );

		$fileext = $this->getFileExtension( $mimetype );
		$dest = "${type}/${domain}/${filename[0]}/${filename[1]}/${filename}${fileext}";

		$fs = $this->getContext()->getFilesystemManager()->get( 'fs-media' );
		$fs->writef( $dest, $file );

		unlink( $file );

		return $dest;
	}
}
