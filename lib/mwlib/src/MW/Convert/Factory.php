<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Convert
 */


namespace Aimeos\MW\Convert;


/**
 * Factory for converter objects
 *
 * @package MW
 * @subpackage Convert
 */
class Factory
{
	/**
	 * Creates the converter objects
	 *
	 * @param string|array $keys Key or list of keys of the converter classes
	 * @return \Aimeos\MW\Convert\Iface Instance of converter class
	 */
	public static function createConverter( $keys ) : Iface
	{
		if( is_array( $keys ) )
		{
			$list = [];

			foreach( $keys as $key ) {
				$list[] = self::createObject( $key );
			}

			return new \Aimeos\MW\Convert\Compose( $list );
		}

		return self::createObject( $keys ); ;
	}


	/**
	 * Creates a new converter object
	 *
	 * @param string $key Key of the converter class
	 * @return \Aimeos\MW\Convert\Iface Instance of converter class
	 */
	protected static function createObject( string $key ) : Iface
	{
		$key = str_replace( '/', '\\', $key );

		foreach( explode( '\\', $key ) as $part )
		{
			if( ctype_alnum( $part ) === false )
			{
				$msg = sprintf( 'Invalid characters in class name "%1$s"', '\Aimeos\MW\Convert\\' . $key );
				throw new \Aimeos\MW\Convert\Exception( $msg );
			}
		}

		$iface = \Aimeos\MW\Convert\Iface::class;
		$classname = '\Aimeos\MW\Convert\\' . $key;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MW\Convert\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object = new $classname();

		if( !( $object instanceof $iface ) ) {
			throw new \Aimeos\MW\Convert\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $object;
	}
}
