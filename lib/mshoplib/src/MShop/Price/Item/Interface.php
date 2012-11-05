<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Generic interface for price DTO objects.
 *
 * @package MShop
 * @subpackage Price
 */
interface MShop_Price_Item_Interface
	extends MShop_Common_Item_Interface, MShop_Common_Item_Typeid_Interface
{
	/**
	 * Add the given price to the current one.
	 *
	 * @param MShop_Price_Item_Interface $item Price item which should be added
	 * @param integer $quantity Number of times the Price should be added
	 */
	public function addItem( MShop_Price_Item_Interface $item, $quantity = 1 );

	/**
	 * Returns the domain the price is valid for.
	 *
	 * @return string Domain name
	 */
	public function getDomain();

	/**
	 * Sets the new domain the price is valid for.
	 *
	 * @param string $domain Domain name
	 */
	public function setDomain( $domain );

	/**
	 * Returns the quantity.
	 *
	 * @return integer Quantity
	 */
	public function getQuantity();

	/**
	 * Sets the quantity.
	 *
	 * @param integer Quantity
	 */
	public function setQuantity( $quantity );

	/**
	 * Returns the amount of money.
	 *
	 * @return numeric Price
	 */
	public function getValue();

	/**
	 * Sets the new amount of money.
	 *
	 * @param numeric $price Amount with two digits precision
	 */
	public function setValue( $price );

	/**
	 * Returns the shipping costs.
	 *
	 * @return numeric Shipping costs
	 */
	public function getShipping();

	/**
	 * Sets the new shipping costs.
	 *
	 * @param numeric $price Amount with two digits precision
	 */
	public function setShipping( $price );

	/**
	 * Returns the rebate amount.
	 *
	 * @return numeric Rebate amount
	 */
	public function getRebate();

	/**
	 * Sets the new Tax rate.
	 *
	 * @param numeric $taxrate Tax rate with two digits precision
	 */
	public function setTaxRate( $taxrate );

	/**
	 * Returns the taxrate amount.
	 *
	 * @return Tax rate of product
	 */
	public function getTaxRate();

	/**
	 * Sets the new rebate amount.
	 *
	 * @param numeric $price Rebate amount with two digits precision
	 */
	public function setRebate( $price );

	/**
	 * Returns the used currency as three letter code.
	 *
	 * @return string Currency ID
	 */
	public function getCurrencyId();

	/**
	 * Sets the used currency ID.
	 *
	 * @param string $currencyid Three letter currency code
	 */
	public function setCurrencyId( $currencyid );

	/**
	 * Returns the status of the item
	 *
	 * @return integer Status of the item
	 */
	public function getStatus();

	/**
	 * Sets the status of the item
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status );

}
