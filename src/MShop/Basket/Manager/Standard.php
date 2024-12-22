<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2024
 * @package MShop
 * @subpackage Basket
 */

namespace Aimeos\MShop\Basket\Manager;


/**
 * Default implementation for basket manager.
 *
 * @package MShop
 * @subpackage Basket
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Basket\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private array $searchConfig = array(
		'basket.id' => array(
			'code' => 'basket.id',
			'internalcode' => 'mbas."id"',
			'label' => 'Basket ID',
			'public' => false,
		),
		'basket.siteid' => array(
			'code' => 'basket.siteid',
			'internalcode' => 'mbas."siteid"',
			'label' => 'Basket site ID',
			'public' => false,
		),
		'basket.customerid' => array(
			'code' => 'basket.customerid',
			'internalcode' => 'mbas."customerid"',
			'label' => 'Basket customer ID',
			'public' => false,
		),
		'basket.name' => array(
			'code' => 'basket.name',
			'internalcode' => 'mbas."name"',
			'label' => 'Basket name',
		),
		'basket.content' => array(
			'code' => 'basket.content',
			'internalcode' => 'mbas."content"',
			'label' => 'Basket content',
		),
		'basket.ctime' => array(
			'code' => 'basket.ctime',
			'internalcode' => 'mbas."ctime"',
			'label' => 'Basket create date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'basket.mtime' => array(
			'code' => 'basket.mtime',
			'internalcode' => 'mbas."mtime"',
			'label' => 'Basket modify date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'basket.editor' => array(
			'code' => 'basket.editor',
			'internalcode' => 'mbas."editor"',
			'label' => 'Basket editor',
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

		/** mshop/basket/manager/resource
		 * Name of the database connection resource to use
		 *
		 * You can configure a different database connection for each data domain
		 * and if no such connection name exists, the "db" connection will be used.
		 * It's also possible to use the same database connection for different
		 * data domains by configuring the same connection name using this setting.
		 *
		 * @param string Database connection name
		 * @since 2023.04
		 */
		$this->setResourceName( $context->config()->get( 'mshop/basket/manager/resource', 'db-basket' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Order\Manager\Basket\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/basket/manager/submanagers';
		foreach( $this->context()->config()->get( $path, [] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/basket/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Basket\Item\Iface New basket item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['basket.siteid'] = $values['basket.siteid'] ?? $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Creates a search critera object
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface New search criteria object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		$filter = parent::filter( $default, $site );

		if( $default !== false ) {
			$filter->add( 'basket.customerid', '==', $this->context()->user() );
		}

		return $filter;
	}


	/**
	 * Adds or updates an basket object.
	 *
	 * @param \Aimeos\MShop\Basket\Item\Iface $item Order basket object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Basket\Item\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Basket\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Basket\Item\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->context();
		$date = $context->datetime();
		$conn = $context->db( $this->getResourceName() );
		$columns = $this->object()->getSaveAttributes();

		/** mshop/basket/manager/insert/mysql
		 * Inserts a new basket record into the database table or updates an existing one
		 *
		 * The SQL statement must be a string suitable for being used as
		 * prepared statement. It must include question marks for binding
		 * the values from the basket item to the statement before they are
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
		 * @see mshop/basket/manager/newid/ansi
		 * @see mshop/basket/manager/delete/ansi
		 * @see mshop/basket/manager/search/ansi
		 * @see mshop/basket/manager/count/ansi
		 */
		$path = 'mshop/basket/manager/insert';

		$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		$serialized = base64_encode( serialize( clone $item->getItem() ) );
		$idx = 1;

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		// insert
		$stmt->bind( $idx++, $item->getCustomerId() );
		$stmt->bind( $idx++, $serialized );
		$stmt->bind( $idx++, $item->getName() );
		$stmt->bind( $idx++, $date ); //mtime
		$stmt->bind( $idx++, $context->editor() );
		$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
		$stmt->bind( $idx++, $date ); //ctime
		$stmt->bind( $idx++, $item->getId() );
		// update
		$stmt->bind( $idx++, $item->getCustomerId() );
		$stmt->bind( $idx++, $serialized );
		$stmt->bind( $idx++, $item->getName() );
		$stmt->bind( $idx++, $date ); //mtime
		$stmt->bind( $idx++, $context->editor() );

		$stmt->execute()->finish();

		return $item;
	}


	/**
	 * Returns the basket item specified by its ID.
	 *
	 * @param string $id Unique ID of the basket item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Basket\Item\Iface Returns basket item of the given id
	 * @throws \Aimeos\MShop\Order\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'basket.id', $id, $ref, $default );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Order\Manager\Basket\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/basket/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/basket/manager/delete/ansi
		 */

		/** mshop/basket/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the basket database.
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
		 * @see mshop/basket/manager/insert/ansi
		 * @see mshop/basket/manager/update/ansi
		 * @see mshop/basket/manager/newid/ansi
		 * @see mshop/basket/manager/search/ansi
		 * @see mshop/basket/manager/count/ansi
		 */
		$path = 'mshop/basket/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/basket/manager/submanagers
		 * List of manager names that can be instantiated by the basket manager
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
		 */
		$path = 'mshop/basket/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for basket extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	public function getSubManager( string $manager, ?string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/basket/manager/name
		 * Class name of the used basket manager implementation
		 *
		 * Each default basket manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Order\Manager\Basket\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Order\Manager\Basket\Mybasket
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/basket/manager/name = Mybasket
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyBasket"!
		 *
		 * @param string Last part of the class name
		 * @since 2022.10
		 */

		/** mshop/basket/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the basket manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the basket manager.
		 *
		 *  mshop/basket/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the basket manager.
		 *
		 * @param array List of decorator names
		 * @since 2022.10
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/basket/manager/decorators/global
		 * @see mshop/basket/manager/decorators/local
		 */

		/** mshop/basket/manager/decorators/global
		 * Adds a list of globally available decorators only to the basket manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the basket
		 * manager.
		 *
		 *  mshop/basket/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the
		 * basket manager.
		 *
		 * @param array List of decorator names
		 * @since 2022.10
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/basket/manager/decorators/excludes
		 * @see mshop/basket/manager/decorators/local
		 */

		/** mshop/basket/manager/decorators/local
		 * Adds a list of local decorators only to the basket manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Order\Manager\Basket\Decorator\*") around the
		 * basket manager.
		 *
		 *  mshop/basket/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Order\Manager\Basket\Decorator\Decorator2" only to the
		 * basket manager.
		 *
		 * @param array List of decorator names
		 * @since 2022.10
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/basket/manager/decorators/excludes
		 * @see mshop/basket/manager/decorators/global
		 */

		return $this->getSubManagerBase( 'basket', $manager, $name );
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Basket\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], ?int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$required = array( 'basket' );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;
		$level = $context->config()->get( 'mshop/basket/manager/sitemode', $level );

		/** mshop/basket/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/basket/manager/search/ansi
		 */

		/** mshop/basket/manager/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the basket
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
		 * replaces the ":order" placeholder. Columns of
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
		 * @see mshop/basket/manager/insert/ansi
		 * @see mshop/basket/manager/update/ansi
		 * @see mshop/basket/manager/newid/ansi
		 * @see mshop/basket/manager/delete/ansi
		 * @see mshop/basket/manager/count/ansi
		 */
		$cfgPathSearch = 'mshop/basket/manager/search';

		/** mshop/basket/manager/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/basket/manager/count/ansi
		 */

		/** mshop/basket/manager/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the basket
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
		 * @see mshop/basket/manager/insert/ansi
		 * @see mshop/basket/manager/update/ansi
		 * @see mshop/basket/manager/newid/ansi
		 * @see mshop/basket/manager/delete/ansi
		 * @see mshop/basket/manager/search/ansi
		 */
		$cfgPathCount = 'mshop/basket/manager/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		while( $row = $results->fetch() )
		{
			$basket = unserialize( base64_decode( $row['basket.content'] ) );

			if( !( $basket instanceof \Aimeos\MShop\Order\Item\Iface ) )
			{
				$msg = sprintf( 'Invalid serialized basket. "%1$s" returned "%2$s".', __METHOD__, $row['basket.content'] );
				$context->logger()->warning( $msg, 'core/basket' );
			}

			if( $item = $this->createItemBase( $row, $basket ?: null ) ) {
				$items[$row['basket.id']] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Creates a new basket object.
	 *
	 * @param array $values List of attributes for the basket object
	 * @param \Aimeos\MShop\Order\Item\Iface|null $basket Basket object
	 * @return \Aimeos\MShop\Basket\Item\Iface New basket object
	 */
	protected function createItemBase( array $values = [], ?\Aimeos\MShop\Order\Item\Iface $basket = null ) : \Aimeos\MShop\Basket\Item\Iface
	{
		return new \Aimeos\MShop\Basket\Item\Standard( $values, $basket );
	}
}
