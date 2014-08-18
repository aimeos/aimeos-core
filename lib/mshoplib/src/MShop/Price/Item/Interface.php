<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
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
	 * @return void
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
	 * @return void
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
	 * @return void
	 */
	public function setQuantity( $quantity );

	/**
	 * Returns the amount of money.
	 *
	 * @return string Price
	 */
	public function getValue();

	/**
	 * Sets the new amount of money.
	 *
	 * @param integer|double $price Amount with two digits precision
	 * @return void
	 */
	public function setValue( $price );

	/**
	 * Returns the costs.
	 *
	 * @return string Costs
	 */
	public function getCosts();

	/**
	 * Sets the new costs.
	 *
	 * @param integer|double $price Amount with two digits precision
	 * @return void
	 */
	public function setCosts( $price );

	/**
	 * Returns the rebate amount.
	 *
	 * @return string Rebate amount
	 */
	public function getRebate();

	/**
	 * Sets the new rebate amount.
	 *
	 * @param integer|double $price Rebate amount with two digits precision
	 * @return void
	 */
	public function setRebate( $price );

	/**
	 * Returns the taxrate amount.
	 *
	 * @return string rate of product
	 */
	public function getTaxRate();

	/**
	 * Sets the new Tax rate.
	 *
	 * @param string $taxrate Tax rate with two digits precision
	 * @return void
	 */
	public function setTaxRate( $taxrate );

	/**
	 * Returns the currency ID.
	 *
	 * @return string|null Three letter ISO currency code (e.g. EUR)
	 */
	public function getCurrencyId();

	/**
	 * Sets the currency ID.
	 *
	 * @param string|null $currencyid Three letter ISO currency code (e.g. EUR)
	 * @throws MShop_Exception If the currency ID is invalid
	 * @return void
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
	 * @return void
	 */
	public function setStatus( $status );

}
