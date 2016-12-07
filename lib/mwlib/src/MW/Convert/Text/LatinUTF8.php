<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Convert
 */


namespace Aimeos\MW\Convert\Text;


/**
 * Converts latin text into UTF-8
 *
 * @package MW
 * @subpackage Convert
 */
class LatinUTF8 implements \Aimeos\MW\Convert\Iface
{
	/**
	 * Translates a value to another one.
	 *
	 * @param mixed $value Value to translate
	 * @return mixed Translated value
	 */
	public function translate( $value )
	{
		return utf8_encode( $value );
	}


	/**
	 * Reverses the translation of the value.
	 *
	 * @param mixed $value Value to reverse
	 * @return mixed Reversed translation
	 */
	public function reverse( $value )
	{
		return utf8_decode( $value );
	}
}
