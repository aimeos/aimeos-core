<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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


	/**
	 * Initalizes the object with the given values
	 *
	 * @param array $values Associative array of key/value pairs for price, costs, rebate and currencyid
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( 'price.', $values, $listItems, $refItems );

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

		return null;
	}


	/**
	 * Returns the localized name of the type
	 *
	 * @return string|null Localized name of the type
	 */
	public function getTypeName()
	{
		if( isset( $this->values['price.typename'] ) ) {
			return (string) $this->values['price.typename'];
		}

		return null;
	}


	/**
	 * Returns the type ID of the price.
	 *
	 * @return integer|null Type ID of the price
	 */
	public function getTypeId()
	{
		if( isset( $this->values['price.typeid'] ) ) {
			return (int) $this->values['price.typeid'];
		}

		return null;
	}


	/**
	 * Sets the new type ID of the price.
	 *
	 * @param integer $typeid Type ID of the price
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return $this; }

		$this->values['price.typeid'] = (int) $typeid;
		$this->setModified();

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

		return null;
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
		if( $currencyid == $this->getCurrencyId() ) { return $this; }

		$this->values['price.currencyid'] = $this->checkCurrencyId( $currencyid, false );
		$this->setModified();

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
		if( $domain == $this->getDomain() ) { return $this; }

		$this->values['price.domain'] = (string) $domain;
		$this->setModified();

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
		if( $label == $this->getLabel() ) { return $this; }

		$this->values['price.label'] = (string) $label;
		$this->setModified();

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
		if( $quantity == $this->getQuantity() ) { return $this; }

		$this->values['price.quantity'] = (int) $quantity;
		$this->setModified();

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
	 * @param integer|double $price Amount with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setValue( $price )
	{
		if( $price == $this->getValue() ) { return $this; }

		$this->values['price.value'] = $this->checkPrice( $price );
		$this->setModified();

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
	 * @param integer|double $price Amount with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setCosts( $price )
	{
		if( $price == $this->getCosts() ) { return $this; }

		$this->values['price.costs'] = $this->checkPrice( $price );
		$this->setModified();

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
	 * @param string $price Rebate amount with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setRebate( $price )
	{
		if( $price == $this->getRebate() ) { return $this; }

		$this->values['price.rebate'] = $this->checkPrice( $price );
		$this->setModified();

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
	 * @param string $taxrate Tax rate with two digits precision
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setTaxRate( $taxrate )
	{
		if( $taxrate == $this->getTaxRate() ) { return $this; }

		$this->values['price.taxrate'] = $this->checkPrice( $taxrate );
		$this->setModified();

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
		if( $flag == $this->getTaxFlag() ) { return $this; }

		$this->values['price.taxflag'] = (bool) $flag;
		$this->setModified();

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

			$this->values['price.tax'] = $this->formatNumber( $tax, 4 );
		}

		return (string) $this->values['price.tax'];
	}


	/**
	 * Sets the tax amount
	 *
	 * @param integer|double $value Tax value with up to four digits precision
	 */
	public function setTaxValue( $value )
	{
		if( $value == $this->getTaxValue() ) { return $this; }

		$this->values['price.tax'] = $this->checkPrice( $value, 4 );
		parent::setModified(); // don't unset tax immediately again

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

		return 0;
	}


	/**
	 * Sets the status of the item
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Price\Item\Iface Price item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values['price.status'] = (int) $status;
		$this->setModified();

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
	 *
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


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );
		unset( $list['price.type'], $list['price.typename'] );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'price.typeid': $this->setTypeId( $value ); break;
				case 'price.currencyid': $this->setCurrencyId( $value ); break;
				case 'price.domain': $this->setDomain( $value ); break;
				case 'price.quantity': $this->setQuantity( $value ); break;
				case 'price.value': $this->setValue( $value ); break;
				case 'price.costs': $this->setCosts( $value ); break;
				case 'price.rebate': $this->setRebate( $value ); break;
				case 'price.taxvalue': $this->setTaxValue( $value ); break;
				case 'price.taxrate': $this->setTaxRate( $value ); break;
				case 'price.taxflag': $this->setTaxFlag( $value ); break;
				case 'price.status': $this->setStatus( $value ); break;
				case 'price.label': $this->setLabel( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
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
		$list['price.typename'] = $this->getTypeName();
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

		if( $private === true ) {
			$list['price.typeid'] = $this->getTypeId();
		}

		return $list;
	}
}
