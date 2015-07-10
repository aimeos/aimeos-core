<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product stock warehouse item implementation.
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Item_Stock_Warehouse_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Product_Item_Stock_Warehouse_Interface
{
	private $_values;

	/**
	 * Initializes the warehouse item object with the given values
	 */
	public function __construct( array $values = array() )
	{
		parent::__construct('product.stock.warehouse.', $values);

		$this->_values = $values;
	}


	/**
	 * Returns the code of the warehouse item.
	 *
	 * @return string Code of the warehouse item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the code of the warehouse item.
	 *
	 * @param string $code New code of the warehouse item
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the label of the warehouse item.
	 *
	 * @return string Label of the warehouse item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the label of the warehouse item.
	 *
	 * @param string $label New label of the warehouse item
	 */
	public function setLabel( $label )
	{
		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the status of the warehouse item.
	 *
	 * @return integer Status of the warehouse item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the warehouse item.
	 *
	 * @param integer $status New status of the warehouse item
	 */
	public function setStatus( $status )
	{
		$this->_values['status'] = (int) $status;
		$this->setModified();
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