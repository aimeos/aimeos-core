<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Price
 */


namespace Aimeos\MShop\Price\Item;

use \Aimeos\MShop\Common\Item\ListsRef;
use \Aimeos\MShop\Common\Item\PropertyRef;
use \Aimeos\MShop\Common\Item\TypeRef;


/**
 * Default implementation of a price object.
 *
 * @package MShop
 * @subpackage Price
 */
class Standard extends Base
{
	use ListsRef\Traits, PropertyRef\Traits, TypeRef\Traits {
		ListsRef\Traits::__clone as __cloneList;
		PropertyRef\Traits::__clone as __cloneProperty;
	}


	private int $precision;
	private ?string $tax;


	/**
	 * Initalizes the object with the given values
	 *
	 * @param string $_prefix Prefix for the keys returned by toArray()
	 * @param array $values Associative array of key/value pairs for price, costs, rebate and currencyid
	 */
	public function __construct( string $_prefix, array $values = [] ) // @phpstan-ignore constructor.unusedParameter
	{
		$this->precision = (int) ( $values['.precision'] ?? 2 );
		$this->tax = $values['price.taxvalue'] ?? null;

		parent::__construct( 'price.', $values );

		// @phpstan-ignore argument.type
		$this->initPropertyItems( (array) ( $values['.propitems'] ?? [] ) );
		$this->initListItems( (array) ( $values['.listitems'] ?? [] ) );
	}


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		parent::__clone();
		$this->__cloneList();
		$this->__cloneProperty();
	}


	/**
	 * Returns costs per item.
	 *
	 * @return string Costs per item
	 */
	public function getCosts() : string
	{
		return $this->formatNumber( (float) $this->get( 'price.costs', '0.00' ) ) ?? '0.00';
	}


	/**
	 * Sets the new costsper item.
	 *
	 * @param string|integer|double $price Amount with two digits precision
	 * @return static Price item for chaining method calls
	 */
	public function setCosts( $price ) : static
	{
		return $this->set( 'price.costs', $this->checkPrice( (string) $price ) );
	}


	/**
	 * Returns the currency ID.
	 *
	 * @return string|null Three letter ISO currency code (e.g. EUR)
	 */
	public function getCurrencyId() : ?string
	{
		// @phpstan-ignore return.type
		return $this->get( 'price.currencyid' );
	}


	/**
	 * Sets the used currency ID.
	 *
	 * @param string $currencyid Three letter currency code
	 * @return static Price item for chaining method calls
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	public function setCurrencyId( string $currencyid ) : static
	{
		return $this->set( 'price.currencyid', \Aimeos\Utils::currency( $currencyid, false ) );
	}


	/**
	 * Returns the domain the price is valid for.
	 *
	 * @return string Domain name
	 */
	public function getDomain() : string
	{
		return (string) $this->get( 'price.domain', '' );
	}


	/**
	 * Sets the new domain the price is valid for.
	 *
	 * @param string $domain Domain name
	 * @return static Price item for chaining method calls
	 */
	public function setDomain( string $domain ) : static
	{
		return $this->set( 'price.domain', $this->checkDomain( $domain ) );
	}


	/**
	 * Returns the label of the item
	 *
	 * @return string Label of the item
	 */
	public function getLabel() : string
	{
		return (string) $this->get( 'price.label', '' );
	}


	/**
	 * Sets the label of the item
	 *
	 * @param string $label Label of the item
	 * @return static Price item for chaining method calls
	 */
	public function setLabel( ?string $label ) : static
	{
		return $this->set( 'price.label', (string) $label );
	}


	/**
	 * Returns the decimal precision of the price
	 *
	 * @return int Number of decimal digits
	 */
	public function getPrecision() : int
	{
		return $this->precision;
	}


	/**
	 * Returns the quantity the price is valid for.
	 *
	 * @return float Quantity
	 */
	public function getQuantity() : float
	{
		return (float) $this->get( 'price.quantity', 1 );
	}


	/**
	 * Sets the quantity the price is valid for.
	 *
	 * @param float $quantity Quantity
	 * @return static Price item for chaining method calls
	 */
	public function setQuantity( float $quantity ) : static
	{
		return $this->set( 'price.quantity', $quantity );
	}


	/**
	 * Returns the rebate amount.
	 *
	 * @return string Rebate amount
	 */
	public function getRebate() : string
	{
		return $this->formatNumber( (float) $this->get( 'price.rebate', '0.00' ) ) ?? '0.00';
	}


	/**
	 * Sets the new rebate amount.
	 *
	 * @param string|integer|double $price Rebate amount with two digits precision
	 * @return static Price item for chaining method calls
	 */
	public function setRebate( $price ) : static
	{
		return $this->set( 'price.rebate', $this->checkPrice( (string) $price ) );
	}


	/**
	 * Returns the status of the item
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return (int) $this->get( 'price.status', 1 );
	}


	/**
	 * Sets the status of the item
	 *
	 * @param int $status Status of the item
	 * @return static Price item for chaining method calls
	 */
	public function setStatus( int $status ) : static
	{
		return $this->set( 'price.status', $status );
	}


	/**
	 * Returns the tax rate
	 *
	 * @return string Tax rate
	 */
	public function getTaxRate() : string
	{
		$list = (array) $this->get( 'price.taxrates', [] );
		// @phpstan-ignore argument.type
		return (string) $this->formatNumber( $list['tax'] ?? '0.00' );
	}


	/**
	 * Returns all tax rates in percent.
	 *
	 * @return string[] Tax rates for the price
	 */
	 public function getTaxRates() : array
	 {
		// @phpstan-ignore return.type
		return (array) $this->get( 'price.taxrates', [] );
	 }


	/**
	 * Sets the new tax rate.
	 *
	 * @param string|integer|double $taxrate Tax rate with two digits precision
	 * @return static Price item for chaining method calls
	 */
	public function setTaxRate( $taxrate ) : static
	{
		return $this->setTaxRates( ['tax' => $taxrate] );
	}


	/**
	 * Sets the new tax rates in percent
	 *
	 * @param array $taxrates Tax rates with name as key and values with two digits precision
	 * @return static Price item for chaining method calls
	 */
	public function setTaxRates( array $taxrates ) : static
	{
		foreach( $taxrates as $name => $taxrate )
		{
			unset( $taxrates[$name] ); // change index 0 to ''
			$taxrates[$name ?: 'tax'] = $this->checkPrice( (string) $taxrate );
		}

		return $this->set( 'price.taxrates', $taxrates );
	}


	/**
	 * Returns the tax rate flag.
	 *
	 * True if tax is included in the price value, costs and rebate, false if not
	 *
	 * @return bool Tax rate flag for the price
	 */
	public function getTaxFlag() : bool
	{
		return (bool) $this->get( 'price.taxflag', true );
	}


	/**
	 * Sets the new tax flag.
	 *
	 * @param bool $flag True if tax is included in the price value, costs and rebate, false if not
	 * @return static Price item for chaining method calls
	 */
	public function setTaxFlag( bool $flag ) : static
	{
		return $this->set( 'price.taxflag', $flag );
	}


	/**
	 * Returns the tax for the price item
	 *
	 * @return string Tax value with four digits precision
	 * @see mshop/price/taxflag
	 */
	public function getTaxValue() : string
	{
		if( $this->tax === null )
		{
			$taxrate = array_sum( $this->getTaxRates() );

			if( $this->getTaxFlag() !== false ) {
				$this->tax = (string) ( ( $this->getValue() + $this->getCosts() ) / ( 100 + $taxrate ) * $taxrate ); // @phpstan-ignore binaryOp.invalid
			} else {
				$this->tax = (string) ( ( $this->getValue() + $this->getCosts() ) * $taxrate / 100 ); // @phpstan-ignore binaryOp.invalid
			}

			parent::setModified();
		}

		return $this->formatNumber( (float) $this->tax, $this->getPrecision() + 2 ) ?? '0.00';
	}


	/**
	 * Sets the tax amount
	 *
	 * @param string|integer|double $value Tax value with up to four digits precision
	 * @return static Price item for chaining method calls
	 */
	public function setTaxValue( $value ) : static
	{
		$this->tax = $this->checkPrice( (string) $value, $this->getPrecision() + 2 );
		parent::setModified();
		return $this;
	}


	/**
	 * Returns the type of the price item.
	 * Overwritten for different default value.
	 *
	 * @return string Type of the price item
	 */
	public function getType() : string
	{
		return (string) $this->get( 'price.type', 'default' );
	}


	/**
	 * Returns the amount of money.
	 *
	 * @return string|null Price value or NULL for on request
	 */
	public function getValue() : ?string
	{
		// @phpstan-ignore argument.type
		return $this->formatNumber( $this->get( 'price.value' ) );
	}


	/**
	 * Sets the new amount of money.
	 *
	 * @param string|integer|double|null $price Amount with configured precision or NULL for on request
	 * @return static Price item for chaining method calls
	 */
	public function setValue( $price ) : static
	{
		return $this->set( 'price.value', $this->checkPrice( $price ) );
	}


	/**
	 * Sets the modified flag of the object.
	 *
	 * @return static Price item for chaining method calls
	 */
	public function setModified() : static
	{
		$this->tax = null;
		return parent::setModified();
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		$cid = $this->get( '.currencyid' );
		return parent::isAvailable() && $this->getStatus() > 0 && ( $cid === null || $this->getCurrencyId() === $cid );
	}


	/**
	 * Add the given price to the current one.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item which should be added
	 * @param float $quantity Number of times the Price should be added
	 * @return static Price item for chaining method calls
	 */
	public function addItem( \Aimeos\MShop\Price\Item\Iface $item, float $quantity = 1 ) : static
	{
		if( $item->getCurrencyId() != $this->getCurrencyId() )
		{
			$msg = 'Price can not be added. Currency ID "%1$s" of price item and currently used currency ID "%2$s" does not match.';
			throw new \Aimeos\MShop\Price\Exception( sprintf( $msg, $item->getCurrencyId(), $this->getCurrencyId() ) );
		}

		if( $this === $item ) { $item = clone $item; }
		$taxValue = $this->getTaxValue(); // use initial value before it gets reset

		$this->setQuantity( 1 );
		// @phpstan-ignore argument.type
		$this->setValue( $this->getValue() + $item->getValue() * $quantity ); // @phpstan-ignore binaryOp.invalid, binaryOp.invalid
		// @phpstan-ignore argument.type
		$this->setCosts( $this->getCosts() + $item->getCosts() * $quantity ); // @phpstan-ignore binaryOp.invalid, binaryOp.invalid
		// @phpstan-ignore argument.type
		$this->setRebate( $this->getRebate() + $item->getRebate() * $quantity ); // @phpstan-ignore binaryOp.invalid, binaryOp.invalid
		// @phpstan-ignore argument.type
		$this->setTaxValue( $taxValue + $item->getTaxValue() * $quantity ); // @phpstan-ignore binaryOp.invalid, binaryOp.invalid

		return $this;
	}


	/**
	 * Resets the values of the price item.
	 * The currency ID, domain, type and status stays the same.
	 *
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function clear()
	{
		$this->setQuantity( 1 );
		$this->setValue( '0.00' );
		$this->setCosts( '0.00' );
		$this->setRebate( '0.00' );
		$this->setTaxRate( '0.00' );
		$this->tax = null;

		return $this->setModified();
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @type array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Price item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'price.type': $item->setType( (string) $value ); break;
				case 'price.currencyid': $item->setCurrencyId( (string) $value ); break;
				case 'price.quantity': $item->setQuantity( (float) $value ); break;
				case 'price.domain': $item->setDomain( (string) $value ); break;
				case 'price.value': $item->setValue( (string) $value ); break;
				case 'price.costs': $item->setCosts( (string) $value ); break;
				case 'price.rebate': $item->setRebate( (string) $value ); break;
				case 'price.taxvalue': $item->setTaxValue( (string) $value ); break;
				case 'price.taxrate': $item->setTaxRate( (string) $value ); break;
				case 'price.taxrates': $item->setTaxRates( (array) $value ); break;
				case 'price.taxflag': $item->setTaxFlag( (bool) $value ); break;
				case 'price.status': $item->setStatus( (int) $value ); break;
				case 'price.label': $item->setLabel( $value ? (string) $value : null ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool $private True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['price.type'] = $this->getType();
		$list['price.currencyid'] = $this->getCurrencyId();
		$list['price.domain'] = $this->getDomain();
		$list['price.quantity'] = $this->getQuantity();
		$list['price.value'] = $this->getValue();
		$list['price.costs'] = $this->getCosts();
		$list['price.rebate'] = $this->getRebate();
		$list['price.taxvalue'] = $this->getTaxValue();
		$list['price.taxrates'] = $this->getTaxRates();
		$list['price.taxrate'] = $this->getTaxRate();
		$list['price.taxflag'] = $this->getTaxFlag();
		$list['price.status'] = $this->getStatus();
		$list['price.label'] = $this->getLabel();

		return $list;
	}
}
