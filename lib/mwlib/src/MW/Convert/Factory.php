<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Convert
 */


/**
 * Factory for converter objects
 *
 * @package MW
 * @subpackage Convert
 */
class MW_Convert_Factory
{
	/**
	 * Creates the converter objects
	 *
	 * @param string|array $keys Key or list of keys of the converter classes
	 * @return MW_Convert_Iface Instance of converter class
	 */
	public static function createConverter( $keys )
	{
		if( is_array( $keys ) )
		{
			$list = array();

			foreach( $keys as $key ) {
				$list[] = self::createObject( $key );
			}

			return new MW_Convert_Compose( $list );
		}

		return self::createObject( $keys );;
	}


	/**
	 * Creates a new converter object
	 *
	 * @param string $key Key of the converter class
	 * @return MW_Convert_Iface Instance of converter class
	 */
	protected static function createObject( $key )
	{
		$key = str_replace( '/', '_', $key );

		foreach( explode( '_', $key ) as $part )
		{
			if( ctype_alnum( $part ) === false )
			{
				$classname = is_string( $key ) ? 'MW_Convert_' . $key : '<not a string>';
				throw new MW_Convert_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
			}
		}

		$iface = 'MW_Convert_Iface';
		$classname = 'MW_Convert_' . $key;

		if( class_exists( $classname ) === false ) {
			throw new MW_Convert_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$object =  new $classname();

		if( !( $object instanceof $iface ) ) {
			throw new MW_Convert_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $object;
	}
}
