<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Service;


/**
 * Interface for order base service managers.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Creates a new order service attribute item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Attribute\Iface New order service attribute item object
	 */
	public function createAttributeItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Creates a new order service transaction item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Service\Transaction\Iface New order service transaction item object
	 */
	public function createTransaction( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface;
}
