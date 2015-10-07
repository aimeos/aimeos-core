<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Common\Factory;


/**
 * Controller factory interface.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Iface
{
	/**
	 * Creates a new controller based on the name.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param string|null $name Name of the controller implementation (Default if null)
	 * @return \Aimeos\Controller\Frontend\Common\Iface Controller object
	 */
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, $name = null );
}
