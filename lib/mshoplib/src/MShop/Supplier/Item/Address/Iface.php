<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Supplier
 */


namespace Aimeos\MShop\Supplier\Item\Address;


/**
 * Interface for provider supplier address DTO objects used by the shop.
 * @package MShop
 * @subpackage Supplier
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Address\Iface,
		\Aimeos\MShop\Common\Item\Parentid\Iface, \Aimeos\MShop\Common\Item\Position\Iface
{
}
