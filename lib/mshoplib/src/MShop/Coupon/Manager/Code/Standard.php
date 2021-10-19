<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Coupon
 */


namespace Aimeos\MShop\Coupon\Manager\Code;


/**
 * Default coupon manager interface for creating and handling coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Coupon\Manager\Code\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'coupon.code.id' => array(
			'code' => 'coupon.code.id',
			'internalcode' => 'mcouco."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_coupon_code" AS mcouco ON (mcou."id"=mcouco."parentid")' ),
			'label' => 'Code ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'coupon.code.siteid' => array(
			'code' => 'coupon.code.siteid',
			'internalcode' => 'mcouco."siteid"',
			'label' => 'Code site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'coupon.code.parentid' => array(
			'code' => 'coupon.code.parentid',
			'internalcode' => 'mcouco."parentid"',
			'label' => 'Coupon ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'coupon.code.code' => array(
			'code' => 'coupon.code.code',
			'internalcode' => 'mcouco."code"',
			'label' => 'Code value',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.count' => array(
			'code' => 'coupon.code.count',
			'internalcode' => 'mcouco."count"',
			'label' => 'Code quantity',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.datestart' => array(
			'code' => 'coupon.code.datestart',
			'internalcode' => 'mcouco."start"',
			'label' => 'Code start date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.dateend' => array(
			'code' => 'coupon.code.dateend',
			'internalcode' => 'mcouco."end"',
			'label' => 'Code end date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.ref' => array(
			'code' => 'coupon.code.ref',
			'internalcode' => 'mcouco."ref"',
			'label' => 'Code reference',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'coupon.code.ctime' => array(
			'code' => 'coupon.code.ctime',
			'internalcode' => 'mcouco."ctime"',
			'label' => 'Code create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'coupon.code.mtime' => array(
			'code' => 'coupon.code.mtime',
			'internalcode' => 'mcouco."mtime"',
			'label' => 'Code modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'coupon.code.editor' => array(
			'code' => 'coupon.code.editor',
			'internalcode' => 'mcouco."editor"',
			'label' => 'Code editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
	);

	private $date;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-coupon' );

		$this->date = $context->getDateTime();
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Coupon\Manager\Code\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/coupon/manager/code/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/coupon/manager/code/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface New coupon code item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['coupon.code.siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface List manager
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/coupon/manager/code/name
		 * Class name of the used coupon code manager implementation
		 *
		 * Each default coupon code manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Coupon\Manager\Address\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Coupon\Manager\Address\Mycode
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/coupon/manager/code/name = Mycode
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

		/** mshop/coupon/manager/code/decorators/excludes
		 * Excludes decorators added by the "common" option from the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the coupon code manager.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/global
		 * @see mshop/coupon/manager/code/decorators/local
		 */

		/** mshop/coupon/manager/code/decorators/global
		 * Adds a list of globally available decorators only to the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the coupon
		 * code manager.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/excludes
		 * @see mshop/coupon/manager/code/decorators/local
		 */

		/** mshop/coupon/manager/code/decorators/local
		 * Adds a list of local decorators only to the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Coupon\Manager\Code\Decorator\*") around the coupon code
		 * manager.
		 *
		 *  mshop/coupon/manager/code/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Coupon\Manager\Code\Decorator\Decorator2" only to the
		 * coupon code manager.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/excludes
		 * @see mshop/coupon/manager/code/decorators/global
		 */

		return $this->getSubManagerBase( 'coupon', 'code/' . $manager, $name );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/coupon/manager/code/submanagers';
		return $this->getResourceTypeBase( 'coupon/code', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/coupon/manager/code/submanagers
		 * List of manager names that can be instantiated by the coupon code manager
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
		$path = 'mshop/coupon/manager/code/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function find( string $code, array $ref = [], string $domain = null, string $type = null,
		?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->findBase( array( 'coupon.code.code' => $code ), $ref, $default );
	}


	/**
	 * Returns the coupon code object specified by its ID.
	 *
	 * @param string $id Unique ID of the coupon code in the storage
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Coupon code object
	 * @throws \Aimeos\MShop\Coupon\Exception If coupon couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'coupon.code.id', $id, $ref, $default );
	}


	/**
	 * Saves a modified code object to the storage.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Code\Iface $item Coupon code object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Coupon\Item\Code\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Coupon\Item\Code\Iface
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
				/** mshop/coupon/manager/code/insert/mysql
				 * Inserts a new coupon code record into the database table
				 *
				 * @see mshop/coupon/manager/code/insert/ansi
				 */

				/** mshop/coupon/manager/code/insert/ansi
				 * Inserts a new coupon code record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the coupon item to the statement before they are
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
				 * @see mshop/coupon/manager/code/update/ansi
				 * @see mshop/coupon/manager/code/newid/ansi
				 * @see mshop/coupon/manager/code/delete/ansi
				 * @see mshop/coupon/manager/code/search/ansi
				 * @see mshop/coupon/manager/code/count/ansi
				 * @see mshop/coupon/manager/code/counter/ansi
				 */
				$path = 'mshop/coupon/manager/code/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/coupon/manager/code/update/mysql
				 * Updates an existing coupon code record in the database
				 *
				 * @see mshop/coupon/manager/code/update/ansi
				 */

				/** mshop/coupon/manager/code/update/ansi
				 * Updates an existing coupon code record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the coupon item to the statement before they are
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
				 * @see mshop/coupon/manager/code/insert/ansi
				 * @see mshop/coupon/manager/code/newid/ansi
				 * @see mshop/coupon/manager/code/delete/ansi
				 * @see mshop/coupon/manager/code/search/ansi
				 * @see mshop/coupon/manager/code/count/ansi
				 * @see mshop/coupon/manager/code/counter/ansi
				 */
				$path = 'mshop/coupon/manager/code/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getCode() );
			$stmt->bind( $idx++, $item->getDateStart() );
			$stmt->bind( $idx++, $item->getDateEnd() );
			$stmt->bind( $idx++, $item->getCount(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getRef() );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $context->getLocale()->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( $idx, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/coupon/manager/code/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/coupon/manager/code/newid/ansi
				 */

				/** mshop/coupon/manager/code/newid/ansi
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
				 *  SELECT currval('seq_mcouco_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mcouco_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/coupon/manager/code/insert/ansi
				 * @see mshop/coupon/manager/code/update/ansi
				 * @see mshop/coupon/manager/code/delete/ansi
				 * @see mshop/coupon/manager/code/search/ansi
				 * @see mshop/coupon/manager/code/count/ansi
				 * @see mshop/coupon/manager/code/counter/ansi
				 */
				$path = 'mshop/coupon/manager/code/newid';
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
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Coupon\Manager\Code\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/coupon/manager/code/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/coupon/manager/code/delete/ansi
		 */

		/** mshop/coupon/manager/code/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the coupon database.
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
		 * @see mshop/coupon/manager/code/insert/ansi
		 * @see mshop/coupon/manager/code/update/ansi
		 * @see mshop/coupon/manager/code/newid/ansi
		 * @see mshop/coupon/manager/code/search/ansi
		 * @see mshop/coupon/manager/code/count/ansi
		 * @see mshop/coupon/manager/code/counter/ansi
		 */
		$path = 'mshop/coupon/manager/code/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Searchs for coupon items based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Coupon\Item\Code\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );
		$items = [];

		try
		{
			$required = array( 'coupon.code' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;

			/** mshop/coupon/manager/code/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/coupon/manager/code/search/ansi
			 */

			/** mshop/coupon/manager/code/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the coupon
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
			 * @see mshop/coupon/manager/code/insert/ansi
			 * @see mshop/coupon/manager/code/update/ansi
			 * @see mshop/coupon/manager/code/newid/ansi
			 * @see mshop/coupon/manager/code/delete/ansi
			 * @see mshop/coupon/manager/code/count/ansi
			 * @see mshop/coupon/manager/code/counter/ansi
			 */
			$cfgPathSearch = 'mshop/coupon/manager/code/search';

			/** mshop/coupon/manager/code/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/coupon/manager/code/count/ansi
			 */

			/** mshop/coupon/manager/code/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the coupon
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
			 * @see mshop/coupon/manager/code/insert/ansi
			 * @see mshop/coupon/manager/code/update/ansi
			 * @see mshop/coupon/manager/code/newid/ansi
			 * @see mshop/coupon/manager/code/delete/ansi
			 * @see mshop/coupon/manager/code/search/ansi
			 * @see mshop/coupon/manager/code/counter/ansi
			 */
			$cfgPathCount = 'mshop/coupon/manager/code/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== null )
				{
					if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
						$items[$row['coupon.code.id']] = $item;
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
	 * Decreases the counter of the coupon code.
	 *
	 * @param string $code Unique code of a coupon
	 * @param int $amount Amount the coupon count should be decreased
	 * @return \Aimeos\MShop\Coupon\Manager\Code\Iface Manager object for chaining method calls
	 */
	public function decrease( string $code, int $amount ) : \Aimeos\MShop\Coupon\Manager\Code\Iface
	{
		return $this->increase( $code, -$amount );
	}



	/**
	 * Increases the counter of the coupon code.
	 *
	 * @param string $code Unique code of a coupon
	 * @param int $amount Amount the coupon count should be increased
	 * @return \Aimeos\MShop\Coupon\Manager\Code\Iface Manager object for chaining method calls
	 */
	public function increase( string $code, int $amount ) : \Aimeos\MShop\Coupon\Manager\Code\Iface
	{
		$context = $this->getContext();
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;

		$search = $this->getObject()->filter();
		$search->setConditions( $search->compare( '==', 'coupon.code.siteid', $context->getLocale()->getSites( $level ) ) );

		$types = array( 'coupon.code.siteid' => $this->searchConfig['coupon.code.siteid']['internaltype'] );
		$translations = array( 'coupon.code.siteid' => 'siteid' );
		$conditions = $search->getConditionSource( $types, $translations );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/coupon/manager/code/counter/mysql
			 * Increases or decreases the counter of the coupon code record matched by the given code
			 *
			 * @see mshop/coupon/manager/code/counter/ansi
			 */

			/** mshop/coupon/manager/code/counter/ansi
			 * Increases or decreases the counter of the coupon code record matched by the given code
			 *
			 * A counter is associated to each coupon code and it's decreased
			 * each time by one if a code used in an paid order was redeemed
			 * successfully. Shop owners can also use the coupon code counter to
			 * use the same code more often by setting it to an arbitrary value.
			 * In this case, the code can be redeemed until the counter reaches
			 * zero.
			 *
			 * The coupon codes must be from one of the sites that are configured
			 * via the context item. If the current site is part of a tree of
			 * sites, the statement can increase or decrease codes from the
			 * current site and all parent sites if the code is inherited by one
			 * of the parent sites.
			 *
			 * Each time the code is updated, the modify date/time is set to
			 * the current timestamp and the editor field is updated.
			 *
			 * @param string SQL statement for increasing/decreasing the coupon code count
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/coupon/manager/code/insert/ansi
			 * @see mshop/coupon/manager/code/update/ansi
			 * @see mshop/coupon/manager/code/newid/ansi
			 * @see mshop/coupon/manager/code/delete/ansi
			 * @see mshop/coupon/manager/code/search/ansi
			 * @see mshop/coupon/manager/code/count/ansi
			 */
			$path = 'mshop/coupon/manager/code/counter';
			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->getSqlConfig( $path ) ) );

			$stmt->bind( 1, $amount, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); // mtime
			$stmt->bind( 3, $context->getEditor() );
			$stmt->bind( 4, $code );

			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this;
	}


	/**
	 * Creates a new code instance
	 *
	 * @param array $values Associative list of item key/value pairs
	 * @return \Aimeos\MShop\Coupon\Item\Code\Iface Emtpy coupon code object
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MShop\Coupon\Item\Code\Iface
	{
		$values['.date'] = $this->date;

		return new \Aimeos\MShop\Coupon\Item\Code\Standard( $values );
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
		if( $default !== false )
		{
			$object = $this->filterBase( 'coupon', $default );

			$expr = [
				$object->or( [
					$object->compare( '==', 'coupon.code.count', null ),
					$object->compare( '>', 'coupon.code.count', 0 ),
				] ),
				$object->or( [
					$object->compare( '==', 'coupon.code.datestart', null ),
					$object->compare( '<=', 'coupon.code.datestart', $this->date ),
				] ),
				$object->or( [
					$object->compare( '==', 'coupon.code.dateend', null ),
					$object->compare( '>=', 'coupon.code.dateend', $this->date ),
				] ),
			];

			$object->setConditions( $object->and( $expr ) );

			return $object;
		}

		return parent::filter();
	}
}
