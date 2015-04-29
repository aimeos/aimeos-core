<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default impelementation of a product item.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Product_Item_Interface
{
	private $_values;


	/**
	 * Initializes the item object.
	 *
	 * @param array $values Parameter for initializing the basic properties
	 * @param array $listItems List of items implementing MShop_Common_List_Item_Interface
	 * @param array $refItems List of domain/items pairs where the items implements MShop_Common_Item_Interface
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( 'product.', $values, $listItems, $refItems );

		$this->_values = $values;
	}


	/**
	 * Returns the type ID of the product item.
	 *
	 * @return integer|null Type ID of the product item
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 * Sets the new type ID of the product item.
	 *
	 * @param integer $typeid New type ID of the product item
	 */
	public function setTypeId( $typeid )
	{
		if ( $typeid == $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the type of the product item.
	 *
	 * @return string|null Type of the product item
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns the status of the product item.
	 *
	 * @return integer Status of the product item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the new status of the product item.
	 *
	 * @param integer $status New status of the product item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
		$this->setModified();
	}


	/**
	 * Returns the code of the product item.
	 *
	 * @return string Code of the product item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the new code of the product item.
	 *
	 * @param string $code New code of product item
	 */
	public function setCode( $code )
	{
		$this->_checkCode( $code );

		if ( $code == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $code;
		$this->setModified();
	}


	/**
	 * Returns the supplier code of the product item.
	 *
	 * @return string supplier code of the product item
	 */
	public function getSupplierCode()
	{
		return ( isset( $this->_values['suppliercode'] ) ? (string) $this->_values['suppliercode'] : '' );
	}


	/**
	 * Sets the new supplier code of the product item.
	 *
	 * @param string $suppliercode New supplier code of the product item
	 */
	public function setSupplierCode( $suppliercode )
	{
		if ( $suppliercode == $this->getSupplierCode() ) { return; }

		$this->_values['suppliercode'] = (string) $suppliercode;
		$this->setModified();
	}


	/**
	 * Returns the label of the product item.
	 *
	 * @return string Label of the product item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets a new label of the product item.
	 *
	 * @param string $label New label of the product item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the starting point of time, in which the product is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateStart()
	{
		return ( isset( $this->_values['start'] ) ? (string) $this->_values['start'] : null );
	}


	/**
	 * Sets a new starting point of time, in which the product is available.
	 *
	 * @param string|null New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateStart( $date )
	{
		if ( $date === $this->getDateStart() ) { return; }

		$this->_checkDateFormat( $date );

		$this->_values['start'] = ( $date !== null ? (string) $date : null );
		$this->setModified();
	}


	/**
	 * Returns the ending point of time, in which the product is available.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getDateEnd()
	{
		return ( isset( $this->_values['end'] ) ? (string) $this->_values['end'] : null );
	}


	/**
	 * Sets a new ending point of time, in which the product is available.
	 *
	 * @param string|null New ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function setDateEnd( $date )
	{
		if ( $date === $this->getDateEnd() ) { return; }

		$this->_checkDateFormat( $date );

		$this->_values['end'] = ( $date !== null ? (string) $date : null );
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
				case 'product.typeid': $this->setTypeId( $value ); break;
				case 'product.code': $this->setCode( $value ); break;
				case 'product.label': $this->setLabel( $value ); break;
				case 'product.status': $this->setStatus( $value ); break;
				case 'product.suppliercode': $this->setSupplierCode( $value ); break;
				case 'product.datestart': $this->setDateStart( $value ); break;
				case 'product.dateend': $this->setDateEnd( $value ); break;
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

		$list['product.typeid'] = $this->getTypeId();
		$list['product.type'] = $this->getType();
		$list['product.code'] = $this->getCode();
		$list['product.label'] = $this->getLabel();
		$list['product.status'] = $this->getStatus();
		$list['product.suppliercode'] = $this->getSupplierCode();
		$list['product.datestart'] = $this->getDateStart();
		$list['product.dateend'] = $this->getDateEnd();

		return $list;
	}
}
