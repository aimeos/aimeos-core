<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
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
	extends \Aimeos\MShop\Common\Manager\Base
{
	use \Aimeos\MShop\Common\Manager\ListRef\Traits;
	use \Aimeos\MShop\Common\Manager\AddressRef\Traits;
	use \Aimeos\MShop\Common\Manager\PropertyRef\Traits;


	private $salt;
	private $helper;


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
	 * @param bool $default Include default criteria like the status
	 * @return \Aimeos\MW\Criteria\Iface Search criteria object
	 */
	public function createSearch( bool $default = false ) : \Aimeos\MW\Criteria\Iface
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
	 * @param bool $default True to add default criteria
	 * @return \Aimeos\MShop\Customer\Item\Iface Item object
	 */
	public function findItem( string $code, array $ref = [], string $domain = null, string $type = null,
		bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->findItemBase( array( 'customer.code' => $code ), $ref, $default );
	}


	/**
	 * Returns the customer item object specificed by its ID.
	 *
	 * @param string $id Unique customer ID referencing an existing customer
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool $default Add default criteria
	 * @return \Aimeos\MShop\Customer\Item\Iface Returns the customer item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( string $id, array $ref = [], bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'customer.id', $id, $ref, $default );
	}


	/**
	 * Adds the customer to the groups listed in the customer item
	 *
	 * @param \Aimeos\MShop\Customer\Item\Iface $item Customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface $item Modified customer item
	 */
	protected function addGroups( \Aimeos\MShop\Customer\Item\Iface $item ): \Aimeos\MShop\Customer\Item\Iface
	{
		$pos = 0;
		$groupIds = [];

		$manager = $this->getObject()->getSubManager( 'lists' );
		$listItems = $item->getListItems( 'customer/group', 'default', null, false );

		foreach( $item->getGroups() as $refId )
		{
			if( ( $litem = $item->getListItem( 'customer/group', 'default', $refId, false ) ) !== null ) {
				unset( $listItems[$litem->getId()] );
			} else {
				$litem = $manager->createItem()->setType( 'default' );
			}

			$item->addListItem( 'customer/group', $litem->setRefId( $refId )->setPosition( $pos++ ) );
		}

		return $item->deleteListItems( $listItems->toArray() );
	}


	/**
	 * Creates a new customer item.
	 *
	 * @param array $values List of attributes for customer item
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Address\Iface[] $addrItems List of address items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 * @return \Aimeos\MShop\Customer\Item\Iface New customer item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [],
		array $addrItems = [], array $propItems = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$helper = $this->getPasswordHelper();
		$address = new \Aimeos\MShop\Common\Item\Address\Simple( 'customer.', $values );

		return new \Aimeos\MShop\Customer\Item\Standard(
			$address, $values, $listItems, $refItems, $addrItems, $propItems, $helper, $this->salt
		);
	}


	/**
	 * Returns a password helper object based on the configuration.
	 *
	 * @return \Aimeos\MShop\Common\Helper\Password\Iface Password helper object
	 * @throws \Aimeos\MShop\Exception If the name is invalid or the class isn't found
	 */
	protected function getPasswordHelper() : \Aimeos\MShop\Common\Helper\Password\Iface
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
			$classname = is_string( $name ) ? '\Aimeos\MShop\Common\Helper\Password\\' . $name : '<not a string>';
			throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$classname = '\Aimeos\MShop\Common\Helper\Password\\' . $name;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$helper = new $classname( $options );

		self::checkClass( \Aimeos\MShop\Common\Helper\Password\Iface::class, $helper );

		$this->helper = $helper;

		return $helper;
	}
}
