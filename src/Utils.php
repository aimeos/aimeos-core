<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 */


namespace Aimeos;


/**
 * Utility methods
 */
class Utils
{
	/**
	 * Creates a new object instance
	 *
	 * @param string $class Name of the class
	 * @param array $args Constructor arguments
	 * @param string|null $iface Name of the interface the object must implement
	 * @return object New object instance
	 * @throws \LogicException If the class isn't found or doesn't implement the interface
	 * @todo 2025.01 Allow list of interfaces to check for common and specific interfaces
	 */
	public static function create( string $class, array $args, string $iface = null ) : object
	{
		if( class_exists( $class ) === false ) {
			throw new \LogicException( sprintf( 'Class "%1$s" not found', $class ), 400 );
		}

		$object = new $class( ...$args );

		if( $iface && !( $object instanceof $iface ) ) {
			throw new \LogicException( sprintf( 'Class "%1$s" does not implement "%2$s"', $class, $iface ), 400 );
		}

		return $object;
	}


	/**
	 * Checks if the object implements the given interface
	 *
	 * @param object $object Object to check
	 * @param string $iface Name of the interface the object must implement
	 * @return object Same object as passed in
	 * @throws \LogicException If the object doesn't implement the interface
	 * @todo 2025.01 Allow list of interfaces to check for common and specific interfaces
	 */
	public static function implements( object $object, string $iface ) : object
	{
		if( !( $object instanceof $iface ) ) {
			throw new \LogicException( sprintf( 'Class "%1$s" does not implement "%2$s"', get_class( $object ), $iface ), 400 );
		}

		return $object;
	}
}
