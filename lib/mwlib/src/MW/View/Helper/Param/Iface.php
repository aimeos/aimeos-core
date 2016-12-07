<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Param;


/**
 * View helper class for retrieving parameter values.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the parameter value.
	 *
	 * @param string|null $name Name of the parameter key or null for all parameters
	 * @param mixed $default Default value if parameter key is not available
	 * @return mixed Parameter value or associative list of key/value pairs
	 */
	public function transform( $name = null, $default = null );
}