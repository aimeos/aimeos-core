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
	public function getStockLevel()
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
	public function setStockLevel( $stocklevel )
	{
		$stocklevel = ( is_numeric( $stocklevel ) ? (int) $stocklevel : null );

		if( $stocklevel !== $this->getStockLevel() )
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

	/**
	 * Returns the expected delivery time frame
	 *
	 * @return string|null Expected delivery time frame
	 */
	public function getTimeframe()
	{
		if( isset( $this->values['stock.timeframe'] ) ) {
			return (string) $this->values['stock.timeframe'];
		}
	}

	/**
	 * Sets the expected delivery time frame
	 *
	 * @param string|null $timeframe Expected delivery time frame
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock stock item for chaining method calls
	 */
	public function setTimeframe( $timeframe )
	{
		if( $timeframe !== $this->getTimeframe() )
		{
			$this->values['stock.timeframe'] = (string) $timeframe;
			$this->setModified();
		}

		return $this;
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'stock.productcode': $item = $item->setProductCode( $value ); break;
				case 'stock.stocklevel': $item = $item->setStockLevel( $value ); break;
				case 'stock.timeframe': $item = $item->setTimeFrame( $value ); break;
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
		$list['stock.stocklevel'] = $this->getStockLevel();
		$list['stock.timeframe'] = $this->getTimeFrame();
		$list['stock.dateback'] = $this->getDateBack();
		$list['stock.type'] = $this->getType();

		return $list;
	}

}
