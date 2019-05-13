<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Media;


/**
 * Common media controller methods
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	implements \Aimeos\Controller\Common\Media\Iface
{
	private $context;


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$this->context = $context;
	}


	/**
	 * Stores the uploaded file and adds the references to the media item
	 *
	 * {inheritDoc}
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item to add the file references to
	 * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file
	 * @param string $fsname Name of the file system to store the files at
	 * @return \Aimeos\MShop\Media\Item\Iface Added media item
	 */
	public function add( \Aimeos\MShop\Media\Item\Iface $item, \Psr\Http\Message\UploadedFileInterface $file, $fsname = 'fs-media' )
	{
		$this->checkFileUpload( $file );
		$media = $this->getMediaFile( $file->getStream() );

		if( $media instanceof \Aimeos\MW\Media\Image\Iface )
		{
			$item = $this->addImages( $item, $media, md5( $file->getClientFilename() ), $fsname );
		}
		else
		{
			$mimetype = $media->getMimeType();
			$filepath = $this->getFilePath( $file->getClientFilename(), 'files', $mimetype );

			$this->store( $filepath, $media->save(), $fsname );
			$item->setUrl( $filepath )->setPreview( $this->getMimeIcon( $mimetype ) )->setMimeType( $mimetype );
		}

		return $item->setLabel( $item->getLabel() ?: basename( $file->getClientFilename() ) );
	}


	/**
	 * Copies the media item and the referenced files
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be copied
	 * @param string $fsname Name of the file system to delete the files from
	 * @return \Aimeos\MShop\Media\Item\Iface Copied media item with new files
	 */
	public function copy( \Aimeos\MShop\Media\Item\Iface $item, $fsname = 'fs-media' )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'media' );

		$search = $manager->createSearch()->setSlice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'media.url', $item->getUrl() ) );

		if( count( $manager->searchItems( $search ) ) > 0 ) {
			return $item;
		}

		$fs = $this->context->getFilesystemManager()->get( $fsname );
		$preview = $item->getPreview();
		$path = $item->getUrl();

		if( $fs->has( $path ) )
		{
			$newPath = $this->getFilePath( $path, 'files', $item->getMimeType() );
			$fs->copy( $path, $newPath );
			$item->setUrl( $newPath );
		}

		if( $fs->has( $preview ) )
		{
			try
			{
				$newPath = $this->getFilePath( $preview, 'preview', pathinfo( $preview, PATHINFO_EXTENSION ) );
				$fs->copy( $preview, $newPath );
				$item->setPreview( $newPath );
			}
			catch( \Aimeos\MW\Filesystem\Exception $e ) {} // mime icons can't be copied
		}

		return $item;
	}


	/**
	 * Deletes the files of the media item
	 *
	 * {inheritDoc}
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be deleted
	 * @param string $fsname Name of the file system to delete the files from
	 */
	public function delete( \Aimeos\MShop\Media\Item\Iface $item, $fsname = 'fs-media' )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'media' );
		$search = $manager->createSearch()->setSlice( 0, 2 );
		$search->setConditions( $search->compare( '==', 'media.url', $item->getUrl() ) );

		if( count( $manager->searchItems( $search ) ) > 1 ) {
			return $item->setUrl( '' )->setPreview( '' );
		}

		$fs = $this->context->getFilesystemManager()->get( $fsname );
		$preview = $item->getPreview();
		$path = $item->getUrl();

		if( $path !== '' && $fs->has( $path ) ) {
			$fs->rm( $path );
		}

		try
		{
			if( $preview !== '' && $fs->has( $preview ) ) {
				$fs->rm( $preview );
			}
		}
		catch( \Exception $e ) { ; } // Can be a mime icon with relative path

		return $item->setUrl( '' )->setPreview( '' );
	}


	/**
	 * Rescales the files (original and preview) referenced by the media item
	 *
	 * The height/width configuration for scaling and which one should be scaled is used from
	 * - controller/common/media/standard/<files|preview>/maxheight
	 * - controller/common/media/standard/<files|preview>/maxwidth
	 * - controller/common/media/standard/<files|preview>/scale
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be scaled
	 * @param string $fsname Name of the file system to rescale the files from
	 * @return \Aimeos\MShop\Media\Item\Iface Rescaled media item
	 */
	public function scale( \Aimeos\MShop\Media\Item\Iface $item, $fsname = 'fs-media' )
	{
		$path = $item->getUrl();
		$media = $this->getMediaFile( $this->getFileContent( $path, $fsname ) );

		if( !( $media instanceof \Aimeos\MW\Media\Image\Iface ) ) {
			return $item;
		}

		return $this->addImages( $item, $media, $path, $fsname );
	}


	/**
	 * Adds original image and preview images to the media item
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item which will contains the image URLs afterwards
	 * @param \Aimeos\MW\Media\Image\Iface $media Image object to scale
	 * @param string $path Path to the file or URL, empty or random for uploaded files
	 * @param string $fsname File system name the file is located at
	 * @return \Aimeos\MShop\Media\Item\Iface Updated media item with URLs
	 */
	protected function addImages( \Aimeos\MShop\Media\Item\Iface $item, \Aimeos\MW\Media\Image\Iface $media, $path, $fsname )
	{
		$mime = $this->getMimeType( $media, 'files' );
		$mediaFile = $this->scaleImage( $media, 'files' );

		// Don't overwrite original files that are stored in linked directories
		$filepath = ( strncmp( $path, 'files/', 6 ) !== 0 ? $this->getFilePath( $path, 'files', $mime ) : $path );

		$this->store( $filepath, $mediaFile->save( null, $mime ), $fsname );
		$item = $item->setUrl( $filepath )->setMimeType( $mime );
		unset( $mediaFile );


		$path = $item->getPreview();
		$mime = $this->getMimeType( $media, 'preview' );
		$mediaFile = $this->scaleImage( $media, 'preview' );

		// Don't try to overwrite mime icons that are stored in another directory
		$filepath = ( strncmp( $path, 'preview/', 8 ) !== 0 ? $this->getFilePath( $path, 'preview', $mime ) : $path );

		$this->store( $filepath, $mediaFile->save( null, $mime ), $fsname );
		$item = $item->setPreview( $filepath );
		unset( $mediaFile );

		return $item;
	}


	/**
	 * Checks if an error during upload occured
	 *
	 * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file
	 * @throws \Aimeos\Controller\Common\Exception If an error occured during upload
	 */
	protected function checkFileUpload( \Psr\Http\Message\UploadedFileInterface $file )
	{
		if( $file->getError() !== UPLOAD_ERR_OK )
		{
			switch( $file->getError() )
			{
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new \Aimeos\Controller\Common\Exception( 'The uploaded file exceeds the max. allowed filesize' );
				case UPLOAD_ERR_PARTIAL:
					throw new \Aimeos\Controller\Common\Exception( 'The uploaded file was only partially uploaded' );
				case UPLOAD_ERR_NO_FILE:
					throw new \Aimeos\Controller\Common\Exception( 'No file was uploaded' );
				case UPLOAD_ERR_NO_TMP_DIR:
					throw new \Aimeos\Controller\Common\Exception( 'Temporary folder is missing' );
				case UPLOAD_ERR_CANT_WRITE:
					throw new \Aimeos\Controller\Common\Exception( 'Failed to write file to disk' );
				case UPLOAD_ERR_EXTENSION:
					throw new \Aimeos\Controller\Common\Exception( 'File upload stopped by extension' );
				default:
					throw new \Aimeos\Controller\Common\Exception( 'Unknown upload error' );
			}
		}
	}


	/**
	 * Returns the context item
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context item
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the file content of the file or URL
	 *
	 * @param string $path Path to the file or URL
	 * @param string $fsname File system name the file is located at
	 * @return string File content
	 * @throws \Aimeos\Controller\Common\Exception If no file is found
	 */
	protected function getFileContent( $path, $fsname )
	{
		if( $path !== '' )
		{
			if( preg_match( '#^[a-zA-Z]{1,10}://#', $path ) === 1 )
			{
				if( ( $content = @file_get_contents( $path ) ) === false )
				{
					$msg = sprintf( 'Downloading file "%1$s" failed', $path );
					throw new \Aimeos\Controller\Common\Exception( $msg );
				}

				return $content;
			}

			$fs = $this->context->getFilesystemManager()->get( $fsname );

			if( $fs->has( $path ) !== false ) {
				return $fs->read( $path );
			}
		}

		throw new \Aimeos\Controller\Common\Exception( sprintf( 'File "%1$s" not found', $path ) );
	}


	/**
	 * Creates a new file path from the given arguments and random values
	 *
	 * @param string $filename Original file name, can contain the path as well
	 * @param string $type File type, i.e. "files" or "preview"
	 * @param string $mimeext Mime type or extension of the file
	 * @return string New file name including the file path
	 */
	protected function getFilePath( $filename, $type, $mimeext )
	{
		/** controller/common/media/standard/extensions
		 * Available files extensions for mime types of uploaded files
		 *
		 * Uploaded files should have the right file extension (e.g. ".jpg" for
		 * JPEG images) so files are recognized correctly if downloaded by users.
		 * The extension of the uploaded file can't be trusted and only its mime
		 * type can be determined automatically. This configuration setting
		 * provides the file extensions for the configured mime types. You can
		 * add more mime type / file extension combinations if required.
		 *
		 * @param array Associative list of mime types as keys and file extensions as values
		 * @since 2018.04
		 * @category Developer
		 */
		$list = $this->context->getConfig()->get( 'controller/common/media/standard/extensions', [] );

		$filename = md5( $filename . getmypid() . microtime( true ) );
		$ext = isset( $list[$mimeext] ) ? '.' . $list[$mimeext] : ( ctype_alpha( $mimeext ) ? '.' . $mimeext : '' );

		return "${type}/${filename[0]}/${filename[1]}/${filename}${ext}";
	}


	/**
	 * Returns the media object for the given file name
	 *
	 * @param string $file Path to the file or file content
	 * @return \Aimeos\MW\Media\Iface Media object
	 */
	protected function getMediaFile( $file )
	{
		/** controller/common/media/standard/options
		 * Options used for processing the uploaded media files
		 *
		 * When uploading a file, a preview image for that file is generated if
		 * possible (especially for images). You can configure certain options
		 * for the generated images, namely the implementation of the scaling
		 * algorithm and the quality of the resulting images with
		 *
		 *  array(
		 *  	'image' => array(
		 *  		'name' => 'Imagick',
		 *  		'quality' => 75,
		 *  	)
		 *  )
		 *
		 * @param array Multi-dimendional list of configuration options
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */
		$options = $this->context->getConfig()->get( 'controller/common/media/standard/options', [] );

		return \Aimeos\MW\Media\Factory::get( $file, $options );
	}


	/**
	 * Returns the relative path to the mime icon for the given mime type.
	 *
	 * @param string $mimetype Mime type like "image/png"
	 * @return string Relative path to the mime icon
	 */
	protected function getMimeIcon( $mimetype )
	{
		$config = $this->context->getConfig();

		/** controller/common/media/standard/mimeicon/directory
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
		 * @since 2016.01
		 * @category Developer
		 */
		if( ( $mimedir = $config->get( 'controller/common/media/standard/mimeicon/directory' ) ) == null ) {
			return '';
		}

		/** controller/common/media/standard/mimeicon/extension
		 * File extension of the mime icon images
		 *
		 * If you would like to use different mime icons that are available in
		 * another file format, you have to change the file extension for the
		 * mime icons to the actual ones.
		 *
		 * Note: The configured file extension needs a leading dot!
		 *
		 * @param string File extension including a leading dot, e.g ".jpg"
		 * @since 2016.01
		 * @category Developer
		 */
		$ext = $config->get( 'controller/common/media/standard/mimeicon/extension', '.png' );

		return $mimedir . DIRECTORY_SEPARATOR . $mimetype . $ext;
	}


	/**
	 * Returns the mime type for the new image
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $media Media object
	 * @param string $type Type of the image like "preview" or "files"
	 * @return string New mime type
	 * @throws \Aimeos\Controller\Common\Exception If no mime types are configured
	 */
	protected function getMimeType( \Aimeos\MW\Media\Image\Iface $media, $type )
	{
		$mimetype = $media->getMimetype();
		$config = $this->context->getConfig();

		/** controller/common/media/standard/files/allowedtypes
		 * A list of image mime types that are allowed for uploaded image files
		 *
		 * The list of allowed image types must be explicitly configured for the
		 * uploaded image files. Trying to upload and store an image file not
		 * available in the list of allowed mime types will result in an exception.
		 *
		 * @param array List of image mime types
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */

		/** controller/common/media/standard/preview/allowedtypes
		 * A list of image mime types that are allowed for preview image files
		 *
		 * The list of allowed image types must be explicitly configured for the
		 * preview image files. Trying to create a preview image whose mime type
		 * is not available in the list of allowed mime types will result in an
		 * exception.
		 *
		 * @param array List of image mime types
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */
		$default = array( 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml' );
		$allowed = $config->get( 'controller/common/media/standard/' . $type . '/allowedtypes', $default );

		if( in_array( $mimetype, $allowed ) === false )
		{
			if( ( $defaulttype = reset( $allowed ) ) === false ) {
				throw new \Aimeos\Controller\Common\Exception( sprintf( 'No allowed image types configured for "%1$s"', $type ) );
			}

			return $defaulttype;
		}

		return $mimetype;
	}


	/**
	 * Scales the image according to the configuration settings
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $media Media object
	 * @param string $type Type of the image like "preview" or "files"
	 * @return \Aimeos\MW\Media\Image\Iface Scaled media object
	 */
	protected function scaleImage( \Aimeos\MW\Media\Image\Iface $media, $type )
	{
		$config = $this->context->getConfig();

		/** controller/common/media/standard/files/maxwidth
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
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */

		/** controller/common/media/standard/preview/maxwidth
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
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */
		$maxwidth = $config->get( 'controller/common/media/standard/' . $type . '/maxwidth', null );

		/** controller/common/media/standard/files/maxheight
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
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */

		/** controller/common/media/standard/preview/maxheight
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
		 * @since 2016.01
		 * @category Developer
		 * @category User
		 */
		$maxheight = $config->get( 'controller/common/media/standard/' . $type . '/maxheight', null );

		/** controller/common/media/standard/files/force-size
		 * Force exact image size for the uploaded images
		 *
		 * This configuration forces the output image to have exact the width
		 * and height specified in maxwidth / maxheight configuration options
		 * by scaling and cropping the original image.
		 *
		 * @param integer True to enable the feature, false to disable it
		 * @since 2018.10
		 * @category Developer
		 * @category User
		 * @see controller/common/media/standard/preview/force-size
		 */

		/** controller/common/media/standard/preview/force-size
		 * Force exact image size for the preview images
		 *
		 * This configuration forces the output image to have exact the width
		 * and height specified in maxwidth / maxheight configuration options
		 * by scaling and cropping the original image.
		 *
		 * @param integer True to enable the feature, false to disable it
		 * @since 2018.10
		 * @category Developer
		 * @category User
		 * @see controller/common/media/standard/files/force-size
		 */
		$fit = (bool) $config->get( 'controller/common/media/standard/' . $type . '/force-size', false );

		if( $maxheight || $maxwidth ) {
			return $media->scale( $maxwidth, $maxheight, !$fit );
		}

		return $media;
	}


	/**
	 * Stores the file content
	 *
	 * @param string $filepath Path of the new file
	 * @param string $content File content
	 * @param string $fsname Name of the file system to store the files at
	 * @return \Aimeos\Controller\Common\Media\Iface Self object for fluent interface
	 */
	protected function store( $filepath, $content, $fsname )
	{
		$this->context->getFilesystemManager()->get( $fsname )->write( $filepath, $content );
		return $this;
	}


	/**
	 * Stores the file content
	 *
	 * @param string $content File content
	 * @param string $fsname Name of the file system to store the files at
	 * @param string $filepath Path of the new file
	 * @param string $oldpath Path of the old file
	 * @deprecated 2020.01
	 */
	protected function storeFile( $content, $fsname, $filepath, $oldpath )
	{
		$fs = $this->context->getFilesystemManager()->get( $fsname );

		try
		{
			if( $oldpath !== '' && $oldpath !== $filepath && $fs->has( $oldpath ) ) {
				$fs->rm( $oldpath );
			}
		}
		catch( \Aimeos\MW\Filesystem\Exception $e ) {} // continue if removing file fails

		$fs->write( $filepath, $content );
	}
}
