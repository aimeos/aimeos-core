<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	/**
	 * Initializes the stock item object with the given values
	 *
	 * @param array $values Associative list of product stock key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'stock.', $values );
	}


	/**
	 * Returns the back in stock date of the
	 *
	 * @return string|null Back in stock date of the product
	 */
	public function getDateBack() : ?string
	{
		return $this->get( 'stock.backdate' );
	}


	/**
	 * Sets the product back in stock date.
	 *
	 * @param string|null $backdate New back in stock date of the product
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setDateBack( ?string $backdate ) : \Aimeos\MShop\Stock\Item\Iface
	{
		return $this->set( 'stock.backdate', $this->checkDateFormat( $backdate ) );
	}


	/**
	 * Returns the type code of the product stock item.
	 *
	 * @return string|null Type code of the product stock item
	 */
	public function getType() : ?string
	{
		return $this->get( 'stock.type', 'default' );
	}


	/**
	 * Sets the new type of the product stock item
	 *
	 * @param string $type Type of the product stock item
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setType( string $type ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'stock.type', $this->checkCode( $type ) );
	}


	/**
	 * Returns the ID of the product the stock item belongs to.
	 *
	 * @return string Product ID
	 */
	public function getProductId() : string
	{
		return $this->get( 'stock.productid', '' );
	}


	/**
	 * Sets a new product ID the stock item belongs to.
	 *
	 * @param string $value New product ID
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setProductId( string $value ) : \Aimeos\MShop\Stock\Item\Iface
	{
		return $this->set( 'stock.productid', $value );
	}


	/**
	 * Returns the stock level.
	 *
	 * @return int|null Stock level
	 */
	public function getStockLevel() : ?int
	{
		return $this->get( 'stock.stocklevel' );
	}


	/**
	 * Sets the stock level.
	 *
	 * @param int|null $stocklevel New stock level
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setStockLevel( $stocklevel = null ) : \Aimeos\MShop\Stock\Item\Iface
	{
		return $this->set( 'stock.stocklevel', is_numeric( $stocklevel ) ? (int) $stocklevel : null );
	}


	/**
	 * Returns the expected delivery time frame
	 *
	 * @return string Expected delivery time frame
	 */
	public function getTimeframe() : string
	{
		return $this->get( 'stock.timeframe', '' );
	}


	/**
	 * Sets the expected delivery time frame
	 *
	 * @param string $timeframe Expected delivery time frame
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock stock item for chaining method calls
	 */
	public function setTimeframe( ?string $timeframe ) : \Aimeos\MShop\Stock\Item\Iface
	{
		return $this->set( 'stock.timeframe', (string) $timeframe );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'stock';
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'stock.productid': $item = $item->setProductId( $value ); break;
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
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['stock.productid'] = $this->getProductId();
		$list['stock.stocklevel'] = $this->getStockLevel();
		$list['stock.timeframe'] = $this->getTimeFrame();
		$list['stock.dateback'] = $this->getDateBack();
		$list['stock.type'] = $this->getType();

		return $list;
	}

}
