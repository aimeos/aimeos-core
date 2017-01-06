<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2017
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
	 * Scales the image to the given width and height.
	 *
	 * @param integer $width New width of the image
	 * @param integer $height New height of the image
	 * @param boolean $fit True to keep the width/height ratio of the image
	 * @return \Aimeos\MW\Media\Image\Iface Self object for method chaining
	 */
	public function scale( $width, $height, $fit = true );
}
