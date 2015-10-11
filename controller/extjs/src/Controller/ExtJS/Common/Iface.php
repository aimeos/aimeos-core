<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Common;


/**
 * ExtJS controller interface.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Iface
	extends \Aimeos\Controller\ExtJS\Iface
{
	/**
	 * Initializes the controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @return void
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context );

}
