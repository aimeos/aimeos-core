<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Supplier
 */


namespace Aimeos\MShop\Supplier\Manager;


/**
 * Class \Aimeos\MShop\Supplier\Manager\Standard.
 *
 * @package MShop
 * @subpackage Supplier
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\ListRef\Base
	implements \Aimeos\MShop\Supplier\Manager\Iface
{
	private $searchConfig = array(
		'supplier.id' => array(
			'code' => 'supplier.id',
			'internalcode' => 'msup."id"',
			'label' => 'Supplier ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'supplier.siteid' => array(
			'code' => 'supplier.siteid',
			'internalcode' => 'msup."siteid"',
			'label' => 'Supplier site ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'supplier.code' => array(
			'code' => 'supplier.code',
			'internalcode' => 'msup."code"',
			'label' => 'Supplier code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'supplier.label' => array(
			'code' => 'supplier.label',
			'internalcode' => 'msup."label"',
			'label' => 'Supplier label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'supplier.status'=> array(
			'code' => 'supplier.status',
			'internalcode' => 'msup."status"',
			'label' => 'Supplier status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'supplier.ctime'=> array(
			'code'=>'supplier.ctime',
			'internalcode'=>'msup."ctime"',
			'label'=>'Supplier create date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'supplier.mtime'=> array(
			'code'=>'supplier.mtime',
			'internalcode'=>'msup."mtime"',
			'label'=>'Supplier modification date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'supplier.editor'=> array(
			'code'=>'supplier.editor',
			'internalcode'=>'msup."editor"',
			'label'=>'Supplier editor',
			'type'=> 'string',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
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
		$this->setResourceName( 'db-supplier' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/supplier/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'address' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/supplier/manager/standard/delete' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/supplier/manager/submanagers';

		return $this->getResourceTypeBase( 'supplier', $path, array( 'address', 'lists' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/supplier/manager/submanagers
		 * List of manager names that can be instantiated by the supplier manager
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
		$path = 'mshop/supplier/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'address' ), $withsub );
	}


	/**
	 * Instantiates a new supplier item object.
	 *
	 * @return \Aimeos\MShop\Supplier\Item\Iface
	 */
	public function createItem()
	{
		$values = array('supplier.siteid' => $this->getContext()->getLocale()->getSiteId());
		return $this->createItemBase($values);
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/supplier/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/supplier/manager/standard/delete/ansi
		 */

		/** mshop/supplier/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the supplier database.
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
		 * @see mshop/supplier/manager/standard/insert/ansi
		 * @see mshop/supplier/manager/standard/update/ansi
		 * @see mshop/supplier/manager/standard/newid/ansi
		 * @see mshop/supplier/manager/standard/search/ansi
		 * @see mshop/supplier/manager/standard/count/ansi
		 */
		$path = 'mshop/supplier/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
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
		return $this->findItemBase( array( 'supplier.code' => $code ), $ref );
	}


	/**
	 * Returns the supplier item object specificed by its ID.
	 *
	 * @param integer $id Unique supplier ID referencing an existing supplier
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Supplier\Item\Iface Returns the supplier item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'supplier.id', $id, $ref, $default );
	}


	/**
	 * Saves a supplier item object.
	 *
	 * @param \Aimeos\MShop\Supplier\Item\Iface $item Supplier item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Supplier\\Item\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Supplier\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			if( $id === null )
			{
				/** mshop/supplier/manager/standard/insert/mysql
				 * Inserts a new supplier record into the database table
				 *
				 * @see mshop/supplier/manager/standard/insert/ansi
				 */

				/** mshop/supplier/manager/standard/insert/ansi
				 * Inserts a new supplier record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the supplier item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The order of the columns must correspond to the
				 * order in the saveItems() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/supplier/manager/standard/update/ansi
				 * @see mshop/supplier/manager/standard/newid/ansi
				 * @see mshop/supplier/manager/standard/delete/ansi
				 * @see mshop/supplier/manager/standard/search/ansi
				 * @see mshop/supplier/manager/standard/count/ansi
				 */
				$path = 'mshop/supplier/manager/standard/insert';
			}
			else
			{
				/** mshop/supplier/manager/standard/update/mysql
				 * Updates an existing supplier record in the database
				 *
				 * @see mshop/supplier/manager/standard/update/ansi
				 */

				/** mshop/supplier/manager/standard/update/ansi
				 * Updates an existing supplier record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the supplier item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/supplier/manager/standard/insert/ansi
				 * @see mshop/supplier/manager/standard/newid/ansi
				 * @see mshop/supplier/manager/standard/delete/ansi
				 * @see mshop/supplier/manager/standard/search/ansi
				 * @see mshop/supplier/manager/standard/count/ansi
				 */
				$path = 'mshop/supplier/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getCode() );
			$stmt->bind( 3, $item->getLabel() );
			$stmt->bind( 4, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 5, $date ); // mtime
			$stmt->bind( 6, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 7, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 7, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/supplier/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/supplier/manager/standard/newid/ansi
				 */

				/** mshop/supplier/manager/standard/newid/ansi
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
				 *  SELECT currval('seq_msup_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_msup_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/supplier/manager/standard/insert/ansi
				 * @see mshop/supplier/manager/standard/update/ansi
				 * @see mshop/supplier/manager/standard/delete/ansi
				 * @see mshop/supplier/manager/standard/search/ansi
				 * @see mshop/supplier/manager/standard/count/ansi
				 */
				$path = 'mshop/supplier/manager/standard/newid';
				$item->setId( $this->newId( $conn, $path ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of supplier items implementing \Aimeos\MShop\Supplier\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$map = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'supplier' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;

			/** mshop/supplier/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/supplier/manager/standard/search/ansi
			 */

			/** mshop/supplier/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the supplier
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
			 * @see mshop/supplier/manager/standard/insert/ansi
			 * @see mshop/supplier/manager/standard/update/ansi
			 * @see mshop/supplier/manager/standard/newid/ansi
			 * @see mshop/supplier/manager/standard/delete/ansi
			 * @see mshop/supplier/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/supplier/manager/standard/search';

			/** mshop/supplier/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/supplier/manager/standard/count/ansi
			 */

			/** mshop/supplier/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the supplier
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
			 * @see mshop/supplier/manager/standard/insert/ansi
			 * @see mshop/supplier/manager/standard/update/ansi
			 * @see mshop/supplier/manager/standard/newid/ansi
			 * @see mshop/supplier/manager/standard/delete/ansi
			 * @see mshop/supplier/manager/standard/search/ansi
			 */
			$cfgPathCount =  'mshop/supplier/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$map[$row['supplier.id']] = $row;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$addrItems = [];
		if( in_array( 'supplier/address', $ref, true ) ) {
			$addrItems = $this->getAddressItems( array_keys( $map ) );
		}

		return $this->buildItems( $map, $ref, 'supplier', $addrItems );
	}


	/**
	 * Creates a new manager for supplier
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Address\Iface Returns a address manager
	 * @throws \Aimeos\MShop\Supplier\Exception If creating manager failed
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'supplier', $manager, $name );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return \Aimeos\MW\Criteria\Iface
	 */
	public function createSearch($default = false)
	{
		if ($default) {
			return $this->createSearchBase('supplier');
		}

		return parent::createSearch();
	}


	/**
	 * Creates a new supplier item.
	 *
	 * @param array $values List of attributes for supplier item
	 * @param array $listitems Associative list of list item IDs as keys and \Aimeos\MShop\Common\Item\List\Iface as values
	 * @param array $refItems Associative list of item IDs as keys and \Aimeos\MShop\Common\Item\Iface as values
	 * @param array $addresses List of address items of the supplier item
	 * @return \Aimeos\MShop\Supplier\Item\Iface New supplier item
	 */
	protected function createItemBase( array $values = [], array $listitems = [], array $refItems = [], array $addresses = [] )
	{
		return new \Aimeos\MShop\Supplier\Item\Standard( $values, $listitems, $refItems, $addresses );
	}


	/**
	 * Returns the address items for the given supplier IDs
	 *
	 * @param array $prodIds List of supplier IDs
	 * @return array Associative list of supplier IDs / address IDs as keys and items implementing
	 * 	\Aimeos\MShop\Common\Item\Address\Iface as values
	 */
	protected function getAddressItems( array $supplierIds )
	{
		$list = [];
		$manager = $this->getSubManager( 'address' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'supplier.address.parentid', $supplierIds ) );
		$search->setSlice( 0, 0x7fffffff );

		foreach( $manager->searchItems( $search ) as $id => $addrItem ) {
			$list[$addrItem->getParentId()][$id] = $addrItem;
		}

		return $list;
	}
}
