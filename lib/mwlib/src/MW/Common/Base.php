<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Common
 */


namespace Aimeos\MW\Common;


/**
 * Common methods for many objects.
 *
 * @package MW
 * @subpackage Common
 */
abstract class Base
{
	/**
	 * Tests if the given object is an instance of the given class, extends the class or implements the interface.
	 *
	 * @param string $name Class or interface name
	 * @param mixed $object Class instance to test
	 * @return mixed Tested class instance
	 * @throws \Aimeos\MW\Common\Exception if the object doesn't extend the class or implements the interface
	 */
	public static function checkClass( string $name, $object )
	{
		if( ( $object instanceof $name ) === false )
		{
			$msg = sprintf( 'Object "%1$s" doesn\'t implement "%2$s"', $object ? get_class( $object ) : 'NULL', $name );
			throw new \Aimeos\MW\Common\Exception( $msg );
		}

		return $object;
	}


	/**
	 * Tests if a list of objects are an instance of the given class, extends the class or implements the interface.
	 *
	 * @param string $name Name of the class or interface
	 * @param iterable $list List of objects to test
	 * @return iterable Tested list of class instances
	 * @throws \Aimeos\MW\Common\Exception if an object of the list doesn't match the type
	 */
	public static function checkClassList( string $name, iterable $list ) : iterable
	{
		foreach( $list as $object )
		{
			if( ( $object instanceof $name ) === false )
			{
				$msg = sprintf( 'Object "%1$s" doesn\'t implement "%2$s"', $object ? get_class( $object ) : 'NULL', $name );
				throw new \Aimeos\MW\Common\Exception( $msg );
			}
		}

		return $list;
	}


	/**
	 * Replaces dangerous characteris
	 *
	 * @param string $string String to sanitize
	 * @return string Sanitized string
	 */
	public static function sanitize( string $string ) : string
	{
		return \Aimeos\MW\Str::slug( $string );
	}
}
