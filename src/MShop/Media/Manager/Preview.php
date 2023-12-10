<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager;

use \Intervention\Image\Interfaces\ImageInterface;


/**
 * Media preview trait
 *
 * @package MShop
 * @subpackage Media
 */
trait Preview
{
	/**
	 * Creates scaled images according to the configuration settings
	 *
	 * @param \Intervention\Image\Interfaces\ImageInterface $image Media object
	 * @param string $domain Domain the item is from, e.g. product, catalog, etc.
	 * @param string $type Type of the item within the given domain, e.g. default, stage, etc.
	 * @return \Intervention\Image\Interfaces\ImageInterface[] Associative list of image width as keys and scaled media object as values
	 */
	protected function createPreviews( ImageInterface $image, string $domain, string $type ) : array
	{
		$list = [];
		$config = $this->context()->config();

		/** mshop/media/manager/previews/common
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
		 * You can also define different preview sizes for different domains (e.g.
		 * for catalog images) and for different types (e.g. catalog stage images).
		 * Use configuration settings like
		 *
		 *  mshop/media/manager/previews/previews/<domain>/
		 *  mshop/media/manager/previews/previews/<domain>/<type>/
		 *
		 * for example:
		 *
		 *  mshop/media/manager/previews/catalog/previews => [
		 *    ['maxwidth' => 240, 'maxheight' => 320, 'force-size' => true],
		 *  ]
		 *  mshop/media/manager/previews/catalog/previews => [
		 *    ['maxwidth' => 400, 'maxheight' => 300, 'force-size' => false]
		 *  ]
		 *  mshop/media/manager/previews/catalog/stage/previews => [
		 *    ['maxwidth' => 360, 'maxheight' => 320, 'force-size' => true],
		 *    ['maxwidth' => 720, 'maxheight' => 480, 'force-size' => true]
		 *  ]
		 *
		 * These settings will create two preview images for catalog stage images,
		 * one with a different size for all other catalog images and all images
		 * from other domains will be sized to 240x320px. The available domains
		 * which can have images are:
		 *
		 * * attribute
		 * * catalog
		 * * product
		 * * service
		 * * supplier
		 *
		 * There are a few image types included per domain ("default" is always
		 * available). You can also add your own types in the admin backend and
		 * extend the frontend to display them where you need them.
		 *
		 * @param array List of image size definitions
		 * @category Developer
		 * @category User
		 * @since 2019.07
		 */
		$previews = $config->get( 'mshop/media/manager/previews/common', [] );
		$previews = $config->get( 'mshop/media/manager/previews/' . $domain, $previews );
		$previews = $config->get( 'mshop/media/manager/previews/' . $domain . '/' . $type, $previews );

		foreach( $previews as $entry )
		{
			$force = $entry['force-size'] ?? 0;
			$maxwidth = $entry['maxwidth'] ?? null;
			$maxheight = $entry['maxheight'] ?? null;
			$bg = ltrim( $entry['background'] ?? 'ffffffff', '#' );

			if( $this->call( 'filterPreviews', $image, $domain, $type, $maxwidth, $maxheight, $force ) )
			{
				$file = match( $force ) {
					0 => $image->scaleDown( $maxwidth, $maxheight ),
					1 => $image->pad( $maxwidth, $maxheight, $bg, 'center' ),
					2 => $image->cover( $maxwidth, $maxheight )
				};

				$list[$file->width()] = $file;
			}
		}

		return $list;
	}


	/**
	 * Removes the previes images from the storage
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item which will contains the image URLs afterwards
	 * @param array List of preview paths to remove
	 * @return \Aimeos\MShop\Media\Item\Iface Media item with preview images removed
	 */
	protected function deletePreviews( \Aimeos\MShop\Media\Item\Iface $item, array $paths ) : \Aimeos\MShop\Media\Item\Iface
	{
		if( !empty( $paths = $this->call( 'removePreviews', $item, $paths ) ) )
		{
			$fs = $this->context()->fs( $item->getFileSystem() );

			foreach( $paths as $preview )
			{
				if( $preview && $fs->has( $preview ) ) {
					$fs->rm( $preview );
				}
			}
		}

		return $item;
	}


	/**
	 * Tests if the preview image should be created
	 *
	 * @param \Intervention\Image\Interfaces\ImageInterface $image Media object
	 * @param string $domain Domain the item is from, e.g. product, catalog, etc.
	 * @param string $type Type of the item within the given domain, e.g. default, stage, etc.
	 * @param int|null $width New width of the image or null for automatic calculation
	 * @param int|null $height New height of the image or null for automatic calculation
	 * @param int $fit "0" keeps image ratio, "1" adds padding while "2" crops image to enforce image size
	 */
	protected function filterPreviews( ImageInterface $image, string $domain, string $type,
		?int $maxwidth, ?int $maxheight, int $force ) : bool
	{
		return true;
	}


	/**
	 * Returns the preview images to be deleted
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item Media item with new preview URLs
	 * @param array List of preview paths to remove
	 * @return iterable List of preview URLs to remove
	 */
	protected function removePreviews( \Aimeos\MShop\Media\Item\Iface $item, array $paths ) : iterable
	{
		$previews = $item->getPreviews();

		// don't delete first (smallest) image because it may be referenced in past orders
		if( $item->getDomain() === 'product' && in_array( key( $previews ), $paths ) ) {
			return array_slice( $paths, 1 );
		}

		return $paths;
	}
}
