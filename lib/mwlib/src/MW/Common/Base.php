<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Common
 */


/**
 * Common methods for many objects.
 *
 * @package MW
 * @subpackage Common
 */
abstract class MW_Common_Base
{
	/**
	 * Tests if the given object is an instance of the given class, extends the class or implements the interface.
	 *
	 * @param string $name Class or interface name
	 * @param mixed $object Class instance to test
	 * @throws MW_Common_Exception if the object doesn't extend the class or implements the interface
	 */
	public static function checkClass( $name, $object )
	{
		if( ($object instanceof $name) === false ) {
			throw new MW_Common_Exception( sprintf( 'Object doesn\'t implement "%1$s"', $name ) );
		}
	}


	/**
	 * Tests if a list of objects are an instance of the given class, extends the class or implements the interface.
	 *
	 * @param string $name Name of the class or interface
	 * @param array $list List of objects to test
	 * @throws MW_Common_Exception if an object of the list doesn't match the type
	 */
	public static function checkClassList( $name, array $list )
	{
		foreach( $list as $object ) {
			if( ($object instanceof $name) === false ) {
				throw new MW_Common_Exception( sprintf( 'Object doesn\'t implement "%1$s"', $name ) );
			}
		}
	}
}