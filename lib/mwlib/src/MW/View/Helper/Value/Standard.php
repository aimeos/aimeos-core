<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Value;


/**
 * View helper class for retrieving a value from an associative array
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Value\Iface
{
	/**
	 * Returns the value for the given key in the array
	 *
	 * @param array $values Multi-dimensional associative list of key/value pairs
	 * @param string $key Parameter key like "name" or "list/test" for associative arrays
	 * @param mixed $default Returned value if no one for key is available
	 * @return mixed Value from the array or default value if not present in array
	 */
	public function transform( array $values, $key, $default = null )
	{
		foreach( explode( '/', trim( $key, '/' ) ) as $part )
		{
			if( isset( $values[$part] ) ) {
				$values = $values[$part];
			} else {
				return $default;
			}
		}

		return $values;
	}
}
