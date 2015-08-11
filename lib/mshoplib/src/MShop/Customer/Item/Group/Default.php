<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Customer
 */


/**
 * Default customer group object
 *
 * @package MShop
 * @subpackage Customer
 */
class MShop_Customer_Item_Group_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Customer_Item_Group_Interface
{
	private $_values;


	/**
	 * Initializes the customer group item
	 *
	 * @param array $values List of key/value pairs of the customer group
	 */
	public function __construct( $values = array() )
	{
		parent::__construct( 'customer.group.', $values );

		$this->_values = $values;
	}


	/**
	 * Returns the code of the customer group
	 *
	 * @return string Code of the customer group
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the new code of the customer group
	 *
	 * @param string $value Code of the customer group
	 */
	public function setCode( $value )
	{
		if( $value == $this->getCode() ) { return; }

		$this->_values['code'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the label of the customer group
	 *
	 * @return string Label of the customer group
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the customer group
	 *
	 * @param string $value Label of the customer group
	 */
	public function setLabel( $value )
	{
		if( $value == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $value;
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
				case 'customer.group.code': $this->setCode( $value ); break;
				case 'customer.group.label': $this->setLabel( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return array Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['customer.group.code'] = $this->getCode();
		$list['customer.group.label'] = $this->getLabel();

		return $list;
	}
}
