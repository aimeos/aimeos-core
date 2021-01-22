<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Attribute
 */


namespace Aimeos\MShop\Attribute\Manager;


/**
 * Interface for attribute manager.
 *
 * @package MShop
 * @subpackage Attribute
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface,
		\Aimeos\MShop\Common\Manager\ListsRef\Iface, \Aimeos\MShop\Common\Manager\PropertyRef\Iface
{
}
