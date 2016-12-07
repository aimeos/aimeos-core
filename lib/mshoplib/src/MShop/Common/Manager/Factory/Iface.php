<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Factory;


/**
 * Generic interface for all manager created by factories.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Initializes the manager by using the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @return null
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context );
}
