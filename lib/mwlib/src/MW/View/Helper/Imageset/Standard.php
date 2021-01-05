<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Imageset;


/**
 * View helper class for creating an image srcset string
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Imageset\Iface
{
	/**
	 * Returns the image srcset value for the given image list
	 *
	 * @param array $images List of widths as keys and URLs as values
	 * @return string Image srcset value
	 */
	public function transform( array $images ) : string
	{
		$srcset = [];
		$view = $this->getView();

		foreach( $images as $type => $path ) {
			$srcset[] = $view->content( $path ) . ' ' . $type . 'w';
		}

		return join( ', ', $srcset );
	}
}
