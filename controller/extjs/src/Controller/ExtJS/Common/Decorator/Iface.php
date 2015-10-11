<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Common\Decorator;


/**
 * Decorator interface for controller.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Iface
	extends \Aimeos\Controller\ExtJS\Iface
{
	/**
	 * Initializes a new controller decorator object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\Controller\ExtJS\Iface $controller Controller object
	 * @return void
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Controller\ExtJS\Iface $controller );
}