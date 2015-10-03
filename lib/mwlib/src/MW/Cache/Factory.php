<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Cache
 */


/**
 * Creates new instances of classes in the cache domain.
 *
 * @package MW
 * @subpackage Cache
 */
class MW_Cache_Factory
{
	/**
	 * Creates and returns a cache object.
	 *
	 * @param string $name Object type name
	 * @param array $config Associative list of configuration strings for the cache object
	 * @param mixed $resource Reference to the resource which should be used by the cache
	 * @return MW_Cache_Iface Cache object of the requested type
	 * @throws MW_Cache_Exception if class isn't found
	 */
	static public function createManager( $name, array $config, $resource )
	{
		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'MW_Cache_' . $name : '<not a string>';
			throw new MW_Cache_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'MW_Cache_Iface';
		$classname = 'MW_Cache_' . ucwords( $name );

		if( class_exists( $classname ) === false ) {
			throw new MW_Cache_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$manager =  new $classname( $config, $resource );

		if( !( $manager instanceof $iface ) ) {
			throw new MW_Cache_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $manager;
	}
}
