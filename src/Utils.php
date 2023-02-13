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


	public static function implements( object $object, string $iface ) : object
	{
		if( !( $object instanceof $iface ) ) {
			throw new \LogicException( sprintf( 'Class "%1$s" does not implement "%2$s"', get_class( $object ), $iface ), 400 );
		}

		return $object;
	}
}
