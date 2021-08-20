<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Media;


/**
 * Common media controller methods.
 *
 * @package Controller
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return null
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context );

	/**
	 * Stores the uploaded file and adds the references to the media item
	 *
	 * Uploaded files will be moved to the storage depending on the
	 * configuration. For images, a preview will be created too according to
	 * the settings while for other files a mime icon will be set as preview
	 * image.
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item to add the file references to
	 * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file
	 * @param string $fsname Name of the file system to store the files at
	 * @return \Aimeos\MShop\Media\Item\Iface Added media item
	 */
	public function add( \Aimeos\MShop\Media\Item\Iface $item, \Psr\Http\Message\UploadedFileInterface $file, string $fsname = 'fs-media' ) : \Aimeos\MShop\Media\Item\Iface;

	/**
	 * Stores the uploaded preview and adds the references to the media item
	 *
	 * This method allows uploading a different image for preview than the one
	 * that will be created automatically by add() method. It's especially
	 * useful for files where no preview image can be created automatically
	 * like videos. Depending on the configuration, several preview files in
	 * different sizes are created.
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item to add the file references to
	 * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file
	 * @param string $fsname Name of the file system to store the files at
	 * @return \Aimeos\MShop\Media\Item\Iface Added media item
	 */
	public function addPreview( \Aimeos\MShop\Media\Item\Iface $item, \Psr\Http\Message\UploadedFileInterface $file, string $fsname = 'fs-media' ) : \Aimeos\MShop\Media\Item\Iface;

	/**
	 * Copies the media item and the referenced files
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be copied
	 * @param string $fsname Name of the file system to delete the files from
	 * @return \Aimeos\MShop\Media\Item\Iface Copied media item with new files
	 */
	public function copy( \Aimeos\MShop\Media\Item\Iface $item, string $fsname = 'fs-media' ) : \Aimeos\MShop\Media\Item\Iface;

	/**
	 * Deletes the files referenced by the media item
	 *
	 * This method removes the files referenced by the media item from the
	 * storage. The media item itself is NOT deleted!
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be deleted
	 * @param string $fsname Name of the file system to delete the files from
	 * @return \Aimeos\MShop\Media\Item\Iface Media item with deleted files
	 */
	public function delete( \Aimeos\MShop\Media\Item\Iface $item, string $fsname = 'fs-media' ) : \Aimeos\MShop\Media\Item\Iface;

	/**
	 * Rescales the files (original and preview) referenced by the media item
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be scaled
	 * @param string $fsname Name of the file system to rescale the files from
	 * @return \Aimeos\MShop\Media\Item\Iface Rescaled media item
	 */
	public function scale( \Aimeos\MShop\Media\Item\Iface $item, string $fsname = 'fs-media' ) : \Aimeos\MShop\Media\Item\Iface;
}
