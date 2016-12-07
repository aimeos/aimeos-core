<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Common\Factory;


/**
 * Controller factory interface.
 *
 * @package Controller
 * @subpackage Jobs
 */
interface Iface
{
	/**
	 * Creates a new controller based on the name.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
	 * @param string|null $name Name of the controller implementation (Default if null)
	 * @return \Aimeos\Controller\Jobs\Iface Controller object
	 */
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos, $name = null );
}