<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Item;


/**
 * Generic interface for delivery and payment item DTOs.
 * @package MShop
 * @subpackage Service
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Config\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\Position\Iface,
		\Aimeos\MShop\Common\Item\Status\Iface, \Aimeos\MShop\Common\Item\Time\Iface,
		\Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Returns the code of the service item.
	 *
	 * @return string Service item code
	 */
	public function getCode() : string;

	/**
	 * Sets a new code for the service item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setCode( string $code ) : \Aimeos\MShop\Service\Item\Iface;

	/**
	 * Returns the name of the service provider the item belongs to.
	 *
	 * @return string Name of the service provider
	 */
	public function getProvider() : string;

	/**
	 * Sets the new name of the service provider the item belongs to.
	 *
	 * @param string $provider Name of the service provider
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setProvider( string $provider ) : \Aimeos\MShop\Service\Item\Iface;

	/**
	 * Returns the label of the service item.
	 *
	 * @return string Service item label
	 */
	public function getLabel() : string;

	/**
	 * Sets a new label for the service item.
	 *
	 * @param string $label Label as defined by the service provider
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setLabel( string $label ) : \Aimeos\MShop\Service\Item\Iface;
}
