<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Text
 */


namespace Aimeos\MShop\Text\Manager;


/**
 * Default text manager implementation
 *
 * @package MShop
 * @subpackage Text
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\ListRef\Base
	implements \Aimeos\MShop\Text\Manager\Iface
{
	private $searchConfig = array(
		'text.id'=> array(
			'code'=>'text.id',
			'internalcode'=>'mtex."id"',
			'label'=>'Text ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'text.siteid'=> array(
			'code'=>'text.siteid',
			'internalcode'=>'mtex."siteid"',
			'label'=>'Text site ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'text.languageid' => array(
			'code'=>'text.languageid',
			'internalcode'=>'mtex."langid"',
			'label'=>'Text language code',
			'type'=> 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'text.typeid' => array(
			'code'=>'text.typeid',
			'internalcode'=>'mtex."typeid"',
			'label'=>'Text type ID',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'text.label' => array(
			'code'=>'text.label',
			'internalcode'=>'mtex."label"',
			'label'=>'Text label',
			'type'=> 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'text.domain' => array(
			'code'=>'text.domain',
			'internalcode'=>'mtex."domain"',
			'label'=>'Text domain',
			'type'=> 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'text.content' => array(
			'code'=>'text.content',
			'internalcode'=>'mtex."content"',
			'label'=>'Text content',
			'type'=> 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'text.status' => array(
			'code'=>'text.status',
			'internalcode'=>'mtex."status"',
			'label'=>'Text status',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'text.ctime'=> array(
			'code'=>'text.ctime',
			'internalcode'=>'mtex."ctime"',
			'label'=>'Text create date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'text.mtime'=> array(
			'code'=>'text.mtime',
			'internalcode'=>'mtex."mtime"',
			'label'=>'Text modification date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'text.editor'=> array(
			'code'=>'text.editor',
			'internalcode'=>'mtex."editor"',
			'label'=>'Text editor',
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
		$this->setResourceName( 'db-text' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/text/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'type', 'lists' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/text/manager/standard/delete' );
	}


	/**
	 * Creates new text item object.
	 *
	 * @return \Aimeos\MShop\Text\Item\Iface New text item object
	 */
	public function createItem()
	{
		$values = array( 'text.siteid' => $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Updates or adds a text item object.
	 * This method doesn't update the type string that belongs to the type ID
	 *
	 * @param \Aimeos\MShop\Text\Item\Iface $item Text item which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Text\\Item\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Text\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
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
				/** mshop/text/manager/standard/insert/mysql
				 * Inserts a new text record into the database table
				 *
				 * @see mshop/text/manager/standard/insert/ansi
				 */

				/** mshop/text/manager/standard/insert/ansi
				 * Inserts a new text record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the text item to the statement before they are
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
				 * @see mshop/text/manager/standard/update/ansi
				 * @see mshop/text/manager/standard/newid/ansi
				 * @see mshop/text/manager/standard/delete/ansi
				 * @see mshop/text/manager/standard/search/ansi
				 * @see mshop/text/manager/standard/count/ansi
				 */
				$path = 'mshop/text/manager/standard/insert';
			}
			else
			{
				/** mshop/text/manager/standard/update/mysql
				 * Updates an existing text record in the database
				 *
				 * @see mshop/text/manager/standard/update/ansi
				 */

				/** mshop/text/manager/standard/update/ansi
				 * Updates an existing text record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the text item to the statement before they are
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
				 * @see mshop/text/manager/standard/insert/ansi
				 * @see mshop/text/manager/standard/newid/ansi
				 * @see mshop/text/manager/standard/delete/ansi
				 * @see mshop/text/manager/standard/search/ansi
				 * @see mshop/text/manager/standard/count/ansi
				 */
				$path = 'mshop/text/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getLanguageId() );
			$stmt->bind( 3, $item->getTypeId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 4, $item->getDomain() );
			$stmt->bind( 5, $item->getLabel() );
			$stmt->bind( 6, $item->getContent() );
			$stmt->bind( 7, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 8, $date ); // mtime
			$stmt->bind( 9, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 10, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 10, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/text/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/text/manager/standard/newid/ansi
				 */

				/** mshop/text/manager/standard/newid/ansi
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
				 *  SELECT currval('seq_mtex_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mtex_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/text/manager/standard/insert/ansi
				 * @see mshop/text/manager/standard/update/ansi
				 * @see mshop/text/manager/standard/delete/ansi
				 * @see mshop/text/manager/standard/search/ansi
				 * @see mshop/text/manager/standard/count/ansi
				 */
				$path = 'mshop/text/manager/standard/newid';
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
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/text/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/text/manager/standard/delete/ansi
		 */

		/** mshop/text/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the text database.
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
		 * @see mshop/text/manager/standard/insert/ansi
		 * @see mshop/text/manager/standard/update/ansi
		 * @see mshop/text/manager/standard/newid/ansi
		 * @see mshop/text/manager/standard/search/ansi
		 * @see mshop/text/manager/standard/count/ansi
		 */
		$path = 'mshop/text/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns the text item object specified by the given ID.
	 *
	 * @param integer $id Id of the text item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Text\Item\Iface Returns the text item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'text.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/text/manager/submanagers';

		return $this->getResourceTypeBase( 'text', $path, array( 'type', 'lists' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/text/manager/submanagers
		 * List of manager names that can be instantiated by the text manager
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
		$path = 'mshop/text/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'type', 'lists' ), $withsub );
	}


	/**
	 * Searches for all text items matching the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of text items implementing \Aimeos\MShop\Text\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$map = $typeIds = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'text' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;

			/** mshop/text/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/text/manager/standard/search/ansi
			 */

			/** mshop/text/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the text
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
			 * @see mshop/text/manager/standard/insert/ansi
			 * @see mshop/text/manager/standard/update/ansi
			 * @see mshop/text/manager/standard/newid/ansi
			 * @see mshop/text/manager/standard/delete/ansi
			 * @see mshop/text/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/text/manager/standard/search';

			/** mshop/text/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/text/manager/standard/count/ansi
			 */

			/** mshop/text/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the text
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
			 * @see mshop/text/manager/standard/insert/ansi
			 * @see mshop/text/manager/standard/update/ansi
			 * @see mshop/text/manager/standard/newid/ansi
			 * @see mshop/text/manager/standard/delete/ansi
			 * @see mshop/text/manager/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/text/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[$row['text.id']] = $row;
				$typeIds[$row['text.typeid']] = null;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		if( !empty( $typeIds ) )
		{
			$typeManager = $this->getSubManager( 'type' );
			$typeSearch = $typeManager->createSearch();
			$typeSearch->setConditions( $typeSearch->compare( '==', 'text.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[$row['text.typeid']] ) )
				{
					$map[$id]['text.type'] = $typeItems[$row['text.typeid']]->getCode();
					$map[$id]['text.typename'] = $typeItems[$row['text.typeid']]->getName();
				}
			}
		}

		return $this->buildItems( $map, $ref, 'text' );
	}


	/**
	 * Returns a new manager for text extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'text', $manager, $name );
	}


	/**
	 * Creates a search object.
	 *
	 * @param boolean $default If base criteria should be added
	 * @return \Aimeos\MW\Criteria\Iface Search criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$object = $this->createSearchBase( 'text' );
			$langid = $this->getContext()->getLocale()->getLanguageId();

			if( $langid !== null )
			{
				$temp = array(
					$object->compare( '==', 'text.languageid', $langid ),
					$object->compare( '==', 'text.languageid', null ),
				);

				$expr = array(
					$object->getConditions(),
					$object->combine( '||', $temp ),
				);

				$object->setConditions( $object->combine( '&&', $expr ) );
			}

			return $object;
		}

		return parent::createSearch();
	}


	/**
	 * Creates a new text item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listItems List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 * @param array $refItems List of items implementing \Aimeos\MShop\Text\Item\Iface
	 * @return \Aimeos\MShop\Text\Item\Iface New product item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [] )
	{
		return new \Aimeos\MShop\Text\Item\Standard( $values, $listItems, $refItems );
	}
}
