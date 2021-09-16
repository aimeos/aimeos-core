<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 */


namespace Aimeos;


/**
 * Factory which can create all MAdmin managers
 *
 * @package MAdmin
 */
class MAdmin
{
	private static $context;
	private static $cache = true;
	private static $objects = [];


	/**
	 * Enables or disables caching of class instances and clears cache
	 *
	 * @param bool $value True to enable caching, false to disable it
	 */
	public static function cache( bool $value )
	{
		self::$cache = (bool) $value;
		self::$context = null;
		self::$objects = [];
	}


	/**
	 * Creates the required manager specified by the given path of manager names
	 *
	 * Domain managers are created by providing only the domain name, e.g.
	 * "product" for the \Aimeos\MAdmin\Log\Manager\Standard or a path of names to
	 * retrieve a specific sub-manager.
	 * Please note, that only the default managers can be created. If you need
	 * a specific implementation, you need to use the factory class of the
	 * domain or the getSubManager() method to hand over specifc implementation
	 * names.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "log"
	 * @return \Aimeos\MShop\Common\Manager\Iface MAdmin manager object
	 * @throws \Aimeos\MAdmin\Exception If the given path is invalid or the manager wasn't found
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, string $path ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( empty( $path ) )
		{
			$msg = $context->translate( 'mshop', 'Manager path is empty' );
			throw new \Aimeos\MAdmin\Exception( $msg );
		}

		if( self::$context !== null && self::$context !== $context ) {
			self::$objects = []; // clear cached objects on context change
		}
		self::$context = $context;

		if( self::$cache === false || !isset( self::$objects[$path] ) )
		{
			$parts = explode( '/', $path );

			foreach( $parts as $part )
			{
				if( ctype_alnum( $part ) === false )
				{
					$msg = $context->translate( 'mshop', 'Invalid characters in manager name "%1$s"' );
					throw new \Aimeos\MAdmin\Exception( sprintf( $msg, $path ) );
				}
			}

			if( ( $name = array_shift( $parts ) ) === null )
			{
				$msg = $context->translate( 'mshop', 'Manager path "%1$s" is invalid' );
				throw new \Aimeos\MAdmin\Exception( sprintf( $msg, $path ) );
			}


			$factory = '\Aimeos\MAdmin\\' . ucfirst( $name ) . '\Manager\Factory';

			if( class_exists( $factory ) === false )
			{
				$msg = $context->translate( 'mshop', 'Class "%1$s" not available' );
				throw new \Aimeos\MAdmin\Exception( sprintf( $msg, $factory ) );
			}

			if( ( $manager = @call_user_func_array( [$factory, 'create'], [$context] ) ) === false )
			{
				$msg = $context->translate( 'mshop', 'Invalid factory "%1$s"' );
				throw new \Aimeos\MAdmin\Exception( sprintf( $msg, $factory ) );
			}

			self::$objects[$path] = $manager;
		}

		return self::$objects[$path];
	}


	/**
	 * Injects a manager object for the given path of manager names
	 *
	 * This method is for testing only and you must call \Aimeos\MAdmin::cache( false )
	 * afterwards!
	 *
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @param \Aimeos\MShop\Common\Manager\Iface|null $object Manager object for the given manager path or null to clear
	 */
	public static function inject( string $path, \Aimeos\MShop\Common\Manager\Iface $object = null )
	{
		self::$objects[$path] = $object;
	}
}
