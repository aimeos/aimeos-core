<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Cache
 */


namespace Aimeos\MW\Cache;


/**
 * Creates new instances of classes in the cache domain.
 *
 * @package MW
 * @subpackage Cache
 */
class Factory
{
	/**
	 * Creates and returns a cache object.
	 *
	 * @param string $name Object type name
	 * @param array $config Associative list of configuration strings for the cache object
	 * @param mixed $resource Reference to the resource which should be used by the cache
	 * @return \Aimeos\MW\Cache\Iface Cache object of the requested type
	 * @throws \Aimeos\MW\Cache\Exception if class isn't found
	 */
	public static function create( $name, array $config = [], $resource = null )
	{
		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\Aimeos\MW\Cache\\' . $name : '<not a string>';
			throw new \Aimeos\MW\Cache\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = \Aimeos\MW\Cache\Iface::class;
		$classname = '\Aimeos\MW\Cache\\' . ucwords( $name );

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MW\Cache\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object = new $classname( $config, $resource );

		if( !( $object instanceof $iface ) ) {
			throw new \Aimeos\MW\Cache\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $object;
	}
}
