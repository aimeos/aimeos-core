<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Common\Decorator;


/**
 * Decorator interface for controller.
 *
 * @package Controller
 * @subpackage Jobs
 */
interface Iface
	extends \Aimeos\Controller\Jobs\Iface
{
	/**
	 * Initializes a new controller decorator object.
	 *
	 * @param \Aimeos\Controller\Jobs\Iface $controller Controller object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
	 * @return null
	 */
	public function __construct( \Aimeos\Controller\Jobs\Iface $controller,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos );
}