<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
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
	public function transform( string $name, $default = null );
}
