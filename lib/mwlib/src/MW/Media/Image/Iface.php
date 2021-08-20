<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Media
 */


namespace Aimeos\MW\Media\Image;


/**
 * Common interface for all application media objects.
 *
 * @package MW
 * @subpackage Media
 */
interface Iface
	extends \Aimeos\MW\Media\Iface
{
	/**
	 * Returns the height of the image
	 *
	 * @return int Height in pixel
	 */
	public function getHeight() : int;

	/**
	 * Returns the width of the image
	 *
	 * @return int Width in pixel
	 */
	public function getWidth() : int;

	/**
	 * Scales the image to the given width and height.
	 *
	 * @param int|null $width New width of the image or null for automatic calculation
	 * @param int|null $height New height of the image or null for automatic calculation
	 * @param int $fit "0" keeps image ratio, "1" adds padding while "2" crops image to enforce image size
	 * @return \Aimeos\MW\Media\Image\Iface Self object for method chaining
	 */
	public function scale( int $width = null, int $height = null, int $fit = 0 ) : Iface;
}
