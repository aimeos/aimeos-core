<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Attribute
 */


/**
 * Default attribute manager for creating and handling attributes.
 * @package MShop
 * @subpackage Attribute
 */
class MShop_Attribute_Manager_Standard
	extends MShop_Common_Manager_ListRef_Base
	implements MShop_Attribute_Manager_Iface
{
	private $searchConfig = array(
		'attribute.id'=> array(
			'code'=>'attribute.id',
			'internalcode'=>'matt."id"',
			'label'=>'Attribute ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Base::PARAM_INT,
		),
		'attribute.siteid'=> array(
			'code'=>'attribute.siteid',
			'internalcode'=>'matt."siteid"',
			'label'=>'Attribute site',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Base::PARAM_INT,
			'public' => false,
		),
		'attribute.typeid'=> array(
			'code'=>'attribute.typeid',
			'internalcode'=>'matt."typeid"',
			'label'=>'Attribute type',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Base::PARAM_INT,
			'public' => false,
		),
		'attribute.domain'=> array(
			'code'=>'attribute.domain',
			'internalcode'=>'matt."domain"',
			'label'=>'Attribute domain',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Base::PARAM_STR,
		),
		'attribute.code'=> array(
			'code'=>'attribute.code',
			'internalcode'=>'matt."code"',
			'label'=>'Attribute code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Base::PARAM_STR,
		),
		'attribute.position'=> array(
			'code'=>'attribute.position',
			'internalcode'=>'matt."pos"',
			'label'=>'Attribute position',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Base::PARAM_INT,
		),
		'attribute.label'=> array(
			'code'=>'attribute.label',
			'internalcode'=>'matt."label"',
			'label'=>'Attribute label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Base::PARAM_STR,
		),
		'attribute.status'=> array(
			'code'=>'attribute.status',
			'internalcode'=>'matt."status"',
			'label'=>'Attribute status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Base::PARAM_INT,
		),
		'attribute.ctime'=> array(
			'code'=>'attribute.ctime',
			'internalcode'=>'matt."ctime"',
			'label'=>'Attribute create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Base::PARAM_STR,
		),
		'attribute.mtime'=> array(
			'code'=>'attribute.mtime',
			'internalcode'=>'matt."mtime"',
			'label'=>'Attribute modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Base::PARAM_STR,
		),
		'attribute.editor'=> array(
			'code'=>'attribute.editor',
			'internalcode'=>'matt."editor"',
			'label'=>'Attribute editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Base::PARAM_STR,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Iface $context Context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-attribute' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/attribute/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'type', 'list' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/attribute/manager/default/item/delete' );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/attribute/manager/submanagers
		 * List of manager names that can be instantiated by the attribute manager
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
		$path = 'classes/attribute/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'type', 'list' ), $withsub );
	}


	/**
	 * Creates a new empty attribute item instance.
	 *
	 * @return MShop_Attribute_Item_Iface Creates a blank Attribute item
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Returns the attributes item specified by its ID.
	 *
	 * @param integer $id Unique ID of the attribute item in the storage
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Attribute_Item_Iface Returns the attribute item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->getItemBase( 'attribute.id', $id, $ref );
	}


	/**
	 * Saves an attribute item to the storage.
	 *
	 * @param MShop_Common_Item_Iface $item Attribute item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @throws MShop_Attribute_Exception If Attribute couldn't be saved
	 */
	public function saveItem( MShop_Common_Item_Iface $item, $fetch = true )
	{
		$iface = 'MShop_Attribute_Item_Iface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Attribute_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( $item->isModified() === false ) { return; }

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
				/** mshop/attribute/manager/default/item/insert
				 * Inserts a new attribute record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the attribute item to the statement before they are
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
				 * @see mshop/attribute/manager/default/item/update
				 * @see mshop/attribute/manager/default/item/newid
				 * @see mshop/attribute/manager/default/item/delete
				 * @see mshop/attribute/manager/default/item/search
				 * @see mshop/attribute/manager/default/item/count
				 */
				$path = 'mshop/attribute/manager/default/item/insert';
			}
			else
			{
				/** mshop/attribute/manager/default/item/update
				 * Updates an existing attribute record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the attribute item to the statement before they are
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
				 * @see mshop/attribute/manager/default/item/insert
				 * @see mshop/attribute/manager/default/item/newid
				 * @see mshop/attribute/manager/default/item/delete
				 * @see mshop/attribute/manager/default/item/search
				 * @see mshop/attribute/manager/default/item/count
				 */
				$path = 'mshop/attribute/manager/default/item/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId() );
			$stmt->bind( 2, $item->getTypeId() );
			$stmt->bind( 3, $item->getDomain() );
			$stmt->bind( 4, $item->getCode() );
			$stmt->bind( 5, $item->getStatus(), MW_DB_Statement_Base::PARAM_INT );
			$stmt->bind( 6, $item->getPosition(), MW_DB_Statement_Base::PARAM_INT );
			$stmt->bind( 7, $item->getLabel() );
			$stmt->bind( 8, $date );
			$stmt->bind( 9, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 10, $id, MW_DB_Statement_Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 10, $date );
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/attribute/manager/default/item/newid
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
				 *  SELECT currval('seq_matt_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_matt_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/attribute/manager/default/item/insert
				 * @see mshop/attribute/manager/default/item/update
				 * @see mshop/attribute/manager/default/item/delete
				 * @see mshop/attribute/manager/default/item/search
				 * @see mshop/attribute/manager/default/item/count
				 */
				$path = 'mshop/attribute/manager/default/item/newid';
				$item->setId( $this->newId( $conn, $context->getConfig()->get( $path, $path ) ) );
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
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/attribute/manager/default/item/delete
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the attribute database.
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
		 * @see mshop/attribute/manager/default/item/insert
		 * @see mshop/attribute/manager/default/item/update
		 * @see mshop/attribute/manager/default/item/newid
		 * @see mshop/attribute/manager/default/item/search
		 * @see mshop/attribute/manager/default/item/count
		 */
		$path = 'mshop/attribute/manager/default/item/delete';
		$this->deleteItemsBase( $ids, $this->getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Searches for attribute items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Iface $search Search object containing the conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of attribute items implementing MShop_Attribute_Item_Iface
	 *
	 * @throws MW_DB_Exception On failures with the db object
	 * @throws MShop_Common_Exception On failures with the MW_Common_Criteria_ object
	 * @throws MShop_Attribute_Exception On failures with the Attribute items
	 */
	public function searchItems( MW_Common_Criteria_Iface $search, array $ref = array(), &$total = null )
	{
		$map = $typeIds = array();
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'attribute' );
			$level = MShop_Locale_Manager_Base::SITE_ALL;

			/** mshop/attribute/manager/default/item/search
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the attribute
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
			 * @see mshop/attribute/manager/default/item/insert
			 * @see mshop/attribute/manager/default/item/update
			 * @see mshop/attribute/manager/default/item/newid
			 * @see mshop/attribute/manager/default/item/delete
			 * @see mshop/attribute/manager/default/item/count
			 */
			$cfgPathSearch = 'mshop/attribute/manager/default/item/search';

			/** mshop/attribute/manager/default/item/count
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the attribute
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
			 * @see mshop/attribute/manager/default/item/insert
			 * @see mshop/attribute/manager/default/item/update
			 * @see mshop/attribute/manager/default/item/newid
			 * @see mshop/attribute/manager/default/item/delete
			 * @see mshop/attribute/manager/default/item/search
			 */
			$cfgPathCount = 'mshop/attribute/manager/default/item/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[$row['id']] = $row;
				$typeIds[$row['typeid']] = null;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		if( !empty( $typeIds ) )
		{
			$typeManager = $this->getSubManager( 'type' );
			$typeSearch = $typeManager->createSearch();
			$typeSearch->setConditions( $typeSearch->compare( '==', 'attribute.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[$row['typeid']] ) ) {
					$map[$id]['type'] = $typeItems[$row['typeid']]->getCode();
				}
			}
		}

		return $this->buildItems( $map, $ref, 'attribute' );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Iface
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->createSearchBase( 'attribute' );
		}

		return parent::createSearch();
	}


	/**
	 * Returns a new manager for attribute extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Iface Manager for different extensions, e.g Type, List's etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'attribute', $manager, $name );
	}


	/**
	 * Creates a new attribute item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listItems List of items implementing MShop_Common_Item_List_Iface
	 * @param array $refItems List of items implementing MShop_Text_Item_Iface
	 * @return MShop_Attribute_Item_Iface New product item
	 */
	protected function createItemBase( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		return new MShop_Attribute_Item_Standard( $values, $listItems, $refItems );
	}
}
