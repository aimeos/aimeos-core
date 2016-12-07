<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


/**
 * Factory interface for managers.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Initializes a new manager decorator object.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @return null
	 */
	public function __construct( \Aimeos\MShop\Common\Manager\Iface $manager, \Aimeos\MShop\Context\Item\Iface $context );
}
