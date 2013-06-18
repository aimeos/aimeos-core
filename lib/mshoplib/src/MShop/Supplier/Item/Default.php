<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Supplier
 */


/**
 * Interface for supplier DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Supplier
 */
class MShop_Supplier_Item_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Supplier_Item_Interface
{
	private $_values;

	/**
	 * Initializes the supplier item object
	 *
	 * @param array $values List of attributes that belong to the supplier item
	 */
	public function __construct( array $values = array() )
	{
		parent::__construct('supplier.', $values);

		$this->_values = $values;
	}


	/**
	 * Returns the label of the supplier item.
	 *
	 * @return string label of the supplier item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the supplier item.
	 *
	 * @param string $value label of the supplier item
	 */
	public function setLabel( $value )
	{
		if ( $value == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the code of the supplier item.
	 *
	 * @return string Code of the supplier item
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the new code of the supplier item.
	 *
	 * @param string $value Code of the supplier item
	 */
	public function setCode( $value )
	{
		$this->_checkCode( $value );

		$this->_values['code'] = (string) $value;
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
	 * Sets the new status of the supplier item.
	 *
	 * @param integer $value status of the supplier item
	 */
	public function setStatus( $value )
	{
		if ( $value == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $value;
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

		$list['supplier.label'] = $this->getLabel();
		$list['supplier.code'] = $this->getCode();
		$list['supplier.status'] = $this->getStatus();

		return $list;
	}

}
