<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
	public static function checkClass( $name, $object )
	{
		if( ($object instanceof $name) === false ) {
			throw new \Aimeos\MW\Common\Exception( sprintf( 'Object doesn\'t implement "%1$s"', $name ) );
		}

		return $object;
	}


	/**
	 * Tests if a list of objects are an instance of the given class, extends the class or implements the interface.
	 *
	 * @param string $name Name of the class or interface
	 * @param array $list List of objects to test
	 * @return mixed Tested list of class instances
	 * @throws \Aimeos\MW\Common\Exception if an object of the list doesn't match the type
	 */
	public static function checkClassList( $name, array $list )
	{
		foreach( $list as $object )
		{
			if( ($object instanceof $name) === false ) {
				throw new \Aimeos\MW\Common\Exception( sprintf( 'Object doesn\'t implement "%1$s"', $name ) );
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
	public static function sanitize( $string )
	{
		$regex = '/(\s|\&|\%|\?|\#|\=|\{|\}|\||\\\\|\~|\[|\]|\`|\^|\_|\/)+/';
		return trim( preg_replace( $regex, '_', $string ), '_' );
	}
}
