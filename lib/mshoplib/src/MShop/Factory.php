<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 */


namespace Aimeos\MShop;


/**
 * Factory which can create all MShop managers.
 *
 * @package MShop
 * @deprecated Use \Aimeos\MShop class instead
 */
class Factory extends \Aimeos\MShop
{
	/**
	 * Creates the required manager specified by the given path of manager names
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 * @throws \Aimeos\MShop\Exception If the given path is invalid or the manager wasn't found
	 * @deprecated Use create() instead
	 */
	static public function createManager( \Aimeos\MShop\Context\Item\Iface $context, $path )
	{
		return self::create( $context, $path );
	}


	/**
	 * Injects a manager object for the given path of manager names
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Manager object for the given manager path
	 * @deprecated Use inject() instead
	 */
	static public function injectManager( \Aimeos\MShop\Context\Item\Iface $context, $path, \Aimeos\MShop\Common\Manager\Iface $object )
	{
		self::inject( $context, $path, $object );
	}


	/**
	 * Enables or disables caching of class instances
	 *
	 * @param boolean $value True to enable caching, false to disable it.
	 * @return boolean Previous cache setting
	 * @deprecated Use cache() instead
	 */
	static public function setCache( $value )
	{
		return self::cache( $value );
	}
}
