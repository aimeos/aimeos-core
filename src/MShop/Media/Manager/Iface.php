<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager;

use Psr\Http\Message\UploadedFileInterface;


/**
 * Generic interface for media managers.
 *
 * @package MShop
 * @subpackage Media
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Copies the media item and the referenced files
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be copied
	 * @return \Aimeos\MShop\Media\Item\Iface Copied media item with new files
	 */
	public function copy( \Aimeos\MShop\Media\Item\Iface $item ) : \Aimeos\MShop\Media\Item\Iface;


	/**
	 * Rescales the original file to preview files referenced by the media item
	 *
	 * The height/width configuration for scaling
	 * - mshop/media/<files|preview>/maxheight
	 * - mshop/media/<files|preview>/maxwidth
	 * - mshop/media/<files|preview>/force-size
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be scaled
	 * @param bool $force True to enforce creating new preview images
	 * @return \Aimeos\MShop\Media\Item\Iface Rescaled media item
	 */
	public function scale( \Aimeos\MShop\Media\Item\Iface $item, bool $force = false ) : \Aimeos\MShop\Media\Item\Iface;


	/**
	 * Stores the uploaded file and returns the updated item
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item for storing the file meta data, "domain" must be set
	 * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file object
	 * @param \Psr\Http\Message\UploadedFileInterface|null $preview Uploaded preview image
	 * @return \Aimeos\MShop\Media\Item\Iface Updated media item including file and preview paths
	 */
	public function upload( \Aimeos\MShop\Media\Item\Iface $item, UploadedFileInterface $file, ?UploadedFileInterface $preview = null ) : \Aimeos\MShop\Media\Item\Iface;
}
