<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item;


/**
 * Generic interface for product items created and saved by product managers.
 *
 * @package MShop
 * @subpackage Product
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Config\Iface, \Aimeos\MShop\Common\Item\ListRef\Iface,
		\Aimeos\MShop\Common\Item\Time\Iface, \Aimeos\MShop\Common\Item\Typeid\Iface
{
	/**
	 * Returns the property items of the product
	 *
	 * @return \Aimeos\MShop\Product\Item\Property\Iface[] Associative list of property IDs as keys and property items as values
	 */
	public function getPropertyItems( $type = null );

	/**
	 * Returns the status of the product item.
	 *
	 * @return integer Status of the product
	 */
	public function getStatus();

	/**
	 * Sets the new status of the product item.
	 *
	 * @param integer $status New status of the product
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setStatus( $status );

	/**
	 * Returns the code of the product item.
	 *
	 * @return string Code of the product
	 */
	public function getCode();

	/**
	 * Sets a new code of the product item.
	 *
	 * @param string $code New code of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setCode( $code );

	/**
	 * Returns the label of the product item.
	 *
	 * @return string Label of the product item
	 */
	public function getLabel();

	/**
	 * Sets a new label of the product item.
	 *
	 * @param string $label New label of the product item
	 * @return \Aimeos\MShop\Product\Item\Iface Product item for chaining method calls
	 */
	public function setLabel( $label );
}
