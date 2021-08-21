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
 * Common methods for image classes.
 *
 * @package MW
 * @subpackage Media
 */
class Base
	extends \Aimeos\MW\Media\Base
{
	/**
	 * Returns the fitted width and height.
	 *
	 * @param int $srcWidth Width of the image
	 * @param int $srcHeight Height of the image
	 * @param int|null $destWidth New width of the image
	 * @param int|null $destHeight New height of the image
	 * @return array Array containing the new width at position 0 and the new height as position 1
	 */
	protected function getSizeFitted( int $srcWidth, int $srcHeight, int $destWidth = null, int $destHeight = null ) : array
	{
		$destWidth = $destWidth ?: $srcWidth;
		$destHeight = $destHeight ?: $srcHeight;

		$wRatio = $srcWidth / $destWidth;
		$hRatio = $srcHeight / $destHeight;

		if( $wRatio > $hRatio ) {
			$destHeight = (int) round( $srcHeight / $wRatio );
		} else {
			$destWidth = (int) round( $srcWidth / $hRatio );
		}

		return [$destWidth, $destHeight];
	}
}
