<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 * @package MShop
 * @subpackage Order
 */

namespace Aimeos\MShop\Order\Manager\Cart;


/**
 * Default implementation for order cart manager.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Cart\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'order.cart.id' => array(
			'code' => 'order.cart.id',
			'internalcode' => 'mordca."id"',
			'label' => 'Cart ID',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.cart.siteid' => array(
			'code' => 'order.cart.siteid',
			'internalcode' => 'mordca."siteid"',
			'label' => 'Cart site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.cart.customerid' => array(
			'code' => 'order.cart.customerid',
			'internalcode' => 'mordca."customerid"',
			'label' => 'Cart customer ID',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.cart.name' => array(
			'code' => 'order.cart.name',
			'internalcode' => 'mordca."name"',
			'label' => 'Cart name',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.cart.content' => array(
			'code' => 'order.cart.content',
			'internalcode' => 'mordca."content"',
			'label' => 'Cart content',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
		),
		'order.cart.ctime' => array(
			'code' => 'order.cart.ctime',
			'internalcode' => 'mordca."ctime"',
			'label' => 'Cart create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.cart.mtime' => array(
			'code' => 'order.cart.mtime',
			'internalcode' => 'mordca."mtime"',
			'label' => 'Cart modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'order.cart.editor' => array(
			'code' => 'order.cart.editor',
			'internalcode' => 'mordca."editor"',
			'label' => 'Cart editor',
			'type' => 'string',
			'internaltype' => \Aimeos\Base\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-order' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Order\Manager\Cart\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/order/manager/cart/submanagers';
		foreach( $this->context()->config()->get( $path, [] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/order/manager/cart/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface New order cart item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['order.cart.siteid'] = $values['order.cart.siteid'] ?? $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Adds or updates an order cart object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Cart\Iface $item Order cart object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Cart\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Cart\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );
		$date = date( 'Y-m-d H:i:s' );

		/** mshop/order/manager/cart/insert/mysql
		 * Inserts a new cart record into the database table or updates an existing one
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
		 * @param string SQL statement for inserting or updating records
		 * @since 2022.10
		 * @category Developer
		 * @see mshop/order/manager/cart/newid/ansi
		 * @see mshop/order/manager/cart/delete/ansi
		 * @see mshop/order/manager/cart/search/ansi
		 * @see mshop/order/manager/cart/count/ansi
		 */
		$path = 'mshop/order/manager/cart/insert';

		$sql = $this->getSqlConfig( 'mshop/order/manager/cart/insert' );
		$stmt = $this->getCachedStatement( $conn, $path, $sql );
		$idx = 1;

		// insert
		$stmt->bind( $idx++, $item->getCustomerId() );
		$stmt->bind( $idx++, $item->getContent() );
		$stmt->bind( $idx++, $item->getName() );
		$stmt->bind( $idx++, $date ); //mtime
		$stmt->bind( $idx++, $context->editor() );
		$stmt->bind( $idx++, $context->locale()->getSiteId() );
		$stmt->bind( $idx++, $date ); //ctime
		$stmt->bind( $idx++, $item->getId() );
		// update
		$stmt->bind( $idx++, $item->getCustomerId() );
		$stmt->bind( $idx++, $item->getContent() );
		$stmt->bind( $idx++, $item->getName() );
		$stmt->bind( $idx++, $date ); //mtime
		$stmt->bind( $idx++, $context->editor() );

		$stmt->execute()->finish();

		return $item;
	}


	/**
	 * Returns the order cart item specified by its ID.
	 *
	 * @param string $id Unique ID of the order cart item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface Returns order cart item of the given id
	 * @throws \Aimeos\MShop\Order\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'order.cart.id', $id, $ref, $default );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Order\Manager\Cart\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/cart/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/order/manager/cart/delete/ansi
		 */

		/** mshop/order/manager/cart/delete/ansi
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
		 * @since 2022.10
		 * @category Developer
		 * @see mshop/order/manager/cart/insert/ansi
		 * @see mshop/order/manager/cart/update/ansi
		 * @see mshop/order/manager/cart/newid/ansi
		 * @see mshop/order/manager/cart/search/ansi
		 * @see mshop/order/manager/cart/count/ansi
		 */
		$path = 'mshop/order/manager/cart/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/order/manager/cart/submanagers';

		return $this->getResourceTypeBase( 'order/cart', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/order/manager/cart/submanagers
		 * List of manager names that can be instantiated by the order cart manager
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
		 * @since 2022.10
		 * @category Developer
		 */
		$path = 'mshop/order/manager/cart/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for order cart extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/order/manager/cart/name
		 * Class name of the used order cart manager implementation
		 *
		 * Each default order cart manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Order\Manager\Cart\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Order\Manager\Cart\Mycart
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/order/manager/cart/name = Mycart
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCart"!
		 *
		 * @param string Last part of the class name
		 * @since 2022.10
		 * @category Developer
		 */

		/** mshop/order/manager/cart/decorators/excludes
		 * Excludes decorators added by the "common" option from the order cart manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order cart manager.
		 *
		 *  mshop/order/manager/cart/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the order cart manager.
		 *
		 * @param array List of decorator names
		 * @since 2022.10
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/cart/decorators/global
		 * @see mshop/order/manager/cart/decorators/local
		 */

		/** mshop/order/manager/cart/decorators/global
		 * Adds a list of globally available decorators only to the order cart manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order cart
		 * manager.
		 *
		 *  mshop/order/manager/cart/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order
		 * cart manager.
		 *
		 * @param array List of decorator names
		 * @since 2022.10
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/cart/decorators/excludes
		 * @see mshop/order/manager/cart/decorators/local
		 */

		/** mshop/order/manager/cart/decorators/local
		 * Adds a list of local decorators only to the order cart manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Order\Manager\Cart\Decorator\*") around the order
		 * cart manager.
		 *
		 *  mshop/order/manager/cart/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Order\Manager\Cart\Decorator\Decorator2" only to the
		 * order cart manager.
		 *
		 * @param array List of decorator names
		 * @since 2022.10
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/cart/decorators/excludes
		 * @see mshop/order/manager/cart/decorators/global
		 */

		return $this->getSubManagerBase( 'order', 'cart/' . $manager, $name );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Order\Item\Cart\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

			$required = array( 'order.cart' );

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$level = $context->config()->get( 'mshop/order/manager/sitemode', $level );

			/** mshop/order/manager/cart/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/cart/search/ansi
			 */

			/** mshop/order/manager/cart/search/ansi
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
			 * @since 2022.10
			 * @category Developer
			 * @see mshop/order/manager/cart/insert/ansi
			 * @see mshop/order/manager/cart/update/ansi
			 * @see mshop/order/manager/cart/newid/ansi
			 * @see mshop/order/manager/cart/delete/ansi
			 * @see mshop/order/manager/cart/count/ansi
			 */
			$cfgPathSearch = 'mshop/order/manager/cart/search';

			/** mshop/order/manager/cart/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/order/manager/cart/count/ansi
			 */

			/** mshop/order/manager/cart/count/ansi
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
			 * @since 2022.10
			 * @category Developer
			 * @see mshop/order/manager/cart/insert/ansi
			 * @see mshop/order/manager/cart/update/ansi
			 * @see mshop/order/manager/cart/newid/ansi
			 * @see mshop/order/manager/cart/delete/ansi
			 * @see mshop/order/manager/cart/search/ansi
			 */
			$cfgPathCount = 'mshop/order/manager/cart/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $level );

			while( ( $row = $results->fetch() ) !== null )
			{
				if( $item = $this->createItemBase( $row ) ) {
					$items[$row['order.cart.id']] = $item;
				}
			}

		return map( $items );
	}


	/**
	 * Creates a new order cart object.
	 *
	 * @param array $values List of attributes for the order cart object
	 * @return \Aimeos\MShop\Order\Item\Cart\Iface New order cart object
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MShop\Order\Item\Cart\Iface
	{
		return new \Aimeos\MShop\Order\Item\Cart\Standard( $values );
	}
}
