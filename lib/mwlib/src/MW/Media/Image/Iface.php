<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	 * @return integer Height in pixel
	 */
	public function getHeight();

	/**
	 * Returns the width of the image
	 *
	 * @return integer Width in pixel
	 */
	public function getWidth();

	/**
	 * Scales the image to the given width and height.
	 *
	 * @param integer|null $width New width of the image or null for automatic calculation
	 * @param integer|null $height New height of the image or null for automatic calculation
	 * @param boolean $fit True to keep the width/height ratio of the image
	 * @return \Aimeos\MW\Media\Image\Iface Self object for method chaining
	 */
	public function scale( $width, $height, $fit = true );
}
