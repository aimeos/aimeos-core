<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MW
 * @subpackage Macro
 */


namespace Aimeos\MW\Macro;


/**
 * Common trait for objects supporting macros
 *
 * @package MW
 * @subpackage Macro
 */
interface Iface
{
	/**
	 * Registers a custom macro that has access to the class properties if called non-static
	 *
	 * Examples:
	 *  SomeClass::macro( 'test', function( $name ) {
	 *      return $this->getConfigValue( $name ) ? true : false;
	 *  } );
	 *
	 * @param string $name Macro name
	 * @param \Closure|null $function Anonymous function
	 * @return \Closure|null Registered function
	 */
	public static function macro( string $name, \Closure $function = null ) : ?\Closure;
}
