<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
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
	use \Aimeos\MShop\Common\Item\TypeRef\Traits;


	/**
	 * Returns the back in stock date of the
	 *
	 * @return string|null Back in stock date of the product
	 */
	public function getDateBack() : ?string
	{
		// @phpstan-ignore return.type
		return $this->get( 'stock.dateback' );
	}


	/**
	 * Sets the product back in stock date.
	 *
	 * @param string|null $dateback New back in stock date of the product
	 * @return static Stock item for chaining method calls
	 */
	public function setDateBack( ?string $dateback ) : static
	{
		return $this->set( 'stock.dateback', \Aimeos\Utils::datetime( $dateback ) );
	}


	/**
	 * Returns the ID of the product the stock item belongs to.
	 *
	 * @return string Product ID
	 */
	public function getProductId() : string
	{
		return (string) $this->get( 'stock.productid', '' );
	}


	/**
	 * Sets a new product ID the stock item belongs to.
	 *
	 * @param string $value New product ID
	 * @return static Stock item for chaining method calls
	 */
	public function setProductId( string $value ) : static
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
		// @phpstan-ignore return.type
		return $this->get( 'stock.stocklevel' );
	}


	/**
	 * Sets the stock level.
	 *
	 * @param int|null $stocklevel New stock level
	 * @return static Stock item for chaining method calls
	 */
	public function setStockLevel( $stocklevel = null ) : static
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
		return (string) $this->get( 'stock.timeframe', '' );
	}


	/**
	 * Sets the expected delivery time frame
	 *
	 * @param string $timeframe Expected delivery time frame
	 * @return static Stock stock item for chaining method calls
	 */
	public function setTimeframe( ?string $timeframe ) : static
	{
		return $this->set( 'stock.timeframe', (string) $timeframe );
	}


	/**
	 * Returns the type of the stock item.
	 * Overwritten for different default value.
	 *
	 * @return string Type of the stock item
	 */
	public function getType() : string
	{
		return (string) $this->get( 'stock.type', 'default' );
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @type array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Stock item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'stock.productid': $item->setProductId( (string) $value ); break;
				case 'stock.stocklevel': $item->setStockLevel( is_numeric( $value ) ? (int) $value : null ); break;
				case 'stock.timeframe': $item->setTimeFrame( $value ? (string) $value : null ); break;
				case 'stock.dateback': $item->setDateBack( $value ? (string) $value : null ); break;
				case 'stock.type': $item->setType( (string) $value ); break;
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

		$list['stock.productid'] = $this->getProductId();
		$list['stock.stocklevel'] = $this->getStockLevel();
		$list['stock.timeframe'] = $this->getTimeFrame();
		$list['stock.dateback'] = $this->getDateBack();
		$list['stock.type'] = $this->getType();

		return $list;
	}

}
