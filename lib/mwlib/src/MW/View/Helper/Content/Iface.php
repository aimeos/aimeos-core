<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Content;


/**
 * View helper class for generating media URLs
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the complete encoded content URL.
	 *
	 * @param string|null $url Absolute, relative or data: URL
	 * @return string Complete encoded content URL
	 */
	public function transform( ?string $url ) : string;
}
