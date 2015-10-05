<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
	extends \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Returns the service provider which is responsible for the service item.
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $item Delivery or payment service item object
	 * @return \Aimeos\MShop\Service\Provider\Iface Returns a service provider implementing \Aimeos\MShop\Service\Provider\Iface
	 * @throws \Aimeos\MShop\Service\Exception If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Service\Item\Iface $item );
}
