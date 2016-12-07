<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Item\Address;


/**
 * Interface for provider common address DTO objects used by the shop.
 * @package MShop
 * @subpackage Customer
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Address\Iface, \Aimeos\MShop\Common\Item\Position\Iface,
	\Aimeos\MShop\Common\Item\Parentid\Iface
{
}
