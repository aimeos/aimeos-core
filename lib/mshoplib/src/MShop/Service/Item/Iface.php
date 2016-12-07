<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	extends \Aimeos\MShop\Common\Item\Config\Iface, \Aimeos\MShop\Common\Item\ListRef\Iface,
		\Aimeos\MShop\Common\Item\Position\Iface, \Aimeos\MShop\Common\Item\Typeid\Iface
{
	/**
	 * Returns the code of the service item.
	 *
	 * @return string Service item code
	 */
	public function getCode();

	/**
	 * Sets a new code for the service item.
	 *
	 * @param string $code Code as defined by the service provider
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setCode( $code );

	/**
	 * Returns the name of the service provider the item belongs to.
	 *
	 * @return string Name of the service provider
	 */
	public function getProvider();

	/**
	 * Sets the new name of the service provider the item belongs to.
	 *
	 * @param string $provider Name of the service provider
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setProvider( $provider );

	/**
	 * Returns the label of the service item.
	 *
	 * @return string Service item label
	 */
	public function getLabel();

	/**
	 * Sets a new label for the service item.
	 *
	 * @param string $label Label as defined by the service provider
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setLabel( $label );

	/**
	 * Returns the status of the service item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();

	/**
	 * Sets the status of the item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Service\Item\Iface Service item for chaining method calls
	 */
	public function setStatus( $status );
}
