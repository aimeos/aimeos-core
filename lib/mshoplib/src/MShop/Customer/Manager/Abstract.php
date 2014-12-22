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
	private $_helper;
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

		/** mshop/customer/manager/default/salt
		 * Password salt for all customer passwords of the installation
		 *
		 * The default password salt is used if no user-specific salt can be
		 * stored in the database along with the user data. It's highly recommended
		 * to set the salt to a random string of at least eight chars using
		 * characters, digits and special characters
		 *
		 * @param string Installation wide password salt
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 * @see mshop/customer/manager/default/password-helper
		 */
		$this->_salt = $context->getConfig()->get( 'mshop/customer/manager/default/salt', 'mshop' );

		/** mshop/customer/manager/default/password/name
		 * Last part of the name for building the password helper item
		 *
		 * The password helper encode given passwords and salts using the
		 * implemented hashing method in the required format. String format and
		 * hash algorithm needs to be the same when comparing the encoded
		 * password to the one provided by the user after login.
		 *
		 * @param string Name of the password helper implementation
		 * @since 2015.01
		 * @category Developer
		 * @see mshop/customer/manager/default/salt
		 */
		$name = $context->getConfig()->get( 'mshop/customer/manager/default/password/name', 'Default' );

		/** mshop/customer/manager/default/password/options
		 * List of options used by the password helper classes
		 *
		 * Each hash method may need an arbitrary number of options specific
		 * for the hash method. This may include the number of iterations the
		 * method is applied or the separator between salt and password.
		 *
		 * @param string Associative list of key/value pairs
		 * @since 2015.01
		 * @category Developer
		 * @see mshop/customer/manager/default/password/name
		 */
		$options = $context->getConfig()->get( 'mshop/customer/manager/default/password/options', array() );

		$this->_helper = $this->_createPasswordHelper( $name, $options );

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

		return new MShop_Customer_Item_Default( $address, $values, $listItems, $refItems, $this->_salt, $this->_helper );
	}


	/**
	 * Returns a password helper object for the given name.
	 *
	 * @param string $name Last part of the password helper class name
	 * @param array Associative list of key/value pairs of options specific for the hashing method
	 * @return MShop_Common_Item_Helper_Password_Interface Password helper object
	 * @throws MShop_Exception If the name is invalid or the class isn't found
	 */
	protected function _createPasswordHelper( $name, $options )
	{
		if ( ctype_alnum($name) === false )
		{
			$classname = is_string($name) ? 'MShop_Common_Item_Helper_Password_' . $name : '<not a string>';
			throw new MShop_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'MShop_Common_Item_Helper_Password_Interface';
		$classname = 'MShop_Common_Item_Helper_Password_' . $name;

		if( class_exists( $classname ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$helper =  new $classname( $options );

		if( !( $helper instanceof $iface ) ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $helper;
	}
}