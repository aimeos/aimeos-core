<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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

		$tmpname = $this->getTempFileName();
		$file->moveTo( $tmpname );

		$mediaFile = $this->getMediaFile( $tmpname );
		$filename = md5( $file->getClientFilename() . getmypid() . microtime( true ) );

		if( $mediaFile instanceof \Aimeos\MW\Media\Image\Iface )
		{
			$item->setUrl( $this->storeImage( $mediaFile, 'files', $filename, $fsname ) );
			$item->setPreview( $this->storeImage( $mediaFile, 'preview', $filename, $fsname ) );
			$item->setMimeType( $this->getMimetype( $mediaFile, 'files' ) );
		}
		else
		{
			$item->setUrl( $this->storeFile( $mediaFile, 'files', $filename, $fsname ) );
			$item->setPreview( $this->getMimeIcon( $mediaFile->getMimetype() ) );
			$item->setMimeType( $mediaFile->getMimetype() );
		}

		$item->setLabel( basename( $file->getClientFilename() ) );
		$this->deleteFile( $tmpname );
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

		$item->setUrl( null );

		$path = $item->getPreview();
		if( $path !== '' && $fs->has( $path ) ) {
			$fs->rm( $path );
		}

		$item->setPreview( null );
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
	 * Removes the file from the file system
	 *
	 * @param string $path Path to the file
	 */
	protected function deleteFile( $path )
	{
		unlink( $path );
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
	 * Returns the media object for the given file name
	 *
	 * @param string $filename Path and name to the file
	 * @return \Aimeos\MW\Media\Image\Iface Media object
	 */
	protected function getMediaFile( $filename )
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
		$options = $this->context->getConfig()->get( 'controller/common/media/standard/options', array() );

		return \Aimeos\MW\Media\Factory::get( $filename, $options );
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
	 * @param \Aimeos\MW\Media\Image\Iface $mediaFile Media object
	 * @param string $type Type of the image like "preview" or "files"
	 * @return string New mime type
	 * @throws \Aimeos\Controller\Common\Exception If no mime types are configured
	 */
	protected function getMimeType( \Aimeos\MW\Media\Image\Iface $mediaFile, $type )
	{
		$mimetype = $mediaFile->getMimetype();
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
	 * Returns a file name for a new temporary file
	 *
	 * @throws \Aimeos\Controller\Common\Exception
	 * @return string File path and name
	 */
	protected function getTempFileName()
	{
		$config = $this->context->getConfig();

		/** controller/common/media/standard/tempdir
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
		$tempdir = $config->get( 'controller/common/media/standard/tempdir', sys_get_temp_dir() );

		if( !is_dir( $tempdir ) && mkdir( $tempdir, 0750, true ) === false )
		{
			$msg = sprintf( 'Unable to create directory "%1$s"', $tempdir );
			throw new \Aimeos\Controller\Common\Exception( $msg );
		}

		if( ( $file = tempnam( $tempdir, 'ai' ) ) === false )
		{
			$msg = sprintf( 'Unable to create file in "%1$s"', $tempdir );
			throw new \Aimeos\Controller\Common\Exception( $msg );
		}

		return $file;
	}


	/**
	 * Scales the image according to the configuration settings
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $mediaFile Media object
	 * @param string $type Type of the image like "preview" or "files"
	 */
	protected function scaleImage( \Aimeos\MW\Media\Image\Iface $mediaFile, $type )
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

		$mediaFile->scale( $maxwidth, $maxheight );
	}


	/**
	 * Stores a binary file and returns it's new relative file name
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $mediaFile Media object
	 * @param string $type Type of the image like "preview" or "files"
	 * @param string $filename Name of the new file without file extension
	 * @param string Name of the file system to store the files at
	 * @return string Relative path to the new file
	 * @throws \Aimeos\Controller\Common\Exception If an error occurs
	 */
	protected function storeFile( \Aimeos\MW\Media\Iface $mediaFile, $type, $filename, $fsname )
	{
		$file = $mediaFile->getFilepath();
		$fileext = $this->getFileExtension( $mediaFile->getMimetype() );
		$dest = "${type}/${filename[0]}/${filename[1]}/${filename}${fileext}";

		$this->context->getFilesystemManager()->get( $fsname )->writef( $dest, $file );

		return $dest;
	}


	/**
	 * Stores a scaled image and returns it's new file name.
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $mediaFile Media object
	 * @param string $type Type of the image like "preview" or "files"
	 * @param string $filename Name of the new file without file extension
	 * @param string $fsname Name of the file system to store the files at
	 * @return string Relative path to the new file
	 * @throws \Aimeos\Controller\Common\Exception If an error occurs
	 */
	protected function storeImage( \Aimeos\MW\Media\Image\Iface $mediaFile, $type, $filename, $fsname )
	{
		$tmpfile = $this->getTempFileName();
		$mimetype = $this->getMimeType( $mediaFile, $type );

		$this->scaleImage( $mediaFile, $type );
		$mediaFile->save( $tmpfile, $mimetype );

		$fileext = $this->getFileExtension( $mimetype );
		$dest = "${type}/${filename[0]}/${filename[1]}/${filename}${fileext}";

		$this->context->getFilesystemManager()->get( $fsname )->writef( $dest, $tmpfile );

		unlink( $tmpfile );

		return $dest;
	}
}
