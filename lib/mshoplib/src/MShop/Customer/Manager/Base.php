<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Manager;


/**
 * Base class with common methods for all customer implementations.
 *
 * @package MShop
 * @subpackage Customer
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\ListRef\Base
	implements \Aimeos\MShop\Customer\Manager\Iface
{
	private $salt;
	private $helper;
	private $addressManager;


	/**
	 * Initializes a new customer manager object using the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-customer' );

		/** mshop/customer/manager/salt
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
		 * @see mshop/customer/manager/password/name
		 * @sse mshop/customer/manager/password/options
		 */
		$this->salt = $context->getConfig()->get( 'mshop/customer/manager/salt', 'mshop' );
	}


	/**
	 * Instantiates a new customer item object.
	 *
	 * @return \Aimeos\MShop\Customer\Item\Iface
	 */
	public function createItem()
	{
		$values = array( 'siteid'=> $this->getContext()->getLocale()->getSiteId() );

		return $this->createItemBase( $values );
	}


	/**
	 * Creates a criteria object for searching.
	 *
	 * @param boolean $default Include default criteria like the status
	 * @return \Aimeos\MW\Criteria\Iface Search criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->createSearchBase( 'customer' );
		}

		return parent::createSearch();
	}


	/**
	 * Returns the customer item object specificed by its ID.
	 *
	 * @param integer $id Unique customer ID referencing an existing customer
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return \Aimeos\MShop\Customer\Item\Iface Returns the customer item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->getItemBase( 'customer.id', $id, $ref );
	}


	/**
	 * Creates a new customer item.
	 *
	 * @param array $values List of attributes for customer item
	 * @param array $listItems List items associated to the customer item
	 * @param array $refItems Items referenced by the customer item via the list items
	 * @return \Aimeos\MShop\Customer\Item\Iface New customer item
	 */
	protected function createItemBase( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		if( !isset( $this->addressManager ) ) {
			$this->addressManager = $this->getSubManager( 'address' );
		}

		$helper = $this->getPasswordHelper();
		$address = $this->addressManager->createItem();

		return new \Aimeos\MShop\Customer\Item\Standard( $address, $values, $listItems, $refItems, $this->salt, $helper );
	}


	/**
	 * Returns a password helper object based on the configuration.
	 *
	 * @return \Aimeos\MShop\Common\Item\Helper\Password\Iface Password helper object
	 * @throws \Aimeos\MShop\Exception If the name is invalid or the class isn't found
	 */
	protected function getPasswordHelper()
	{
		if( $this->helper ) {
			return $this->helper;
		}

		$config = $this->getContext()->getConfig();

		/** mshop/customer/manager/password/name
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
		 * @see mshop/customer/manager/salt
		 * @see mshop/customer/manager/password/options
		 */
		$name = $config->get( 'mshop/customer/manager/password/name', 'Standard' );

		/** mshop/customer/manager/password/options
		 * List of options used by the password helper classes
		 *
		 * Each hash method may need an arbitrary number of options specific
		 * for the hash method. This may include the number of iterations the
		 * method is applied or the separator between salt and password.
		 *
		 * @param string Associative list of key/value pairs
		 * @since 2015.01
		 * @category Developer
		 * @see mshop/customer/manager/password/name
		 * @sse mshop/customer/manager/salt
		 */
		$options = $config->get( 'mshop/customer/manager/password/options', array() );

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\\Aimeos\\MShop\\Common\\Item\\Helper\\Password\\' . $name : '<not a string>';
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = '\\Aimeos\\MShop\\Common\\Item\\Helper\\Password\\Iface';
		$classname = '\\Aimeos\\MShop\\Common\\Item\\Helper\\Password\\' . $name;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$helper = new $classname( $options );

		if( !( $helper instanceof $iface ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		$this->helper = $helper;

		return $helper;
	}
}