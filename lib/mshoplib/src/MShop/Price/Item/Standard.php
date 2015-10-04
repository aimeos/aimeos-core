<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Price
 */


/**
 * Default implementation of a price object.
 *
 * @package MShop
 * @subpackage Price
 */
class MShop_Price_Item_Standard
	extends MShop_Common_Item_ListRef_Base
	implements MShop_Price_Item_Iface
{
	private $values;


	/**
	 * Initalizes the object with the given values
	 *
	 * @param array $values Associative array of key/value pairs for price, costs, rebate and currencyid
	 * @param MShop_Common_Lists_Item_Iface[] $listItems List of list items
	 * @param MShop_Common_Item_Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
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
		return ( isset( $this->values['type'] ) ? (string) $this->values['type'] : null );
	}


	/**
	 * Returns the type ID of the price.
	 *
	 * @return integer|null Type ID of the price
	 */
	public function getTypeId()
	{
		return ( isset( $this->values['typeid'] ) ? (int) $this->values['typeid'] : null );
	}


	/**
	 * Sets the new type ID of the price.
	 *
	 * @param integer $typeid Type ID of the price
	 */
	public function setTypeId( $typeid )
	{
		if( $typeid == $this->getTypeId() ) { return; }

		$this->values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the currency ID.
	 *
	 * @return string|null Three letter ISO currency code (e.g. EUR)
	 */
	public function getCurrencyId()
	{
		return ( isset( $this->values['currencyid'] ) ? (string) $this->values['currencyid'] : null );
	}


	/**
	 * Sets the used currency ID.
	 *
	 * @param string $currencyid Three letter currency code
	 * @throws MShop_Exception If the language ID is invalid
	 */
	public function setCurrencyId( $currencyid )
	{
		if( $currencyid == $this->getCurrencyId() ) { return; }

		$this->checkCurrencyId( $currencyid, false );
		$this->values['currencyid'] = $currencyid;
		$this->setModified();
	}


	/**
	 * Returns the domain the price is valid for.
	 *
	 * @return string Domain name
	 */
	public function getDomain()
	{
		return ( isset( $this->values['domain'] ) ? (string) $this->values['domain'] : '' );
	}


	/**
	 * Sets the new domain the price is valid for.
	 *
	 * @param string $domain Domain name
	 */
	public function setDomain( $domain )
	{
		if( $domain == $this->getDomain() ) { return; }

		$this->values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the quantity the price is valid for.
	 *
	 * @return integer Quantity
	 */
	public function getQuantity()
	{
		return ( isset( $this->values['quantity'] ) ? (int) $this->values['quantity'] : 1 );
	}


	/**
	 * Sets the quantity the price is valid for.
	 *
	 * @param integer $quantity Quantity
	 */
	public function setQuantity( $quantity )
	{
		if( $quantity == $this->getQuantity() ) { return; }

		$this->values['quantity'] = (int) $quantity;
		$this->setModified();
	}


	/**
	 * Returns the amount of money.
	 *
	 * @return string Price value
	 */
	public function getValue()
	{
		return ( isset( $this->values['value'] ) ? (string) $this->values['value'] : '0.00' );
	}


	/**
	 * Sets the new amount of money.
	 *
	 * @param integer|double $price Amount with two digits precision
	 */
	public function setValue( $price )
	{
		if( $price == $this->getValue() ) { return; }

		$this->checkPrice( $price );

		$this->values['value'] = $this->formatNumber( $price );
		$this->setModified();
	}


	/**
	 * Returns costs.
	 *
	 * @return string Costs
	 */
	public function getCosts()
	{
		return ( isset( $this->values['costs'] ) ? (string) $this->values['costs'] : '0.00' );
	}


	/**
	 * Sets the new costs.
	 *
	 * @param integer|double $price Amount with two digits precision
	 */
	public function setCosts( $price )
	{
		if( $price == $this->getCosts() ) { return; }

		$this->checkPrice( $price );

		$this->values['costs'] = $this->formatNumber( $price );
		$this->setModified();
	}


	/**
	 * Returns the rebate amount.
	 *
	 * @return string Rebate amount
	 */
	public function getRebate()
	{
		return ( isset( $this->values['rebate'] ) ? (string) $this->values['rebate'] : '0.00' );
	}


	/**
	 * Sets the new rebate amount.
	 *
	 * @param string $price Rebate amount with two digits precision
	 */
	public function setRebate( $price )
	{
		if( $price == $this->getRebate() ) { return; }

		$this->checkPrice( $price );

		$this->values['rebate'] = $this->formatNumber( $price );
		$this->setModified();
	}


	/**
	 * Returns the tax rate
	 *
	 * @return string Tax rate
	 */
	public function getTaxRate()
	{
		return ( isset( $this->values['taxrate'] ) ? (string) $this->values['taxrate'] : '0.00' );
	}


	/**
	 * Sets the new tax rate.
	 *
	 * @param string $taxrate Tax rate with two digits precision
	 */
	public function setTaxRate( $taxrate )
	{
		if( $taxrate == $this->getTaxRate() ) { return; }

		$this->checkPrice( $taxrate );

		$this->values['taxrate'] = $this->formatNumber( $taxrate );
		$this->setModified();
	}


	/**
	 * Returns the status of the item
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : 0 );
	}


	/**
	 * Sets the status of the item
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return; }

		$this->values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the label of the item
	 *
	 * @return string Label of the item
	 */
	public function getLabel()
	{
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets the label of the item
	 *
	 * @param string $label Label of the item
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return; }

		$this->values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Add the given price to the current one.
	 *
	 * @param MShop_Price_Item_Iface $item Price item which should be added
	 * @param integer $quantity Number of times the Price should be added
	 */
	public function addItem( MShop_Price_Item_Iface $item, $quantity = 1 )
	{
		if( $item->getCurrencyId() != $this->getCurrencyId() )
		{
			throw new MShop_Price_Exception( sprintf( 'Price can not be added. Currency ID "%1$s" of price item and currently used currency ID "%2$s" does not match.', $item->getCurrencyId(), $this->getCurrencyId() ) );
		}

		$this->values['value'] = $this->formatNumber( $this->getValue() + $item->getValue() * $quantity );
		$this->values['costs'] = $this->formatNumber( $this->getCosts() + $item->getCosts() * $quantity );
		$this->values['rebate'] = $this->formatNumber( $this->getRebate() + $item->getRebate() * $quantity );
	}


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
	 * @param MShop_Price_Item_Iface $price Price item to compare with
	 * @return boolean True if equal, false if not
	 * @since 2014.09
	 */
	public function compare( MShop_Price_Item_Iface $price )
	{
		if( $this->getValue() === $price->getValue()
			&& $this->getCosts() === $price->getCosts()
			&& $this->getRebate() === $price->getRebate()
			&& $this->getTaxrate() === $price->getTaxrate()
			&& $this->getQuantity() === $price->getQuantity()
			&& $this->getCurrencyId() === $price->getCurrencyId()
		) {
			return true;
		}

		return false;
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

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
				case 'price.taxrate': $this->setTaxRate( $value ); break;
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
	 * @return array Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['price.typeid'] = $this->getTypeId();
		$list['price.type'] = $this->getType();
		$list['price.currencyid'] = $this->getCurrencyId();
		$list['price.domain'] = $this->getDomain();
		$list['price.quantity'] = $this->getQuantity();
		$list['price.value'] = $this->getValue();
		$list['price.costs'] = $this->getCosts();
		$list['price.rebate'] = $this->getRebate();
		$list['price.taxrate'] = $this->getTaxRate();
		$list['price.status'] = $this->getStatus();
		$list['price.label'] = $this->getLabel();

		return $list;
	}


	/**
	 * Tests if the price is within the requirements.
	 *
	 * @param integer|double $value Monetary value
	 */
	protected function checkPrice( $value )
	{
		if( !is_numeric( $value ) ) {
			throw new MShop_Price_Exception( sprintf( 'Invalid characters in price "%1$s"', $value ) );
		}
	}


	/**
	 * Formats the money value.
	 *
	 * @param string formatted money value
	 */
	protected function formatNumber( $number )
	{
		return number_format( $number, 2, '.', '' );
	}

}
