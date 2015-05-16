<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Convert
 */


/**
 * Converts English dates into ISO format
 *
 * @package MW
 * @subpackage Convert
 */
class MW_Convert_DateTime_EnglishISO implements MW_Convert_Interface
{
	/**
	 * Translates a value to another one.
	 *
	 * @param mixed $value Value to translate
	 * @return mixed Translated value
	 */
	public function translate( $value )
	{
		return date_create( $value )->format( 'Y-m-d H:i:s' );
	}


	/**
	 * Reverses the translation of the value.
	 *
	 * @param mixed $value Value to reverse
	 * @return mixed Reversed translation
	 */
	public function reverse( $value )
	{
		return date_create( $value )->format( 'm/d/Y H:i:s A' );
	}
}
