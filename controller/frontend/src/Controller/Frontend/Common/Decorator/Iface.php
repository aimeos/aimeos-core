<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Common\Decorator;


/**
 * Decorator interface for controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Iface
	extends \Aimeos\Controller\Frontend\Iface
{
	/**
	 * Initializes a new controller decorator object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\Controller\Frontend\Iface $controller Controller object
	 * @return void
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Controller\Frontend\Iface $controller );
}
