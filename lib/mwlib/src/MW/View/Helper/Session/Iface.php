<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Session;


/**
 * View helper class for retrieving session values.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the session value.
	 *
	 * @param string $name Name of the session key
	 * @param mixed $default Default value if session key is not available
	 * @return mixed Session value
	 */
	public function transform( $name, $default = null );
}