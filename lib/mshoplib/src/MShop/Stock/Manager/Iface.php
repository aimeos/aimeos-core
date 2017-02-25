<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Stock
 */


namespace Aimeos\MShop\Stock\Manager;


/**
 * Generic interface for stock managers
 * @package MShop
 * @subpackage Stock
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Factory\Iface, \Aimeos\MShop\Common\Manager\Find\Iface
{
	/**
	 * Decreases the stock level of the product for the stock type.
	 *
	 * @param string $productCode Unique code of a product
	 * @param string $stockType Unique code of the stock type
	 * @param integer $amount Amount the stock level should be decreased
	 * @return void
	 */
	public function decrease( $productCode, $stockType, $amount );


	/**
	 * Increases the stock level of the product for the stock type.
	 *
	 * @param string $productCode Unique code of a product
	 * @param string $stockType Unique code of the stock type
	 * @param integer $amount Amount the stock level should be increased
	 * @return void
	 */
	public function increase( $productCode, $stockType, $amount );
}