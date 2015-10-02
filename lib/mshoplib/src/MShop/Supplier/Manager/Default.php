<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Supplier
 */


/**
 * Class MShop_Supplier_Manager_Default.
 *
 * @package MShop
 * @subpackage Supplier
 */
class MShop_Supplier_Manager_Default
	extends MShop_Common_Manager_ListRef_Abstract
	implements MShop_Supplier_Manager_Interface
{
	private $_searchConfig = array(
		'supplier.id' => array(
			'code' => 'supplier.id',
			'internalcode' => 'msup."id"',
			'label' => 'Supplier ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'supplier.siteid' => array(
			'code' => 'supplier.siteid',
			'internalcode' => 'msup."siteid"',
			'label' => 'Supplier site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'supplier.code' => array(
			'code' => 'supplier.code',
			'internalcode' => 'msup."code"',
			'label' => 'Supplier code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.label' => array(
			'code' => 'supplier.label',
			'internalcode' => 'msup."label"',
			'label' => 'Supplier label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.status'=> array(
			'code' => 'supplier.status',
			'internalcode' => 'msup."status"',
			'label' => 'Supplier status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'supplier.ctime'=> array(
			'code'=>'supplier.ctime',
			'internalcode'=>'msup."ctime"',
			'label'=>'Supplier create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.mtime'=> array(
			'code'=>'supplier.mtime',
			'internalcode'=>'msup."mtime"',
			'label'=>'Supplier modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'supplier.editor'=> array(
			'code'=>'supplier.editor',
			'internalcode'=>'msup."editor"',
			'label'=>'Supplier editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-supplier' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/supplier/manager/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'address' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/supplier/manager/default/item/delete' );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/supplier/manager/submanagers
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
		$path = 'classes/supplier/manager/submanagers';

		return $this->getSearchAttributesBase( $this->_searchConfig, $path, array( 'address' ), $withsub );
	}


	/**
	 * Instantiates a new supplier item object.
	 *
	 * @return MShop_Supplier_Item_Interface
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/supplier/manager/default/item/delete
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
		 * @see mshop/supplier/manager/default/item/insert
		 * @see mshop/supplier/manager/default/item/update
		 * @see mshop/supplier/manager/default/item/newid
		 * @see mshop/supplier/manager/default/item/search
		 * @see mshop/supplier/manager/default/item/count
		 */
		$path = 'mshop/supplier/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the supplier item object specificed by its ID.
	 *
	 * @param integer $id Unique supplier ID referencing an existing supplier
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Supplier_Item_Interface Returns the supplier item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->getItemBase( 'supplier.id', $id, $ref );
	}


	/**
	 * Saves a supplier item object.
	 *
	 * @param MShop_Supplier_Item_Interface $item Supplier item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Supplier_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Supplier_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			if( $id === null )
			{
				/** mshop/supplier/manager/default/item/insert
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
				 * @see mshop/supplier/manager/default/item/update
				 * @see mshop/supplier/manager/default/item/newid
				 * @see mshop/supplier/manager/default/item/delete
				 * @see mshop/supplier/manager/default/item/search
				 * @see mshop/supplier/manager/default/item/count
				 */
				$path = 'mshop/supplier/manager/default/item/insert';
			}
			else
			{
				/** mshop/supplier/manager/default/item/update
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
				 * @see mshop/supplier/manager/default/item/insert
				 * @see mshop/supplier/manager/default/item/newid
				 * @see mshop/supplier/manager/default/item/delete
				 * @see mshop/supplier/manager/default/item/search
				 * @see mshop/supplier/manager/default/item/count
				 */
				$path = 'mshop/supplier/manager/default/item/update';
			}

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getCode() );
			$stmt->bind( 3, $item->getLabel() );
			$stmt->bind( 4, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, $date ); // mtime
			$stmt->bind( 6, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 7, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 7, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/supplier/manager/default/item/newid
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
				 * @see mshop/supplier/manager/default/item/insert
				 * @see mshop/supplier/manager/default/item/update
				 * @see mshop/supplier/manager/default/item/delete
				 * @see mshop/supplier/manager/default/item/search
				 * @see mshop/supplier/manager/default/item/count
				 */
				$path = 'mshop/supplier/manager/default/item/newid';
				$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria object
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Supplier_Item_Interface
	 * @throws MShop_Supplier_Exception If creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$map = array();
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'supplier' );
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;

			/** mshop/supplier/manager/default/item/search
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
			 * @see mshop/supplier/manager/default/item/insert
			 * @see mshop/supplier/manager/default/item/update
			 * @see mshop/supplier/manager/default/item/newid
			 * @see mshop/supplier/manager/default/item/delete
			 * @see mshop/supplier/manager/default/item/count
			 */
			$cfgPathSearch = 'mshop/supplier/manager/default/item/search';

			/** mshop/supplier/manager/default/item/count
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
			 * @see mshop/supplier/manager/default/item/insert
			 * @see mshop/supplier/manager/default/item/update
			 * @see mshop/supplier/manager/default/item/newid
			 * @see mshop/supplier/manager/default/item/delete
			 * @see mshop/supplier/manager/default/item/search
			 */
			$cfgPathCount =  'mshop/supplier/manager/default/item/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$map[$row['id']] = $row;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $this->_buildItems( $map, $ref, 'supplier' );
	}


	/**
	 * Creates a new manager for supplier
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Address_Interface Returns a address manager
	 * @throws MShop_Supplier_Exception If creating manager failed
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'supplier', $manager, $name );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
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
	 * @return MShop_Supplier_Item_Interface New supplier item
	 */
	protected function _createItem( array $values = array(), array $listitems = array(), array $refItems = array() )
	{
		return new MShop_Supplier_Item_Default( $values, $listitems, $refItems );
	}
}
