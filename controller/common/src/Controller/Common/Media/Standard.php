<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * @param \Psr\Http\Message\UploadedFileInterface Uploaded file
	 * @param string $fsname Name of the file system to store the files at
	 */
	public function add( \Aimeos\MShop\Media\Item\Iface $item, \Psr\Http\Message\UploadedFileInterface $file, $fsname = 'fs-media' )
	{
		$this->checkFileUpload( $file );
		$media = $this->getMediaFile( $file->getStream() );

		if( $media instanceof \Aimeos\MW\Media\Image\Iface )
		{
			$this->scaleImage( $media, 'files' );
			$mimetype = $this->getMimeType( $media, 'files' );
			$filepath = $this->getFilePath( $file->getClientFilename(), 'files', $mimetype );
			$this->storeFile( $media->save( null, $mimetype ), $fsname, $filepath, $item->getUrl() );
			$item->setUrl( $filepath );

			$this->scaleImage( $media, 'preview' );
			$mimeprev = $this->getMimeType( $media, 'preview' );
			$filepath = $this->getFilePath( $file->getClientFilename(), 'preview', $mimeprev );
			$this->storeFile( $media->save( null, $mimetype ), $fsname, $filepath, $item->getPreview() );
			$item->setPreview( $filepath );
		}
		else
		{
			$mimetype = $media->getMimeType();
			$item->setPreview( $this->getMimeIcon( $mimetype ) );

			$filepath = $this->getFilePath( $file->getClientFilename(), 'files', $mimetype );
			$this->storeFile( $media->save(), $fsname, $filepath, $item->getPreview() );
			$item->setUrl( $filepath );
		}

		$item->setLabel( basename( $file->getClientFilename() ) );
		$item->setMimeType( $mimetype );
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
		$fs = $this->context->getFilesystemManager()->get( $fsname );

		$path = $item->getUrl();
		if( $path !== '' && $fs->has( $path ) ) {
			$fs->rm( $path );
		}

		$item->setUrl( '' );

		try
		{
			$path = $item->getPreview();
			if( $path !== '' && $fs->has( $path ) ) {
				$fs->rm( $path );
			}
		}
		catch( \Exception $e ) { ; } // Can be a mime icon with relative path

		$item->setPreview( '' );
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
	 * @return void
	 */
	public function scale( \Aimeos\MShop\Media\Item\Iface $item, $fsname = 'fs-media' )
	{
		$path = $item->getUrl();
		$config = $this->context->getConfig();
		$media = $this->getMediaFile( $this->getFileContent( $path, $fsname ) );

		if( !( $media instanceof \Aimeos\MW\Media\Image\Iface ) ) {
			return;
		}

		if( (bool) $config->get( 'controller/common/media/standard/files/scale', false ) === true )
		{
			$mimetype = $this->getMimeType( $media, 'files' );
			$filepath = $this->getFilePath( $path, 'files', $mimetype );
			$this->storeFile( $media->save( null, $mimetype ), $fsname, $filepath, $path );
			$item->setUrl( $filepath );
		}

		if( (bool) $config->get( 'controller/common/media/standard/preview/scale', true ) === true )
		{
			$mimetype = $this->getMimeType( $media, 'preview' );
			$filepath = $this->getFilePath( $path, 'preview', $mimetype );
			$this->storeFile( $media->save( null, $mimetype ), $fsname, $filepath, $item->getPreview() );
			$item->setPreview( $filepath );
		}
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
				if( ( $content = file_get_contents( $path ) ) === false )
				{
					$msg = sprintf( 'Download file "%1$s" using file_get_contents failed', $path );
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
	 * @param string $mimetype Mime type of the file
	 * @return string New file name including the file path
	 */
	protected function getFilePath( $filename, $type, $mimetype )
	{
		switch( $mimetype )
		{
			case 'application/pdf': $ext = '.pdf'; break;

			case 'image/gif': $ext = '.gif'; break;
			case 'image/jpeg': $ext = '.jpg'; break;
			case 'image/png': $ext = '.png'; break;
			case 'image/tiff': $ext = '.tif'; break;

			default: $ext = '';
		}

		$filename = md5( $filename . getmypid() . microtime( true ) );

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
		$default = array( 'image/jpeg', 'image/png', 'image/gif' );
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
	 * @param \Aimeos\MW\Media\Image\Iface Scaled media object
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

		return $media->scale( $maxwidth, $maxheight );
	}


	/**
	 * Stores the file content
	 *
	 * @param string $content File content
	 * @param string $fsname Name of the file system to store the files at
	 * @param string $filepath Path of the new file
	 * @param string $oldpath Path of the old file
	 */
	protected function storeFile( $content, $fsname, $filepath, $oldpath )
	{
		$fs = $this->context->getFilesystemManager()->get( $fsname );

		if( $oldpath !== '' && $oldpath !== $filepath && $fs->has( $oldpath ) ) {
			$fs->rm( $oldpath );
		}

		$fs->write( $filepath, $content );
	}
}
