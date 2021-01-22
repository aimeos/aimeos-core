<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Manager;


/**
 * Common interface for all service manager implementations.
 *
 * @package MShop
 * @subpackage Service
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface, \Aimeos\MShop\Common\Manager\ListsRef\Iface
{
	/**
	 * Returns the service provider which is responsible for the service item.
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $item Delivery or payment service item object
	 * @param string $type Service type code
	 * @return \Aimeos\MShop\Service\Provider\Iface Service provider object
	 * @throws \Aimeos\MShop\Service\Exception If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Service\Item\Iface $item, string $type ) : \Aimeos\MShop\Service\Provider\Iface;
}
