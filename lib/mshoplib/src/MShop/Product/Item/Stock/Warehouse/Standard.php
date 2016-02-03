<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item\Stock\Warehouse;


/**
 * Default product stock warehouse item implementation.
 * @package MShop
 * @subpackage Product
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Product\Item\Stock\Warehouse\Iface
{
	private $values;

	/**
	 * Initializes the warehouse item object with the given values
	 */
	public function __construct( array $values = array() )
	{
		parent::__construct( 'product.stock.warehouse.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the code of the warehouse item.
	 *
	 * @return string Code of the warehouse item
	 */
	public function getCode()
	{
		if( isset( $this->values['product.stock.warehouse.code'] ) ) {
			return (string) $this->values['product.stock.warehouse.code'];
		}

		return '';
	}


	/**
	 * Sets the code of the warehouse item.
	 *
	 * @param string $code New code of the warehouse item
	 * @return \Aimeos\MShop\Product\Item\Stock\Warehouse\Iface Product stock warehouse item for chaining method calls
	 */
	public function setCode( $code )
	{
		if( $code == $this->getCode() ) { return $this; }

		$this->values['product.stock.warehouse.code'] = (string) $this->checkCode( $code );;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the label of the warehouse item.
	 *
	 * @return string Label of the warehouse item
	 */
	public function getLabel()
	{
		if( isset( $this->values['product.stock.warehouse.label'] ) ) {
			return (string) $this->values['product.stock.warehouse.label'];
		}

		return '';
	}


	/**
	 * Sets the label of the warehouse item.
	 *
	 * @param string $label New label of the warehouse item
	 * @return \Aimeos\MShop\Product\Item\Stock\Warehouse\Iface Product stock warehouse item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return $this; }

		$this->values['product.stock.warehouse.label'] = (string) $label;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the status of the warehouse item.
	 *
	 * @return integer Status of the warehouse item
	 */
	public function getStatus()
	{
		if( isset( $this->values['product.stock.warehouse.status'] ) ) {
			return (int) $this->values['product.stock.warehouse.status'];
		}

		return 0;
	}


	/**
	 * Sets the status of the warehouse item.
	 *
	 * @param integer $status New status of the warehouse item
	 * @return \Aimeos\MShop\Product\Item\Stock\Warehouse\Iface Product stock warehouse item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values['product.stock.warehouse.status'] = (int) $status;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'product/stock/warehouse';
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
				case 'product.stock.warehouse.code': $this->setCode( $value ); break;
				case 'product.stock.warehouse.label': $this->setLabel( $value ); break;
				case 'product.stock.warehouse.status': $this->setStatus( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['product.stock.warehouse.code'] = $this->getCode();
		$list['product.stock.warehouse.label'] = $this->getLabel();
		$list['product.stock.warehouse.status'] = $this->getStatus();

		return $list;
	}

}