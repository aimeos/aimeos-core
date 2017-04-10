<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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

		return null;
	}


	/**
	 * Returns the localized name of the type
	 *
	 * @return string|null Localized name of the type
	 */
	public function getTypeName()
	{
		if( isset( $this->values['stock.typename'] ) ) {
			return (string) $this->values['stock.typename'];
		}

		return null;
	}


	/**
	 * Returns the type id of the product stock item
	 *
	 * @return integer|null Type of the product stock item
	 */
	public function getTypeId()
	{
		if( isset( $this->values['stock.typeid'] ) ) {
			return (int) $this->values['stock.typeid'];
		}

		return null;
	}


	/**
	 * Sets the new type of the product stock item
	 *
	 * @param integer|null $id Type of the product stock item
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setTypeId( $id )
	{
		if ( $id == $this->getTypeId() ) { return $this; }

		$this->values['stock.typeid'] = (int) $id;
		$this->setModified();

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
		if( $code == $this->getProductCode() ) { return $this; }

		$this->values['stock.productcode'] = (string) $code;
		$this->setModified();

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

		return null;
	}


	/**
	 * Sets the stock level.
	 *
	 * @param integer|null $stocklevel New stock level
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setStocklevel( $stocklevel )
	{
		if( $stocklevel === $this->getStocklevel() ) { return $this; }

		$this->values['stock.stocklevel'] = ( is_numeric( $stocklevel ) ? (int) $stocklevel : null );
		$this->setModified();

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

		return null;
	}


	/**
	 * Sets the product back in stock date.
	 *
	 * @param string|null $backdate New back in stock date of the product
	 * @return \Aimeos\MShop\Stock\Item\Iface Stock item for chaining method calls
	 */
	public function setDateBack( $backdate )
	{
		if( $backdate == $this->getDateBack() ) { return $this; }

		$this->values['stock.backdate'] = $this->checkDateFormat( $backdate );;
		$this->setModified();

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
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'stock.productcode': $this->setProductCode( $value ); break;
				case 'stock.stocklevel': $this->setStocklevel( $value ); break;
				case 'stock.dateback': $this->setDateBack( $value ); break;
				case 'stock.typeid': $this->setTypeId( $value ); break;
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

		$list['stock.productcode'] = $this->getProductCode();
		$list['stock.stocklevel'] = $this->getStocklevel();
		$list['stock.dateback'] = $this->getDateBack();
		$list['stock.typename'] = $this->getTypeName();
		$list['stock.type'] = $this->getType();

		if( $private === true ) {
			$list['stock.typeid'] = $this->getTypeId();
		}

		return $list;
	}

}