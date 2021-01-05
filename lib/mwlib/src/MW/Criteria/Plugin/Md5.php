<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Criteria\Plugin;


/**
 * Criteria plugin for MD5 hashing
 *
 * @package MW
 * @subpackage Common
 */
class Md5 implements \Aimeos\MW\Criteria\Plugin\Iface
{
	/**
	 * Generates a MD5 hash
	 *
	 * @param mixed $value Value to translate
	 * @return mixed Translated value
	 */
	public function translate( $value )
	{
		if( is_array( $value ) )
		{
			foreach( $value as $key => $str ) {
				$value[$key] = md5( $str );
			}

			return $value;
		}

		return md5( $value );
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
