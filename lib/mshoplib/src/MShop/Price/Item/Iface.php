<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Price
 */


namespace Aimeos\MShop\Price\Item;


/**
 * Generic interface for price DTO objects.
 *
 * @package MShop
 * @subpackage Price
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\Domain\Iface,
		\Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\PropertyRef\Iface,
		\Aimeos\MShop\Common\Item\Status\Iface, \Aimeos\MShop\Common\Item\TypeRef\Iface
{
	/**
	 * Add the given price to the current one.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item which should be added
	 * @param float $quantity Number of times the Price should be added
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function addItem( \Aimeos\MShop\Price\Item\Iface $item, float $quantity = 1 );

	/**
	 * Resets the values of the price item.
	 *
	 * The currency ID, domain, type and status stays the same.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function clear();

	/**
	 * Compares the properties of the given price item with its own one.
	 *
	 * This method compare only the essential price properties:
	 * * Value
	 * * Costs
	 * * Rebate
	 * * Taxrate
	 * * Quantity
	 * * Currency ID
	 *
	 * All other item properties are not compared.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price item to compare with
	 * @return boolean True if equal, false if not
	 * @since 2014.09
	 */
	public function compare( \Aimeos\MShop\Price\Item\Iface $price ) : bool;

	/**
	 * Returns the decimal precision of the price
	 *
	 * @return int Number of decimal digits
	 */
	public function getPrecision() : int;

	/**
	 * Returns the label of the item
	 *
	 * @return string Label of the item
	 */
	public function getLabel() : string;

	/**
	 * Sets the label of the item
	 *
	 * @param string $label Label of the item
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Returns the quantity.
	 *
	 * @return float Quantity
	 */
	public function getQuantity() : float;

	/**
	 * Sets the quantity.
	 *
	 * @param float $quantity Quantity
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setQuantity( float $quantity ) : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Returns the amount of money.
	 *
	 * @return string|null Price value or NULL if price is on request
	 */
	public function getValue() : ?string;

	/**
	 * Sets the new amount of money.
	 *
	 * @param string|int|double|null $price Amount with configured precision or NULL if price is on request
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setValue( $price ) : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Returns the costs.
	 *
	 * @return string Costs
	 */
	public function getCosts() : string;

	/**
	 * Sets the new costs.
	 *
	 * @param string|int|double $price Amount with configured precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setCosts( $price ) : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Returns the rebate amount.
	 *
	 * @return string Rebate amount
	 */
	public function getRebate() : string;

	/**
	 * Sets the new rebate amount.
	 *
	 * @param string|integer|double $price Rebate amount with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setRebate( $price ) : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Returns the tax rate in percent.
	 *
	 * @return string Tax rate of product
	 */
	public function getTaxRate() : string;

	/**
	 * Returns all tax rates in percent.
	 *
	 * @return string[] Tax rates for the price
	 */
	 public function getTaxRates() : array;

	/**
	 * Sets the new tax rate in percent.
	 *
	 * @param string|integer|double $taxrate Tax rate with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setTaxRate( $taxrate ) : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Sets the new tax rates in percent
	 *
	 * @param array $taxrates Tax rates with name as key and values with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setTaxRates( array $taxrates );

	/**
	 * Returns the tax rate flag.
	 *
	 * True if tax is included in the price value, costs and rebate, false if not
	 *
	 * @return bool Tax rate flag for the price
	 */
	public function getTaxFlag() : bool;

	/**
	 * Sets the new tax flag.
	 *
	 * @param bool $flag True if tax is included in the price value, costs and rebate, false if not
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setTaxFlag( bool $flag ) : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Returns the tax for the price item
	 *
	 * If the tax isn't set, it's calculated according to the value, the
	 * costs per item, the tax rate and the tax flag.
	 *
	 * @return string Tax value with four digits precision
	 * @see mshop/price/taxflag
	 */
	public function getTaxValue() : string;

	/**
	 * Sets the tax amount
	 *
	 * @param string|integer|double $value Tax value with up to four digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setTaxValue( $value ) : \Aimeos\MShop\Price\Item\Iface;

	/**
	 * Returns the currency ID.
	 *
	 * @return string|null Three letter ISO currency code (e.g. EUR)
	 */
	public function getCurrencyId() : ?string;

	/**
	 * Sets the currency ID.
	 *
	 * @param string $currencyid Three letter ISO currency code (e.g. EUR)
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 * @throws \Aimeos\MShop\Exception If the currency ID is invalid
	 */
	public function setCurrencyId( string $currencyid ) : \Aimeos\MShop\Price\Item\Iface;
}
