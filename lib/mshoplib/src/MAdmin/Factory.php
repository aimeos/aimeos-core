<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 */


namespace Aimeos\MAdmin;


/**
 * Factory which can create all MAdmin managers.
 *
 * @package MAdmin
 * @deprecated Use \Aimeos\MAdmin class instead
 */
class Factory extends \Aimeos\MAdmin
{
	/**
	 * Creates the required manager specified by the given path of manager names.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "log"
	 * @return \Aimeos\MShop\Common\Manager\Iface MAdmin manager object
	 * @throws \Aimeos\MAdmin\Exception If the given path is invalid or the manager wasn't found
	 * @deprecated Use create() instead
	 */
	static public function createManager( \Aimeos\MShop\Context\Item\Iface $context, $path )
	{
		return self::create( $context, $path );
	}


	/**
	 * Enables or disables caching of class instances.
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
