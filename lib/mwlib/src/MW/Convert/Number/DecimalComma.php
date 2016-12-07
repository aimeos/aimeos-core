<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Convert
 */


namespace Aimeos\MW\Convert\Number;


/**
 * Converts numbers with comma to point as decimal separator
 *
 * @package MW
 * @subpackage Convert
 */
class DecimalComma implements \Aimeos\MW\Convert\Iface
{
	/**
	 * Translates a value to another one.
	 *
	 * @param mixed $value Value to translate
	 * @return mixed Translated value
	 */
	public function translate( $value )
	{
		return str_replace( ',', '.', $value );
	}


	/**
	 * Reverses the translation of the value.
	 *
	 * @param mixed $value Value to reverse
	 * @return mixed Reversed translation
	 */
	public function reverse( $value )
	{
		return str_replace( '.', ',', $value );
	}
}
