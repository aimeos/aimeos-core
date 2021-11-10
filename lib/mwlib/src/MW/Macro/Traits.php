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
trait Traits
{
	private static $macros = [];


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
	public static function macro( string $name, \Closure $function = null ) : ?\Closure
	{
		$self = get_called_class();

		if( $function ) {
			self::$macros[$self][$name] = $function;
		}

		foreach( array_merge( [$self], class_parents( static::class ) ) as $class )
		{
			if( isset( self::$macros[$class][$name] ) ) {
				return self::$macros[$class][$name];
			}
		}

		return null;
	}


	/**
	 * Passes unknown method calls to the custom macros
	 *
	 * @param string $name Method name
	 * @param array $args Method arguments
	 * @return mixed Result or macro call
	 */
	public function __call( string $name, array $args )
	{
		if( $fcn = static::macro( $name ) ) {
			return call_user_func_array( $fcn->bindTo( $this, self::class ), $args );
		}

		$msg = 'Called unknown macro "%1$s" on class "%2$s"';
		throw new \BadMethodCallException( sprintf( $msg, $name, get_class( $this ) ) );
	}


	/**
	 * Passes method calls to the custom macros
	 *
	 * @param string $name Macro name
	 * @param array $args Macro arguments
	 * @return mixed Result or macro call
	 */
	protected function call( string $name, ...$args )
	{
		if( $fcn = static::macro( $name ) ) {
			return call_user_func_array( $fcn->bindTo( $this, self::class ), $args );
		}

		return $this->$name( ...$args );
	}
}
