<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 * @package MW
 * @subpackage Convert
 */


namespace Aimeos\MW\Convert\Hash;


/**
 * Converts numbers with comma to point as decimal separator
 *
 * @package MW
 * @subpackage Convert
 */
class Md5 implements \Aimeos\MW\Convert\Iface
{
	/**
	 * Translates a value to another one.
	 *
	 * @param mixed $value Value to translate
	 * @return mixed Translated value
	 */
	public function translate( $value )
	{
		return md5( $value );
	}


	/**
	 * Reverses the translation of the value.
	 *
	 * @param mixed $value Value to reverse
	 * @return mixed Reversed translation
	 */
	public function reverse( $value )
	{
		return $value;
	}
}
