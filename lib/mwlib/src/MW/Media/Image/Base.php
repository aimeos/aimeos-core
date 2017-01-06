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
	 * @param integer $srcWidth Width of the image
	 * @param integer $srcHeight Height of the image
	 * @param integer $destWidth New width of the image
	 * @param integer $destHeight New height of the image
	 * @return array Array containing the new width at position 0 and the new height as position 1
	 */
	protected function getSizeFitted( $srcWidth, $srcHeight, $destWidth, $destHeight )
	{
		$destWidth = ( $destWidth === null ? $srcWidth : $destWidth );
		$destHeight = ( $destHeight === null ? $srcHeight : $destHeight );

		$wRatio = $srcWidth / $destWidth;
		$hRatio = $srcHeight / $destHeight;

		if( $wRatio > $hRatio ) {
			$destHeight = round( $srcHeight / $wRatio );
		} else {
			$destWidth = round( $srcWidth / $hRatio );
		}

		return array( $destWidth, $destHeight );
	}
}
