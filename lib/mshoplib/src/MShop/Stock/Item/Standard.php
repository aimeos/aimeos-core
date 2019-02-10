<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Stock
 */


namespace Aimeos\MShop\Stock\Item;


/**
 * Default product stock item implementation.
 *
 * @package MShop
 * @subpackage Stock
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Stock\Item\Iface
{
	private $values;


	/**
	 * Initializes the stock item object with the given values
	 *
	 * @param array $values Associative list of product stock key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'stock.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the type code of the product stock item.
	 *
	 * @return string|null Type code of the product stock item
	 */
	public function getType()
	{
		if( isset( $this->values['stock.type'] ) ) {
			return (string) $this->values['stock.type'];
		}
	}


	/**
	 * Sets the new type of the product stock item
	 *
	 * @param string $type Type of the product stock item
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setType( $type )
	{
		if( (string) $type !== $this->getType() )
		{
			$this->values['stock.type'] = (string) $type;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the code of the stock item.
	 *
	 * @return string Product code (SKU)
	 */
	public function getProductCode()
	{
		if( isset( $this->values['stock.productcode'] ) ) {
			return (string) $this->values['stock.productcode'];
		}

		return '';
	}


	/**
	 * Sets a new code of the stock item.
	 *
	 * @param string $code New product code (SKU)
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setProductCode( $code )
	{
		if( (string) $code !== $this->getProductCode() )
		{
			$this->values['stock.productcode'] = (string) $code;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Returns the stock level.
	 *
	 * @return integer|null Stock level
	 */
	public function getStocklevel()
	{
		if( isset( $this->values['stock.stocklevel'] ) ) {
			return (int) $this->values['stock.stocklevel'];
		}
	}


	/**
	 * Sets the stock level.
	 *
	 * @param integer|null $stocklevel New stock level
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setStocklevel( $stocklevel )
	{
		$stocklevel = ( is_numeric( $stocklevel ) ? (int) $stocklevel : null );

		if( $stocklevel !== $this->getStocklevel() )
		{
			$this->values['stock.stocklevel'] = $stocklevel;
			$this->setModified();
		}


		return $this;
	}


	/**
	 * Returns the back in stock date of the
	 *
	 * @return string|null Back in stock date of the product
	 */
	public function getDateBack()
	{
		if( isset( $this->values['stock.backdate'] ) ) {
			return (string) $this->values['stock.backdate'];
		}
	}


	/**
	 * Sets the product back in stock date.
	 *
	 * @param string|null $backdate New back in stock date of the product
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setDateBack( $backdate )
	{
		if( $backdate !== $this->getDateBack() )
		{
			$this->values['stock.backdate'] = $this->checkDateFormat( $backdate );
			$this->setModified();
		}


		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'stock';
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function fromArray( array &$list )
	{
		$item = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'stock.productcode': $item = $item->setProductCode( $value ); break;
				case 'stock.stocklevel': $item = $item->setStocklevel( $value ); break;
				case 'stock.dateback': $item = $item->setDateBack( $value ); break;
				case 'stock.type': $item = $item->setType( $value ); break;
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

		$list['stock.productcode'] = $this->getProductCode();
		$list['stock.stocklevel'] = $this->getStocklevel();
		$list['stock.dateback'] = $this->getDateBack();
		$list['stock.type'] = $this->getType();

		return $list;
	}

}
