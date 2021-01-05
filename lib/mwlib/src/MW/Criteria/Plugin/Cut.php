<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Plugin;


/**
 * Criteria plugin for cutting strings
 *
 * @package MW
 * @subpackage Common
 */
class Cut implements \Aimeos\MW\Criteria\Plugin\Iface
{
	/**
	 * Cuts the value
	 *
	 * @param mixed $value Value to translate
	 * @return mixed Translated value
	 */
	public function translate( $value )
	{
		if( is_array( $value ) )
		{
			foreach( $value as $key => $str ) {
				$value[$key] = substr( $str, 0, 255 );
			}

			return $value;
		}

		return substr( $value, 0, 255 );
	}


	/**
	 * Reverses cutting the value
	 *
	 * @param mixed $value Value to reverse
	 * @return mixed Reversed translation
	 */
	public function reverse( $value )
	{
		return $value;
	}
}
