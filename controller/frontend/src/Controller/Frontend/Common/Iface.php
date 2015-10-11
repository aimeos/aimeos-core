<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Common;


/**
 * Frontend controller interface.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Iface
	extends \Aimeos\Controller\Frontend\Iface
{
	/**
	 * Initializes the controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @return void
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context );

}
