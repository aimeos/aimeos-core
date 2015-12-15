<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Value;


/**
 * View helper class for retrieving parameter values
 *
 * @package MW
 * @subpackage View
 */
class Standard
	extends \Aimeos\MW\View\Helper\Base
	implements \Aimeos\MW\View\Helper\Value\Iface
{
	/**
	 * Returns the parameter value for the given key
	 *
	 * @param string $key Parameter key like "name" or "list/test" for associative arrays
	 * @param mixed $default Returned value if no one for key is available
	 * @return mixed Parameter value
	 */
	public function transform( $key, $default = null )
	{
		$params = $this->getView()->param();

		foreach( explode( '/', trim( $key, '/' ) ) as $part )
		{
			if( isset( $params[$part] ) ) {
				$params = $params[$part];
			} else {
				return $default;
			}
		}

		return $params;
	}
}
