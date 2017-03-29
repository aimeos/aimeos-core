<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function findItem( $code, array $ref = [], $domain = null, $type = null )
	{
		return $this->findItemBase( array( 'customer.code' => $code ), $ref );
	}


	/**
	 * Returns the customer item object specificed by its ID.
	 *
	 * @param integer $id Unique customer ID referencing an existing customer
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Customer\Item\Iface Returns the customer item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'customer.id', $id, $ref, $default );
	}


	/**
	 * Adds the customer to the groups listed in the customer item
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item
	 */
	protected function addGroups( \Aimeos\MShop\Customer\Item\Iface $item )
	{
		if( count( $item->getGroups() ) === 0 ) {
			return;
		}

		$listMap = [];
		$manager = $this->getSubManager( 'lists' );
		$typeManager = $manager->getSubManager( 'type' );
		$typeId = $typeManager->findItem( 'default', [], 'customer/group', 'default' )->getId();

		$listItem = $manager->createItem();
		$listItem->setParentId( $item->getId() );
		$listItem->setDomain( 'customer/group' );
		$listItem->setTypeId( $typeId );
		$listItem->setStatus( 1 );


		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'customer.lists.parentid', $item->getId() ),
			$search->compare( '==', 'customer.lists.domain', 'customer/group' ),
			$search->compare( '==', 'customer.lists.type.domain', 'customer/group' ),
			$search->compare( '==', 'customer.lists.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		foreach( $manager->searchItems( $search ) as $listid => $listItem ) {
			$listMap[ $listItem->getRefId() ] = $listid;
		}


		$pos = count( $listMap );

		foreach( $item->getGroups() as $gid )
		{
			if( isset( $listMap[$gid] ) )
			{
				unset( $listMap[$gid] );
				continue;
			}

			$listItem->setId( null );
			$listItem->setRefId( $gid );
			$listItem->setPosition( $pos++ );

			$manager->saveItem( $listItem, false );
		}

		$manager->deleteItems( $listMap );
	}


	/**
	 * Creates a new customer item.
	 *
	 * @param array $values List of attributes for customer item
	 * @param array $listItems List items associated to the customer item
	 * @param array $refItems Items referenced by the customer item via the list items
	 * @param array $addresses List of address items of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface New customer item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [], array $addresses = [] )
	{
		if( !isset( $this->addressManager ) ) {
			$this->addressManager = $this->getSubManager( 'address' );
		}

		$helper = $this->getPasswordHelper();
		$address = $this->addressManager->createItem();

		return new \Aimeos\MShop\Customer\Item\Standard( $address, $values, $listItems, $refItems, $this->salt, $helper, $addresses );
	}


	/**
	 * Returns the address items for the given customer IDs
	 *
	 * @param array $custIds List of customer IDs
	 * @return array Associative list of customer IDs / address IDs as keys and items implementing
	 * 	\Aimeos\MShop\Common\Item\Address\Iface as values
	 */
	protected function getAddressItems( array $custIds )
	{
		$list = [];
		$manager = $this->getSubManager( 'address' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.address.parentid', $custIds ) );
		$search->setSlice( 0, 0x7fffffff );

		foreach( $manager->searchItems( $search ) as $id => $addrItem ) {
			$list[$addrItem->getParentId()][$id] = $addrItem;
		}

		return $list;
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
		$options = $config->get( 'mshop/customer/manager/password/options', [] );

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