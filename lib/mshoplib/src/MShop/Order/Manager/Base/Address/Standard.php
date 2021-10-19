<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Base\Address;


/**
 * Default order address manager implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Base\Address\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'order.base.address.id' => array(
			'code' => 'order.base.address.id',
			'internalcode' => 'mordbaad."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base_address" AS mordbaad ON ( mordba."id" = mordbaad."baseid" )' ),
			'label' => 'Address ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.address.baseid' => array(
			'code' => 'order.base.address.baseid',
			'internalcode' => 'mordbaad."baseid"',
			'label' => 'Order ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.address.siteid' => array(
			'code' => 'order.base.address.siteid',
			'internalcode' => 'mordbaad."siteid"',
			'label' => 'Address site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.address.addressid' => array(
			'code' => 'order.base.address.addressid',
			'internalcode' => 'mordbaad."addrid"',
			'label' => 'Customer address ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.address.type' => array(
			'code' => 'order.base.address.type',
			'internalcode' => 'mordbaad."type"',
			'label' => 'Address type',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.company' => array(
			'code' => 'order.base.address.company',
			'internalcode' => 'mordbaad."company"',
			'label' => 'Address company',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.vatid' => array(
			'code' => 'order.base.address.vatid',
			'internalcode' => 'mordbaad."vatid"',
			'label' => 'Address Vat ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.salutation' => array(
			'code' => 'order.base.address.salutation',
			'internalcode' => 'mordbaad."salutation"',
			'label' => 'Address salutation',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.title' => array(
			'code' => 'order.base.address.title',
			'internalcode' => 'mordbaad."title"',
			'label' => 'Address title',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.firstname' => array(
			'code' => 'order.base.address.firstname',
			'internalcode' => 'mordbaad."firstname"',
			'label' => 'Address firstname',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.lastname' => array(
			'code' => 'order.base.address.lastname',
			'internalcode' => 'mordbaad."lastname"',
			'label' => 'Address lastname',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.address1' => array(
			'code' => 'order.base.address.address1',
			'internalcode' => 'mordbaad."address1"',
			'label' => 'Address part one',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.address2' => array(
			'code' => 'order.base.address.address2',
			'internalcode' => 'mordbaad."address2"',
			'label' => 'Address part two',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.address3' => array(
			'code' => 'order.base.address.address3',
			'internalcode' => 'mordbaad."address3"',
			'label' => 'Address part three',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.postal' => array(
			'code' => 'order.base.address.postal',
			'internalcode' => 'mordbaad."postal"',
			'label' => 'Address postal',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.city' => array(
			'code' => 'order.base.address.city',
			'internalcode' => 'mordbaad."city"',
			'label' => 'Address city',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.state' => array(
			'code' => 'order.base.address.state',
			'internalcode' => 'mordbaad."state"',
			'label' => 'Address state',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.countryid' => array(
			'code' => 'order.base.address.countryid',
			'internalcode' => 'mordbaad."countryid"',
			'label' => 'Address country ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.languageid' => array(
			'code' => 'order.base.address.languageid',
			'internalcode' => 'mordbaad."langid"',
			'label' => 'Address language ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.telephone' => array(
			'code' => 'order.base.address.telephone',
			'internalcode' => 'mordbaad."telephone"',
			'label' => 'Address telephone',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.email' => array(
			'code' => 'order.base.address.email',
			'internalcode' => 'mordbaad."email"',
			'label' => 'Address email',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.telefax' => array(
			'code' => 'order.base.address.telefax',
			'internalcode' => 'mordbaad."telefax"',
			'label' => 'Address telefax',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.website' => array(
			'code' => 'order.base.address.website',
			'internalcode' => 'mordbaad."website"',
			'label' => 'Address website',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'order.base.address.longitude' => array(
			'code' => 'order.base.address.longitude',
			'internalcode' => 'mordbaad."longitude"',
			'label' => 'Address longitude',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.address.latitude' => array(
			'code' => 'order.base.address.latitude',
			'internalcode' => 'mordbaad."latitude"',
			'label' => 'Address latitude',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.address.position' => array(
			'code' => 'order.base.address.position',
			'internalcode' => 'mordbaad."pos"',
			'label' => 'Address position',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'order.base.address.birthday' => array(
			'code' => 'order.base.address.birthday',
			'internalcode' => 'mordbaad."birthday"',
			'label' => 'Address birthday',
			'type' => 'date',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.address.ctime' => array(
			'code' => 'order.base.address.ctime',
			'internalcode' => 'mordbaad."ctime"',
			'label' => 'Address create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.address.mtime' => array(
			'code' => 'order.base.address.mtime',
			'internalcode' => 'mordbaad."mtime"',
			'label' => 'Address modify date/time',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.base.address.editor' => array(
			'code' => 'order.base.address.editor',
			'internalcode' => 'mordbaad."editor"',
			'label' => 'Address editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-order' );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param array|string $key Search key or list of keys to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key, string $value = null, string $type = null ) : \Aimeos\Map
	{
		/** mshop/order/manager/base/address/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/base/address/aggregate/ansi
		 */

		/** mshop/order/manager/base/address/aggregate/ansi
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * Groups all records by the values in the key column and counts their
		 * occurence. The matched records can be limited by the given criteria
		 * from the order database. The records must be from one of the sites
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
		 * @param string SQL statement for aggregating order items
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/order/manager/base/address/insert/ansi
		 * @see mshop/order/manager/base/address/update/ansi
		 * @see mshop/order/manager/base/address/newid/ansi
		 * @see mshop/order/manager/base/address/delete/ansi
		 * @see mshop/order/manager/base/address/search/ansi
		 * @see mshop/order/manager/base/address/count/ansi
		 */
		$cfgkey = 'mshop/order/manager/base/address/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order.base.address'], $value, $type );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Order\Manager\Base\Address\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/order/manager/base/address/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/order/manager/base/address/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface New order address item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['order.base.address.siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
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
		$search = parent::filter( $default );
		$search->setSortations( [$search->sort( '+', 'order.base.address.id' )] );

		return $search;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Order\Manager\Base\Address\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/base/address/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/base/address/delete/ansi
		 */

		/** mshop/order/manager/base/address/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the order database.
		 * The records must be from the site that is configured via the
		 * context item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/order/manager/base/address/insert/ansi
		 * @see mshop/order/manager/base/address/update/ansi
		 * @see mshop/order/manager/base/address/newid/ansi
		 * @see mshop/order/manager/base/address/search/ansi
		 * @see mshop/order/manager/base/address/count/ansi
		 */
		$path = 'mshop/order/manager/base/address/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Creates a order base address item object for the given item id.
	 *
	 * @param string $id Id of the order base address item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface Returns order base address item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'order.base.address.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/order/manager/base/address/submanagers';
		return $this->getResourceTypeBase( 'order/base/address', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/order/manager/base/address/submanagers
		 * List of manager names that can be instantiated by the order base address manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2014.03
		 * @category Developer
		 */
		$path = 'mshop/order/manager/base/address/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Creates a new manager for order
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions
	 * @throws \Aimeos\MShop\Order\Exception If creating manager failed
	 */

	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/base/address/name
		 * Class name of the used order base address manager implementation
		 *
		 * Each default order base address manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Order\Manager\Base\Address\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Order\Manager\Base\Address\Myaddress
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/order/manager/base/address/name = Myaddress
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyAddress"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/base/address/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base address manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base address manager.
		 *
		 *  mshop/order/manager/base/address/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the order base address manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/address/decorators/global
		 * @see mshop/order/manager/base/address/decorators/local
		 */

		/** mshop/order/manager/base/address/decorators/global
		 * Adds a list of globally available decorators only to the order base address manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base
		 * address manager.
		 *
		 *  mshop/order/manager/base/address/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order base
		 * address manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/address/decorators/excludes
		 * @see mshop/order/manager/base/address/decorators/local
		 */

		/** mshop/order/manager/base/address/decorators/local
		 * Adds a list of local decorators only to the order base address manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Order\Manager\Base\Address\Decorator\*") around the
		 * order base address manager.
		 *
		 *  mshop/order/manager/base/address/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Order\Manager\Base\Address\Decorator\Decorator2" only
		 * to the order base address manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/address/decorators/excludes
		 * @see mshop/order/manager/base/address/decorators/global
		 */

		return $this->getSubManagerBase( 'order', 'base/address/' . $manager, $name );
	}


	/**
	 * Inserts the new order base address items
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface $item order address item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Base\Address\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Base\Address\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );
			$columns = $this->getObject()->getSaveAttributes();

			if( $id === null )
			{
				/** mshop/order/manager/base/address/insert/mysql
				 * Inserts a new order record into the database table
				 *
				 * @see mshop/order/manager/base/address/insert/ansi
				 */

				/** mshop/order/manager/base/address/insert/ansi
				 * Inserts a new order record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the order item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The order of the columns must correspond to the
				 * order in the save() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/order/manager/base/address/update/ansi
				 * @see mshop/order/manager/base/address/newid/ansi
				 * @see mshop/order/manager/base/address/delete/ansi
				 * @see mshop/order/manager/base/address/search/ansi
				 * @see mshop/order/manager/base/address/count/ansi
				 */
				$path = 'mshop/order/manager/base/address/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/order/manager/base/address/update/mysql
				 * Updates an existing order record in the database
				 *
				 * @see mshop/order/manager/base/address/update/ansi
				 */

				/** mshop/order/manager/base/address/update/ansi
				 * Updates an existing order record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the order item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the save() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/order/manager/base/address/insert/ansi
				 * @see mshop/order/manager/base/address/newid/ansi
				 * @see mshop/order/manager/base/address/delete/ansi
				 * @see mshop/order/manager/base/address/search/ansi
				 * @see mshop/order/manager/base/address/count/ansi
				 */
				$path = 'mshop/order/manager/base/address/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getBaseId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getAddressId() );
			$stmt->bind( $idx++, $item->getType() );
			$stmt->bind( $idx++, $item->getCompany() );
			$stmt->bind( $idx++, $item->getVatID() );
			$stmt->bind( $idx++, $item->getSalutation() );
			$stmt->bind( $idx++, $item->getTitle() );
			$stmt->bind( $idx++, $item->getFirstname() );
			$stmt->bind( $idx++, $item->getLastname() );
			$stmt->bind( $idx++, $item->getAddress1() );
			$stmt->bind( $idx++, $item->getAddress2() );
			$stmt->bind( $idx++, $item->getAddress3() );
			$stmt->bind( $idx++, $item->getPostal() );
			$stmt->bind( $idx++, $item->getCity() );
			$stmt->bind( $idx++, $item->getState() );
			$stmt->bind( $idx++, $item->getCountryId() );
			$stmt->bind( $idx++, $item->getLanguageId() );
			$stmt->bind( $idx++, $item->getTelephone() );
			$stmt->bind( $idx++, $item->getEmail() );
			$stmt->bind( $idx++, $item->getTelefax() );
			$stmt->bind( $idx++, $item->getWebsite() );
			$stmt->bind( $idx++, $item->getLongitude() );
			$stmt->bind( $idx++, $item->getLatitude() );
			$stmt->bind( $idx++, (int) $item->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getBirthday() );
			$stmt->bind( $idx++, $date );
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $context->getLocale()->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx++, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/order/manager/base/address/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/order/manager/base/address/newid/ansi
				 */

				/** mshop/order/manager/base/address/newid/ansi
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * As soon as a new record is inserted into the database table,
				 * the database server generates a new and unique identifier for
				 * that record. This ID can be used for retrieving, updating and
				 * deleting that specific record from the table again.
				 *
				 * For MySQL:
				 *  SELECT LAST_INSERT_ID()
				 * For PostgreSQL:
				 *  SELECT currval('seq_mord_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mord_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/order/manager/base/address/insert/ansi
				 * @see mshop/order/manager/base/address/update/ansi
				 * @see mshop/order/manager/base/address/delete/ansi
				 * @see mshop/order/manager/base/address/search/ansi
				 * @see mshop/order/manager/base/address/count/ansi
				 */
				$path = 'mshop/order/manager/base/address/newid';
				$id = $this->newId( $conn, $path );
			}

			$item->setId( $id );

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $item;
	}


	/**
	 * Search for order base address items based on the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Base\Address\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = [];

		try
		{
			$required = array( 'order.base.address' );

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$level = $context->getConfig()->get( 'mshop/order/manager/sitemode', $level );

			/** mshop/order/manager/base/address/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/address/search/ansi
			 */

			/** mshop/order/manager/base/address/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the order
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the SELECT statement can retrieve all records
			 * from the current site and the complete sub-tree of sites.
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
			 * If the records that are retrieved should be ordered by one or more
			 * columns, the generated string of column / sort direction pairs
			 * replaces the ":order" placeholder. In case no ordering is required,
			 * the complete ORDER BY part including the "\/*-orderby*\/...\/*orderby-*\/"
			 * markers is removed to speed up retrieving the records. Columns of
			 * sub-managers can also be used for ordering the result set but then
			 * no index can be used.
			 *
			 * The number of returned records can be limited and can start at any
			 * number between the begining and the end of the result set. For that
			 * the ":size" and ":start" placeholders are replaced by the
			 * corresponding values from the criteria object. The default values
			 * are 0 for the start and 100 for the size value.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for searching items
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/order/manager/base/address/insert/ansi
			 * @see mshop/order/manager/base/address/update/ansi
			 * @see mshop/order/manager/base/address/newid/ansi
			 * @see mshop/order/manager/base/address/delete/ansi
			 * @see mshop/order/manager/base/address/count/ansi
			 */
			$cfgPathSearch = 'mshop/order/manager/base/address/search';

			/** mshop/order/manager/base/address/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/base/address/count/ansi
			 */

			/** mshop/order/manager/base/address/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the order
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the statement can count all records from the
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
			 * Both, the strings for ":joins" and for ":cond" are the same as for
			 * the "search" SQL statement.
			 *
			 * Contrary to the "search" statement, it doesn't return any records
			 * but instead the number of records that have been found. As counting
			 * thousands of records can be a long running task, the maximum number
			 * of counted records is limited for performance reasons.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for counting items
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/order/manager/base/address/insert/ansi
			 * @see mshop/order/manager/base/address/update/ansi
			 * @see mshop/order/manager/base/address/newid/ansi
			 * @see mshop/order/manager/base/address/delete/ansi
			 * @see mshop/order/manager/base/address/search/ansi
			 */
			$cfgPathCount = 'mshop/order/manager/base/address/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== null )
				{
					if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
						$items[$row['order.base.address.id']] = $item;
					}
				}
			}
			catch( \Exception $e )
			{
				$results->finish();
				throw $e;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return map( $items );
	}


	/**
	 * Creates new order base address item object.
	 *
	 * @param array $values Possible optional array keys can be given: id, type, firstname, lastname
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface New order base address item object
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MShop\Order\Item\Base\Address\Iface
	{
		return new \Aimeos\MShop\Order\Item\Base\Address\Standard( $values );
	}
}
