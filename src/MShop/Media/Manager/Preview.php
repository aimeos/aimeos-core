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
	use \Aimeos\Macro\Macroable;


	private ?\Intervention\Image\ImageManager $driver = null;


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	abstract protected function context() : \Aimeos\MShop\ContextIface;


	/**
	 * Creates scaled images according to the configuration settings
	 *
	 * @param \Intervention\Image\Interfaces\ImageInterface $image Media object
	 * @param array $sizes List of entries with "maxwidth" (int or null), "maxheight" (int or null), "force-size" (0: scale, 1: pad, 2: cover) and "background" (hex color) values
	 * @return \Intervention\Image\Interfaces\ImageInterface[] Associative list of image width as keys and scaled media object as values
	 */
	protected function createPreviews( ImageInterface $image, array $sizes ) : array
	{
		$list = [];

		foreach( $sizes as $entry )
		{
			$force = $entry['force-size'] ?? 0;
			$maxwidth = $entry['maxwidth'] ?? null;
			$maxheight = $entry['maxheight'] ?? null;
			$bg = ltrim( $entry['background'] ?? 'ffffff00', '#' );

			if( $this->call( 'filterPreviews', $image, $maxwidth, $maxheight, $force ) )
			{
				$file = match( $force ) {
					0 => (clone $image)->scaleDown( $maxwidth, $maxheight ),
					1 => (clone $image)->pad( $maxwidth, $maxheight, $bg, 'center' ),
					2 => (clone $image)->cover( $maxwidth, $maxheight )
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
	 * @param int|null $width New width of the image or null for automatic calculation
	 * @param int|null $height New height of the image or null for automatic calculation
	 * @param int $fit "0" keeps image ratio, "1" adds padding while "2" crops image to enforce image size
	 */
	protected function filterPreviews( ImageInterface $image, ?int $maxwidth, ?int $maxheight, int $force ) : bool
	{
		return true;
	}


	/**
	 * Returns the image object for the given file name
	 *
	 * @param string $file URL or relative path to the file
	 * @param string $fsname File system name where the file is stored
	 * @return \Intervention\Image\Interfaces\ImageInterface Image object
	 */
	protected function image( string $file, string $fsname = 'fs-media' ) : ImageInterface
	{
		if( !isset( $this->driver ) )
		{
			if( class_exists( '\Intervention\Image\Vips\Driver' ) ) {
				$driver = new \Intervention\Image\Vips\Driver();
			} elseif( class_exists( '\Imagick' ) ) {
				$driver = new \Intervention\Image\Drivers\Imagick\Driver();
			} else {
				$driver = new \Intervention\Image\Drivers\Gd\Driver();
			}

			$this->driver = new \Intervention\Image\ImageManager( $driver );
		}

		if( preg_match( '#^[a-zA-Z]{1,10}://#', $file ) === 1 )
		{
			if( ( $fh = fopen( $file, 'r' ) ) === false )
			{
				$msg = $this->context()->translate( 'mshop', 'Unable to open file "%1$s"' );
				throw new \RuntimeException( sprintf( $msg, $file ) );
			}
		}
		else
		{
			$fh = $this->context()->fs( $fsname )->reads( $file );
		}

		$image = $this->driver->read( $fh );
		fclose( $fh );

		return $image;
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
