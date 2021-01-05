<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Container
 */


namespace Aimeos\MW\Container;


/**
 * Factory for manageing containers like Zip or Excel.
 *
 * @package MW
 * @subpackage Container
 */
class Factory
{
	/**
	 * Opens an existing container or creates a new one.
	 *
	 * @param string $resourcepath Path to the resource like a file
	 * @param string $type Type of the container object
	 * @param string $format Format of the content objects inside the container
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public static function getContainer( string $resourcepath, string $type, string $format, array $options = [] )
	{
		if( ctype_alnum( $type ) === false )
		{
			$msg = sprintf( 'Invalid characters in class name "%1$s"', '\Aimeos\MW\Container\\' . $type );
			throw new \Aimeos\MW\Container\Exception( $msg );
		}

		$iface = \Aimeos\MW\Container\Iface::class;
		$classname = '\Aimeos\MW\Container\\' . $type;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MW\Container\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object = new $classname( $resourcepath, $format, $options );

		if( !( $object instanceof $iface ) )
		{
			$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface );
			throw new \Aimeos\MW\Container\Exception( $msg );
		}

		return $object;
	}
}
