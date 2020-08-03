<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
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
	public function add( \Aimeos\MShop\Media\Item\Iface $item, \Psr\Http\Message\UploadedFileInterface $file, string $fsname = 'fs-media' ) : \Aimeos\MShop\Media\Item\Iface
	{
		$this->checkFileUpload( $file );

		$media = $this->getMediaFile( $file->getStream() );
		$mimetype = $this->getMimeType( $media, 'files' );

		if( $media instanceof \Aimeos\MW\Media\Image\Iface )
		{
			$item = $this->addImages( $item, $media, null, $fsname );
		}
		else
		{
			$filepath = $this->getFilePath( $file->getClientFilename() ?: rand(), 'files', $mimetype );

			$this->store( $filepath, $media->save(), $fsname );
			$item->setUrl( $filepath )->setPreviews( [1 => $this->getMimeIcon( $mimetype )] )->setMimeType( $mimetype );
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
	public function copy( \Aimeos\MShop\Media\Item\Iface $item, string $fsname = 'fs-media' ) : \Aimeos\MShop\Media\Item\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context, 'media' );

		$search = $manager->createSearch()->setSlice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'media.url', $item->getUrl() ) );

		$item = $manager->searchItems( $search )->first( $item )->setId( null );
		$fs = $this->context->getFilesystemManager()->get( $fsname );
		$previews = $item->getPreviews();
		$path = $item->getUrl();

		if( $fs->has( $path ) )
		{
			$newPath = $this->getFilePath( $path, 'files', $item->getMimeType() );
			$fs->copy( $path, $newPath );
			$item->setUrl( $newPath );
		}

		foreach( $previews as $size => $preview )
		{
			if( $fs->has( $preview ) )
			{
				try
				{
					$newPath = $this->getFilePath( $preview, 'preview', pathinfo( $preview, PATHINFO_EXTENSION ) );
					$fs->copy( $preview, $newPath );
					$previews[$size] = $newPath;
				}
				catch( \Aimeos\MW\Filesystem\Exception $e ) {} // mime icons can't be copied
			}
		}

		return $item->setPreviews( $previews );
	}


	/**
	 * Deletes the files of the media item
	 *
	 * {inheritDoc}
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be deleted
	 * @param string $fsname Name of the file system to delete the files from
	 * @return \Aimeos\MShop\Media\Item\Iface Media item with deleted files
	 */
	public function delete( \Aimeos\MShop\Media\Item\Iface $item, string $fsname = 'fs-media' ) : \Aimeos\MShop\Media\Item\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context, 'media' );
		$search = $manager->createSearch()->setSlice( 0, 2 );
		$search->setConditions( $search->compare( '==', 'media.url', $item->getUrl() ) );

		if( count( $manager->searchItems( $search ) ) > 1 ) {
			return $item->setUrl( '' )->setPreview( '' );
		}

		$mimedir = (string) $this->context->getConfig()->get( 'controller/common/media/standard/mimeicon/directory' );
		$fs = $this->context->getFilesystemManager()->get( $fsname );
		$mimelen = strlen( $mimedir );
		$path = $item->getUrl();

		if( $path !== '' && $fs->has( $path ) ) {
			$fs->rm( $path );
		}

		foreach( $item->getPreviews() as $preview )
		{
			try
			{
				if( $preview !== '' && strncmp( $preview, $mimedir, $mimelen ) !== 0 && $fs->has( $preview ) ) {
					$fs->rm( $preview );
				}
			}
			catch( \Exception $e ) { ; } // continue if removing file fails
		}

		return $item->setUrl( '' )->setPreviews( [] )->deletePropertyItems( $item->getPropertyItems()->toArray() );
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
	public function scale( \Aimeos\MShop\Media\Item\Iface $item, string $fsname = 'fs-media' ) : \Aimeos\MShop\Media\Item\Iface
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
	 * @param string|null $path Path to the file or URL, empty or random for uploaded files
	 * @param string $fsname File system name the file is located at
	 * @return \Aimeos\MShop\Media\Item\Iface Updated media item with URLs
	 */
	protected function addImages( \Aimeos\MShop\Media\Item\Iface $item, \Aimeos\MW\Media\Image\Iface $media, ?string $path, string $fsname ) : \Aimeos\MShop\Media\Item\Iface
	{
		if( $path === null )
		{
			$path = $this->getFilePath( rand(), 'files', $media->getMimeType() );
			$this->store( $path, $media->save(), $fsname );
		}

		$previews = [];
		$mime = $this->getMimeType( $media, 'preview' );

		foreach( $this->createPreviews( $media ) as $type => $mediaFile )
		{
			$filepath = $this->getFilePath( rand(), 'preview', $media->getMimeType() );
			$this->store( $filepath, $mediaFile->save( null, $mime ), $fsname );
			$previews[$mediaFile->getWidth()] = $filepath;
			unset( $mediaFile );
		}

		return $item->setUrl( $path )->setPreviews( $previews )->setMimeType( $media->getMimeType() );
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
	 * Creates scaled images according to the configuration settings
	 *
	 * @param \Aimeos\MW\Media\Image\Iface $media Media object
	 * @return \Aimeos\MW\Media\Image\Iface[] Associative list of image width as keys and scaled media object as values
	 */
	protected function createPreviews( \Aimeos\MW\Media\Image\Iface $media ) : array
	{
		$list = [];
		$config = $this->context->getConfig();

		/** controller/common/media/standard/previews
		 * Scaling options for preview images
		 *
		 * For responsive images, several preview images of different sizes are
		 * generated. This setting controls how many preview images are generated,
		 * what's their maximum width and height and if the given width/height is
		 * enforced by cropping images that doesn't fit.
		 *
		 * The setting must consist of a list image size definitions like:
		 *
		 *  [
		 *    ['maxwidth' => 240, 'maxheight' => 320, 'force-size' => true],
		 *    ['maxwidth' => 720, 'maxheight' => 960, 'force-size' => false],
		 *    ['maxwidth' => 2160, 'maxheight' => 2880, 'force-size' => false],
		 *  ]
		 *
		 * "maxwidth" sets the maximum allowed width of the image whereas
		 * "maxheight" does the same for the maximum allowed height. If both
		 * values are given, the image is scaled proportionally so it fits into
		 * the box defined by both values. In case the image has different
		 * proportions than the specified ones and "force-size" is false, the
		 * image is resized to fit entirely into the specified box. One side of
		 * the image will be shorter than it would be possible by the specified
		 * box.
		 *
		 * If "force-size" is true, scaled images that doesn't fit into the
		 * given maximum width/height are centered and then cropped. By default,
		 * images aren't cropped.
		 *
		 * The values for "maxwidth" and "maxheight" can also be null or not
		 * used. In that case, the width or height or both is unbound. If none
		 * of the values are given, the image won't be scaled at all. If only
		 * one value is set, the image will be scaled exactly to the given width
		 * or height and the other side is scaled proportionally.
		 *
		 * @param array List of image size definitions
		 * @category Developer
		 * @category User
		 * @since 2019.07
		 */
		foreach( $config->get( 'controller/common/media/standard/previews', [] ) as $entry )
		{
			$maxwidth = ( isset( $entry['maxwidth'] ) ? (int) $entry['maxwidth'] : null );
			$maxheight = ( isset( $entry['maxheight'] ) ? (int) $entry['maxheight'] : null );
			$fit = ( isset( $entry['force-size'] ) ? (bool) $entry['force-size'] : false );

			if( $maxheight || $maxwidth )
			{
				$image = $media->scale( $maxwidth, $maxheight, !$fit );
				$list[$image->getWidth()] = $image;
			}
		}

		if( empty( $list ) )
		{
			$maxwidth = $config->get( 'controller/common/media/standard/preview/maxwidth', null );
			$maxheight = $config->get( 'controller/common/media/standard/preview/maxheight', null );
			$fit = (bool) $config->get( 'controller/common/media/standard/preview/force-size', false );

			if( $maxheight || $maxwidth )
			{
				$image = $media->scale( $maxwidth, $maxheight, !$fit );
				$list[$image->getWidth()] = $image;
			}
		}

		return $list;
	}


	/**
	 * Returns the context item
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context item
	 */
	protected function getContext() : \Aimeos\MShop\Context\Item\Iface
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
	protected function getFileContent( string $path, string $fsname ) : string
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
	protected function getFilePath( string $filename, string $type, string $mimeext ) : string
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

		$filename = trim( preg_replace( '/[^A-Za-z0-9]+/', '_', $filename ), '_' );
		$filename = substr( md5( $filename . getmypid() . microtime( true ) ), -8 ) . '_' . $filename;
		$ext = isset( $list[$mimeext] ) ? '.' . $list[$mimeext] : ( ctype_alpha( $mimeext ) ? '.' . $mimeext : '' );

		return "${type}/${filename[0]}/${filename[1]}/${filename}${ext}";
	}


	/**
	 * Returns the media object for the given file name
	 *
	 * @param string $file Path to the file or file content
	 * @return \Aimeos\MW\Media\Iface Media object
	 */
	protected function getMediaFile( string $file ) : \Aimeos\MW\Media\Iface
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
	protected function getMimeIcon( string $mimetype ) : string
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

		if( file_exists( $icon = $mimedir . DIRECTORY_SEPARATOR . $mimetype . $ext ) ) {
			return $icon;
		}

		return $mimedir . DIRECTORY_SEPARATOR . 'unknown.png';
	}


	/**
	 * Returns the mime type for the new image
	 *
	 * @param \Aimeos\MW\Media\Iface $media Media object
	 * @param string $type Type of the image like "preview" or "files"
	 * @return string New mime type
	 * @throws \Aimeos\Controller\Common\Exception If no mime types are configured
	 */
	protected function getMimeType( \Aimeos\MW\Media\Iface $media, string $type ) : string
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
	 * Stores the file content
	 *
	 * @param string $filepath Path of the new file
	 * @param string $content File content
	 * @param string $fsname Name of the file system to store the files at
	 * @return \Aimeos\Controller\Common\Media\Iface Self object for fluent interface
	 */
	protected function store( string $filepath, string $content, string $fsname ) : Iface
	{
		$this->context->getFilesystemManager()->get( $fsname )->write( $filepath, $content );
		return $this;
	}
}
