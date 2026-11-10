<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager\Decorator;

use \Psr\Http\Message\UploadedFileInterface;


/**
 * Protects the files of media items against changes depending on the domain owning them
 *
 * The file operations write and remove files before the items are saved, so the
 * passed items are checked before the operations are executed.
 *
 * @package MShop
 * @subpackage Media
 */
class Owner
	extends \Aimeos\MShop\Common\Manager\Decorator\Owner
	implements \Aimeos\MShop\Media\Manager\Iface
{
	/**
	 * Copies the media item and the referenced files if the user is allowed to change the owning domain
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be copied
	 * @return \Aimeos\MShop\Media\Item\Iface Copy of the media item with new files
	 * @throws \Aimeos\MShop\PolicyException If changing items of the owning domain isn't allowed
	 */
	public function copy( \Aimeos\MShop\Media\Item\Iface $item ) : \Aimeos\MShop\Media\Item\Iface
	{
		$this->protect( $item );
		return $this->getManager()->copy( $item ); // @phpstan-ignore method.notFound
	}


	/**
	 * Creates scaled images if the user is allowed to change the owning domain
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be scaled
	 * @param bool $force True to enforce creating scaled images even if they already exist
	 * @return \Aimeos\MShop\Media\Item\Iface Updated media item with preview paths
	 * @throws \Aimeos\MShop\PolicyException If changing items of the owning domain isn't allowed
	 */
	public function scale( \Aimeos\MShop\Media\Item\Iface $item, bool $force = false ) : \Aimeos\MShop\Media\Item\Iface
	{
		$this->protect( $item );
		return $this->getManager()->scale( $item, $force ); // @phpstan-ignore method.notFound
	}


	/**
	 * Stores the uploaded file if the user is allowed to change the owning domain
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item for storing the file meta data, "domain" must be set
	 * @param \Psr\Http\Message\UploadedFileInterface|null $file Uploaded file object
	 * @param \Psr\Http\Message\UploadedFileInterface|null $preview Uploaded preview image
	 * @return \Aimeos\MShop\Media\Item\Iface Updated media item including file and preview paths
	 * @throws \Aimeos\MShop\PolicyException If changing items of the owning domain isn't allowed
	 */
	public function upload( \Aimeos\MShop\Media\Item\Iface $item, ?UploadedFileInterface $file,
		?UploadedFileInterface $preview = null ) : \Aimeos\MShop\Media\Item\Iface
	{
		$this->protect( $item );
		return $this->getManager()->upload( $item, $file, $preview ); // @phpstan-ignore method.notFound
	}
}
