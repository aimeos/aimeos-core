<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Customer
 */


/**
 * Base class with common methods for all customer implementations.
 *
 * @package MShop
 * @subpackage Customer
 */
abstract class MShop_Customer_Manager_Abstract
	extends MShop_Common_Manager_Abstract
	implements MShop_Customer_Manager_Interface
{
	/**
	 * Instantiates a new customer item object.
	 *
	 * @return MShop_Customer_Item_Interface New customer item object
	 */
	public function createItem()
	{
		return $this->_createItem( $this->getSubManager( 'address' )->createItem() );
	}


	/**
	 * Creates a criteria object for searching.
	 *
	 * @param boolean $default Include default criteria like the status
	 * @return MW_Common_Criteria_Interface Search criteria object
	 */
	public function createSearch($default = false)
	{
		if( $default === true ) {
			return parent::_createSearch('customer');
		}

		return parent::createSearch();
	}


	/**
	 * Returns the customer item object specificed by its ID.
	 *
	 * @param integer $id Unique customer ID referencing an existing customer
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Customer_Item_Interface Returns the customer item of the given id
	 * @throws MShop_Customer_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'customer.id', $id, $ref );
	}


	/**
	 * Creates a new customer item.
	 *
	 * @param MShop_Common_Item_Address_Interface $address $address Billingaddress of the customer item
	 * @param array $values List of attributes for customer item
	 * @return MShop_Customer_Item_Interface New customer item
	 */
	protected function _createItem( MShop_Common_Item_Address_Interface $address, array $values = array() )
	{
		return new MShop_Customer_Item_Default( $address, $values );
	}
}