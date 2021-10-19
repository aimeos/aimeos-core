<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	use \Aimeos\MShop\Common\Manager\ListsRef\Traits;
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
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param array|string $key Search key or list of key to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map
	{
		/** mshop/customer/manager/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/customer/manager/aggregate/ansi
		 */

		/** mshop/customer/manager/aggregate/ansi
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * Groups all records by the values in the key column and counts their
		 * occurence. The matched records can be limited by the given criteria
		 * from the customer database. The records must be from one of the sites
		 * that are configured via the context item. If the current site is part
		 * of a tree of sites, the statement can count all records from the
		 * current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * This statement doesn't return any records. Instead, it returns pairs
		 * of the different values found in the key column together with the
		 * number of records that have been found for that key values.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for aggregating customer items
		 * @since 2021.04
		 * @category Developer
		 * @see mshop/customer/manager/insert/ansi
		 * @see mshop/customer/manager/update/ansi
		 * @see mshop/customer/manager/newid/ansi
		 * @see mshop/customer/manager/delete/ansi
		 * @see mshop/customer/manager/search/ansi
		 * @see mshop/customer/manager/count/ansi
		 */

		$cfgkey = 'mshop/customer/manager/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['customer'], $value, $type );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\MW\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\MW\Criteria\Iface
	{
		return $this->filterBase( 'customer', $default );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Customer\Item\Iface Item object
	 */
	public function find( string $code, array $ref = [], string $domain = null, string $type = null,
		?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->findBase( array( 'customer.code' => $code ), $ref, $default );
	}


	/**
	 * Returns the customer item object specificed by its ID.
	 *
	 * @param string $id Unique customer ID referencing an existing customer
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Customer\Item\Iface Returns the customer item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
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
				unset( $listItems[$litem->getId()], $listItems['__customer/group_default_' . $refId] );
			} else {
				$litem = $manager->create()->setType( 'default' );
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
			$msg = $this->getContext()->translate( 'mshop', 'Invalid characters in class name "%1$s"' );
			throw new \Aimeos\MShop\Exception( sprintf( $msg, $name ) );
		}

		$classname = '\Aimeos\MShop\Common\Helper\Password\\' . $name;

		if( class_exists( $classname ) === false )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Class "%1$s" not available' );
			throw new \Aimeos\MShop\Exception( sprintf( $msg, $classname ) );
		}

		$helper = new $classname( $options );

		self::checkClass( \Aimeos\MShop\Common\Helper\Password\Iface::class, $helper );

		$this->helper = $helper;

		return $helper;
	}
}
