<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * Factory which can create all job controllers.
 *
 * @package Controller
 * @subpackage Jobs
 * @deprecated Use \Aimeos\Controller\Jobs class instead
 */
class Factory extends \Aimeos\Controller\Jobs
{
	/**
	 * Creates the required controller specified by the given path of controller names.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by controllers
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
	 * @param string $path Name of the domain
	 * @throws \Aimeos\Controller\Jobs\Exception If the given path is invalid or the controllers wasn't found
	 * @deprecated Use create() instead
	 */
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos, $path )
	{
		return self::create( $context, $aimeos, $path );
	}


	/**
	 * Returns all available controller instances.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by controllers
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
	 * @param array $cntlPaths Associative list of the base path as key and all
	 * 	relative job controller paths (core and extensions)
	 * @return array Associative list of controller names as key and the class instance as value
	 * @deprecated Use get() instead
	 */
	public static function getControllers( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos, array $cntlPaths )
	{
		return self::get( $context, $aimeos, $cntlPaths );
	}
}
