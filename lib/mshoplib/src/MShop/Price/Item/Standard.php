<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Price
 */


namespace Aimeos\MShop\Price\Item;


/**
 * Default implementation of a price object.
 *
 * @package MShop
 * @subpackage Price
 */
class Standard extends Base
{
	private $values;
	private $precision;


	/**
	 * Initalizes the object with the given values
	 *
	 * @param array $values Associative array of key/value pairs for price, costs, rebate and currencyid
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param integer $precision Number of decimal digits
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [], $precision = 2 )
	{
		parent::__construct( 'price.', $values, $listItems, $refItems, $precision );

		$this->precision = $precision;
		$this->values = $values;
	}


	/**
	 * Returns the type of the price.
	 *
	 * @return string|null Type of the price
	 */
	public function getType()
	{
		if( isset( $this->values['price.type'] ) ) {
			return (string) $this->values['price.type'];
		}
	}


	/**
	 * Sets the new type of the price.
	 *
	 * @param string $type Type of the price
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setType( $type )
	{
		if( (string) $type !== $this->getType() )
		{
			$this->values['price.type'] = (string) $type;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the currency ID.
	 *
	 * @return string|null Three letter ISO currency code (e.g. EUR)
	 */
	public function getCurrencyId()
	{
		if( isset( $this->values['price.currencyid'] ) ) {
			return (string) $this->values['price.currencyid'];
		}
	}


	/**
	 * Sets the used currency ID.
	 *
	 * @param string $currencyid Three letter currency code
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 * @throws \Aimeos\MShop\Exception If the language ID is invalid
	 */
	public function setCurrencyId( $currencyid )
	{
		if( (string) $currencyid !== $this->getCurrencyId() )
		{
			$this->values['price.currencyid'] = (string) $this->checkCurrencyId( $currencyid, false );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the domain the price is valid for.
	 *
	 * @return string Domain name
	 */
	public function getDomain()
	{
		if( isset( $this->values['price.domain'] ) ) {
			return (string) $this->values['price.domain'];
		}

		return '';
	}


	/**
	 * Sets the new domain the price is valid for.
	 *
	 * @param string $domain Domain name
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setDomain( $domain )
	{
		if( (string) $domain !== $this->getDomain() )
		{
			$this->values['price.domain'] = (string) $domain;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the label of the item
	 *
	 * @return string Label of the item
	 */
	public function getLabel()
	{
		if( isset( $this->values['price.label'] ) ) {
			return (string) $this->values['price.label'];
		}

		return '';
	}


	/**
	 * Sets the label of the item
	 *
	 * @param string $label Label of the item
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( (string) $label !== $this->getLabel() )
		{
			$this->values['price.label'] = (string) $label;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the quantity the price is valid for.
	 *
	 * @return integer Quantity
	 */
	public function getQuantity()
	{
		if( isset( $this->values['price.quantity'] ) ) {
			return (int) $this->values['price.quantity'];
		}

		return 1;
	}


	/**
	 * Sets the quantity the price is valid for.
	 *
	 * @param integer $quantity Quantity
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setQuantity( $quantity )
	{
		if( (int) $quantity !== $this->getQuantity() )
		{
			$this->values['price.quantity'] = (int) $quantity;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the amount of money.
	 *
	 * @return string Price value
	 */
	public function getValue()
	{
		if( isset( $this->values['price.value'] ) ) {
			return (string) $this->values['price.value'];
		}

		return '0.00';
	}


	/**
	 * Sets the new amount of money.
	 *
	 * @param string|integer|double $price Amount with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setValue( $price )
	{
		if( (string) $price !== $this->getValue() )
		{
			$this->values['price.value'] = (string) $this->checkPrice( $price );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns costs.
	 *
	 * @return string Costs
	 */
	public function getCosts()
	{
		if( isset( $this->values['price.costs'] ) ) {
			return (string) $this->values['price.costs'];
		}

		return '0.00';
	}


	/**
	 * Sets the new costs.
	 *
	 * @param string|integer|double $price Amount with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setCosts( $price )
	{
		if( (string) $price !== $this->getCosts() )
		{
			$this->values['price.costs'] = (string) $this->checkPrice( $price );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the rebate amount.
	 *
	 * @return string Rebate amount
	 */
	public function getRebate()
	{
		if( isset( $this->values['price.rebate'] ) ) {
			return (string) $this->values['price.rebate'];
		}

		return '0.00';
	}


	/**
	 * Sets the new rebate amount.
	 *
	 * @param string|integer|double $price Rebate amount with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setRebate( $price )
	{
		if( (string) $price !== $this->getRebate() )
		{
			$this->values['price.rebate'] = (string) $this->checkPrice( $price );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the tax rate
	 *
	 * @return string Tax rate
	 */
	public function getTaxRate()
	{
		if( isset( $this->values['price.taxrate'] ) ) {
			return (string) $this->values['price.taxrate'];
		}

		return '0.00';
	}


	/**
	 * Sets the new tax rate.
	 *
	 * @param string|integer|double $taxrate Tax rate with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setTaxRate( $taxrate )
	{
		if( (string) $taxrate !== $this->getTaxRate() )
		{
			$this->values['price.taxrate'] = (string) $this->checkPrice( $taxrate );
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the tax rate flag.
	 *
	 * True if tax is included in the price value, costs and rebate, false if not
	 *
	 * @return boolean Tax rate flag for the price
	 */
	public function getTaxFlag()
	{
		if( isset( $this->values['price.taxflag'] ) ) {
			return (bool) $this->values['price.taxflag'];
		}

		return true;
	}


	/**
	 * Sets the new tax flag.
	 *
	 * @param boolean $flag True if tax is included in the price value, costs and rebate, false if not
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	*/
	public function setTaxFlag( $flag )
	{
		if( (bool) $flag !== $this->getTaxFlag() )
		{
			$this->values['price.taxflag'] = (bool) $flag;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the tax for the price item
	 *
	 * @return string Tax value with four digits precision
	 * @see mshop/price/taxflag
	 */
	public function getTaxValue()
	{
		if( !isset( $this->values['price.tax'] ) )
		{
			$taxrate = $this->getTaxRate();

			if( $this->getTaxFlag() !== false ) {
				$tax = ( $this->getValue() + $this->getCosts() ) / ( 100 + $taxrate ) * $taxrate;
			} else {
				$tax = ( $this->getValue() + $this->getCosts() ) * $taxrate / 100;
			}

			$this->values['price.tax'] = $this->formatNumber( $tax, $this->precision + 2 );
		}

		return (string) $this->values['price.tax'];
	}


	/**
	 * Sets the tax amount
	 *
	 * @param string|integer|double $value Tax value with up to four digits precision
	 */
	public function setTaxValue( $value )
	{
		if( (string) $value !== $this->getTaxValue() )
		{
			$this->values['price.tax'] = (string) $this->checkPrice( $value, $this->precision + 2 );
			parent::setModified(); // don't unset tax immediately again
		}

		return $this;
	}


	/**
	 * Returns the status of the item
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['price.status'] ) ) {
			return (int) $this->values['price.status'];
		}

		return 1;
	}


	/**
	 * Sets the status of the item
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( (int) $status !== $this->getStatus() )
		{
			$this->values['price.status'] = (int) $status;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Sets the modified flag of the object.
	 */
	public function setModified()
	{
		parent::setModified();
		unset( $this->values['price.tax'] );
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable()
	{
		return parent::isAvailable() && $this->getStatus() > 0
			&& ( $this->values['currencyid'] === null || $this->getCurrencyId() === $this->values['currencyid'] );
	}


	/**
	 * Add the given price to the current one.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $item Price item which should be added
	 * @param integer $quantity Number of times the Price should be added
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function addItem( \Aimeos\MShop\Price\Item\Iface $item, $quantity = 1 )
	{
		if( $item->getCurrencyId() != $this->getCurrencyId() )
		{
			$msg = 'Price can not be added. Currency ID "%1$s" of price item and currently used currency ID "%2$s" does not match.';
			throw new \Aimeos\MShop\Price\Exception( sprintf( $msg, $item->getCurrencyId(), $this->getCurrencyId() ) );
		}

		if( $this === $item ) { $item = clone $item; }
		$taxValue = $this->getTaxValue();

		$this->setQuantity( 1 );
		$this->setValue( $this->getValue() + $item->getValue() * $quantity );
		$this->setCosts( $this->getCosts() + $item->getCosts() * $quantity );
		$this->setRebate( $this->getRebate() + $item->getRebate() * $quantity );
		$this->setTaxValue( $taxValue + $item->getTaxValue() * $quantity );

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
		unset( $this->values['price.tax'] );

		return $this;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'price.type': $item = $item->setType( $value ); break;
				case 'price.currencyid': $item = $item->setCurrencyId( $value ); break;
				case 'price.domain': $item = $item->setDomain( $value ); break;
				case 'price.quantity': $item = $item->setQuantity( $value ); break;
				case 'price.value': $item = $item->setValue( $value ); break;
				case 'price.costs': $item = $item->setCosts( $value ); break;
				case 'price.rebate': $item = $item->setRebate( $value ); break;
				case 'price.taxvalue': $item = $item->setTaxValue( $value ); break;
				case 'price.taxrate': $item = $item->setTaxRate( $value ); break;
				case 'price.taxflag': $item = $item->setTaxFlag( $value ); break;
				case 'price.status': $item = $item->setStatus( $value ); break;
				case 'price.label': $item = $item->setLabel( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
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
		$list['price.taxrate'] = $this->getTaxRate();
		$list['price.taxflag'] = $this->getTaxFlag();
		$list['price.status'] = $this->getStatus();
		$list['price.label'] = $this->getLabel();

		return $list;
	}
}
