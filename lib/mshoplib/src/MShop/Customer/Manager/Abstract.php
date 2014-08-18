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
	extends MShop_Common_Manager_ListRef_Abstract
	implements MShop_Customer_Manager_Interface
{
	private $_salt;
	private $_addressManager;


	/**
	 * Initializes a new customer manager object using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-customer' );

		$this->_salt = $context->getConfig()->get( 'mshop/customer/manager/default/salt/', 'mshop' );
	}


	/**
	 * Instantiates a new customer item object.
	 *
	 * @return MShop_Customer_Item_Interface
	 */
	public function createItem()
	{
		$values = array( 'siteid'=> $this->_getContext()->getLocale()->getSiteId() );

		return $this->_createItem( $values );
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
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Customer_Item_Interface Returns the customer item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'customer.id', $id, $ref );
	}


	/**
	 * Creates a new customer item.
	 *
	 * @param array $values List of attributes for customer item
	 * @param array $listItems List items associated to the customer item
	 * @param array $refItems Items referenced by the customer item via the list items
	 * @return MShop_Customer_Item_Interface New customer item
	 */
	protected function _createItem( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		if( !isset( $this->_addressManager ) ) {
			$this->_addressManager = $this->getSubManager( 'address' );
		}

		$address = $this->_addressManager->createItem();

		return new MShop_Customer_Item_Default( $address, $values, $listItems, $refItems, $this->_salt );
	}
}