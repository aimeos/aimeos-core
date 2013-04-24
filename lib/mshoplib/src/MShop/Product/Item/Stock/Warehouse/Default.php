<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
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
		if( strlen( $code ) > 32 ) {
			throw new MShop_Exception( sprintf( 'Code must not be longer than 32 characters' ) );
		}

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