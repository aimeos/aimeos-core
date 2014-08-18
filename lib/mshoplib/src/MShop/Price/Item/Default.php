<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Price
 */


/**
 * Default implementation of a price object.
 *
 * @package MShop
 * @subpackage Price
 */
class MShop_Price_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Price_Item_Interface
{
	private $_values;


	/**
	 * Initalizes the object with the given values
	 *
	 * @param array $values Associative array of key/value pairs for price, costs, rebate and currencyid
	 * @param MShop_Common_List_Item_Interface[] $listItems List of list items
	 * @param MShop_Common_Item_Interface[] $refItems List of referenced items
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( 'price.', $values, $listItems, $refItems );

		$this->_values = $values;
	}


	/**
	 * Returns the type of the price.
	 *
	 * @return string|null Type of the price
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns the type ID of the price.
	 *
	 * @return integer|null Type ID of the price
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 * Sets the new type ID of the price.
	 *
	 * @param integer $typeid Type ID of the price
	 */
	public function setTypeId( $typeid )
	{
		if ( $typeid == $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the currency ID.
	 *
	 * @return string|null Three letter ISO currency code (e.g. EUR)
	 */
	public function getCurrencyId()
	{
		return ( isset( $this->_values['currencyid'] ) ? (string) $this->_values['currencyid'] : null );
	}


	/**
	 * Sets the used currency ID.
	 *
	 * @param string $currencyid Three letter currency code
	 * @throws MShop_Exception If the language ID is invalid
	 */
	public function setCurrencyId( $currencyid )
	{
		if ( $currencyid == $this->getCurrencyId() ) { return; }

		$this->_checkCurrencyId( $currencyid, false );
		$this->_values['currencyid'] = $currencyid;
		$this->setModified();
	}


	/**
	 * Returns the domain the price is valid for.
	 *
	 * @return string Domain name
	 */
	public function getDomain()
	{
		return ( isset( $this->_values['domain'] ) ? (string) $this->_values['domain'] : '' );
	}


	/**
	 * Sets the new domain the price is valid for.
	 *
	 * @param string $domain Domain name
	 */
	public function setDomain( $domain )
	{
		if ( $domain == $this->getDomain() ) { return; }

		$this->_values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the quantity the price is valid for.
	 *
	 * @return integer Quantity
	 */
	public function getQuantity()
	{
		return ( isset( $this->_values['quantity'] ) ? (int) $this->_values['quantity'] : 1 );
	}


	/**
	 * Sets the quantity the price is valid for.
	 *
	 * @param integer $quantity Quantity
	 */
	public function setQuantity( $quantity )
	{
		if ( $quantity == $this->getQuantity() ) { return; }

		$this->_values['quantity'] = (int) $quantity;
		$this->setModified();
	}


	/**
	 * Returns the amount of money.
	 *
	 * @return string Price value
	 */
	public function getValue()
	{
		return ( isset( $this->_values['value'] ) ? (string) $this->_values['value'] : '0.00' );
	}


	/**
	 * Sets the new amount of money.
	 *
	 * @param integer|double $price Amount with two digits precision
	 */
	public function setValue( $price )
	{
		if ( $price == $this->getValue() ) { return; }

		$this->_checkPrice( $price );

		$this->_values['value'] = $this->_formatNumber( $price );
		$this->setModified();
	}


	/**
	 * Returns costs.
	 *
	 * @return string Costs
	 */
	public function getCosts()
	{
		return ( isset( $this->_values['costs'] ) ? (string) $this->_values['costs'] : '0.00' );
	}


	/**
	 * Sets the new costs.
	 *
	 * @param integer|double $price Amount with two digits precision
	 */
	public function setCosts( $price )
	{
		if ( $price == $this->getCosts() ) { return; }

		$this->_checkPrice( $price );

		$this->_values['costs'] = $this->_formatNumber( $price );
		$this->setModified();
	}


	/**
	 * Returns the rebate amount.
	 *
	 * @return string Rebate amount
	 */
	public function getRebate()
	{
		return ( isset( $this->_values['rebate'] ) ? (string) $this->_values['rebate'] : '0.00' );
	}


	/**
	 * Sets the new rebate amount.
	 *
	 * @param string $price Rebate amount with two digits precision
	 */
	public function setRebate( $price )
	{
		if ( $price == $this->getRebate() ) { return; }

		$this->_checkPrice( $price );

		$this->_values['rebate'] = $this->_formatNumber( $price );
		$this->setModified();
	}


	/**
	 * Returns the tax rate
	 *
	 * @return string Tax rate
	 */
	public function getTaxRate()
	{
		return ( isset( $this->_values['taxrate'] ) ? (string) $this->_values['taxrate'] : '0.00' );
	}


	/**
	 * Sets the new tax rate.
	 *
	 * @param string $taxrate Tax rate with two digits precision
	 */
	public function setTaxRate( $taxrate )
	{
		if ( $taxrate == $this->getTaxRate() ) { return; }

		$this->_checkPrice( $taxrate );

		$this->_values['taxrate'] = $this->_formatNumber( $taxrate );
		$this->setModified();
	}


	/**
	 * Returns the status of the item
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the item
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the label of the item
	 *
	 * @return string Label of the item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the label of the item
	 *
	 * @param string $label Label of the item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Add the given price to the current one.
	 *
	 * @param MShop_Price_Item_Interface $item Price item which should be added
	 * @param integer $quantity Number of times the Price should be added
	 */
	public function addItem( MShop_Price_Item_Interface $item, $quantity = 1 )
	{
		if( $item->getCurrencyId() != $this->getCurrencyId() )
		{
			throw new MShop_Price_Exception( sprintf( 'Price can not be added. Currency ID "%1$s" of price item and currently used currency ID "%2$s" does not match.', $item->getCurrencyId(), $this->getCurrencyId() ) );
		}

		$this->_values['value'] = $this->_formatNumber( $this->getValue() + $item->getValue() * $quantity );
		$this->_values['costs'] = $this->_formatNumber( $this->getCosts() + $item->getCosts() * $quantity );
		$this->_values['rebate'] = $this->_formatNumber( $this->getRebate() + $item->getRebate() * $quantity );
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
	protected function _checkPrice( $value )
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
	protected function _formatNumber( $number )
	{
		return number_format( $number, 2, '.', '' );
	}

}
