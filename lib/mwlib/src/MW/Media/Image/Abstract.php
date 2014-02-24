<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Media
 */


/**
 * Common methods for image classes.
 *
 * @package MW
 * @subpackage Media
 */
class MW_Media_Image_Abstract
	extends MW_Media_Abstract
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
	protected function _getSizeFitted( $srcWidth, $srcHeight, $destWidth, $destHeight )
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
