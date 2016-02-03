<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item\Stock\Warehouse;


/**
 * Default product stock warehouse item interface.
 * @package MShop
 * @subpackage Product
 */
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Returns the code of the warehouse item.
	 *
	 * @return string Code of the warehouse item
	 */
	public function getCode();

	/**
	 * Sets the code of the warehouse item.
	 *
	 * @param string $code New Code of the warehouse item
	 * @return \Aimeos\MShop\Product\Item\Stock\Warehouse\Iface Product stock warehouse item for chaining method calls
	 */
	public function setCode( $code );

	/**
	 * Returns the label of the warehouse item.
	 *
	 * @return string Label of the warehouse item
	 */
	public function getLabel();

	/**
	 * Sets the label of the warehouse item.
	 *
	 * @param string $label New label of the warehouse item
	 * @return \Aimeos\MShop\Product\Item\Stock\Warehouse\Iface Product stock warehouse item for chaining method calls
	 */
	public function setLabel( $label );

	/**
	 * Returns the status of the warehouse item.
	 *
	 * @return string Status of the warehouse item
	 */
	public function getStatus();

	/**
	 * Sets the status of the warehouse item.
	 *
	 * @param integer $status New status of the warehouse item
	 * @return \Aimeos\MShop\Product\Item\Stock\Warehouse\Iface Product stock warehouse item for chaining method calls
	 */
	public function setStatus( $status );
}