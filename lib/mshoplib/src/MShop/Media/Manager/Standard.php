<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Media
 */


namespace Aimeos\MShop\Media\Manager;


/**
 * Default media manager implementation.
 *
 * @package MShop
 * @subpackage Media
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\ListRef\Base
	implements \Aimeos\MShop\Media\Manager\Iface
{
	private $searchConfig = array(
		'media.id' => array(
			'label' => 'Media ID',
			'code' => 'media.id',
			'internalcode' => 'mmed."id"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'media.siteid' => array(
			'label' => 'Media site ID',
			'code' => 'media.siteid',
			'internalcode' => 'mmed."siteid"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'media.typeid' => array(
			'label' => 'Media type ID',
			'code' => 'media.typeid',
			'internalcode' => 'mmed."typeid"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'media.languageid' => array(
			'label' => 'Media language code',
			'code' => 'media.languageid',
			'internalcode' => 'mmed."langid"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.domain' => array(
			'label' => 'Media domain',
			'code' => 'media.domain',
			'internalcode' => 'mmed."domain"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.label' => array(
			'label' => 'Media label',
			'code' => 'media.label',
			'internalcode' => 'mmed."label"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.mimetype' => array(
			'label' => 'Media mimetype',
			'code' => 'media.mimetype',
			'internalcode' => 'mmed."mimetype"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.url' => array(
			'label' => 'Media URL',
			'code' => 'media.url',
			'internalcode' => 'mmed."link"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.preview' => array(
			'label' => 'Media preview URL',
			'code' => 'media.preview',
			'internalcode' => 'mmed."preview"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.status' => array(
			'label' => 'Media status',
			'code' => 'media.status',
			'internalcode' => 'mmed."status"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'media.ctime'=> array(
			'code'=>'media.ctime',
			'internalcode'=>'mmed."ctime"',
			'label'=>'Media create date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.mtime'=> array(
			'code'=>'media.mtime',
			'internalcode'=>'mmed."mtime"',
			'label'=>'Media modification date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'media.editor'=> array(
			'code'=>'media.editor',
			'internalcode'=>'mmed."editor"',
			'label'=>'Media editor',
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
		$this->setResourceName( 'db-media' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/media/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'type', 'lists' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/media/manager/standard/delete' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/media/manager/submanagers';

		return $this->getResourceTypeBase( 'media', $path, array( 'type', 'lists' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/media/manager/submanagers
		 * List of manager names that can be instantiated by the media manager
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
		$path = 'mshop/media/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'type', 'lists' ), $withsub );
	}


	/**
	 * Creates a new media object.
	 *
	 * @return \Aimeos\MShop\Media\Item\Iface New media object
	 */
	public function createItem()
	{
		$values = array( 'media.siteid' => $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/media/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/media/manager/standard/delete/ansi
		 */

		/** mshop/media/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the media database.
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
		 * @see mshop/media/manager/standard/insert/ansi
		 * @see mshop/media/manager/standard/update/ansi
		 * @see mshop/media/manager/standard/newid/ansi
		 * @see mshop/media/manager/standard/search/ansi
		 * @see mshop/media/manager/standard/count/ansi
		 */
		$path = 'mshop/media/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns an item for the given ID.
	 *
	 * @param integer $id ID of the item that should be retrieved
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Media\Item\Iface Returns the media item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'media.id', $id, $ref, $default );
	}


	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param \Aimeos\MShop\Media\Item\Iface $item New item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Media\\Item\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Media\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
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
				/** mshop/media/manager/standard/insert/mysql
				 * Inserts a new media record into the database table
				 *
				 * @see mshop/media/manager/standard/insert/ansi
				 */

				/** mshop/media/manager/standard/insert/ansi
				 * Inserts a new media record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the media item to the statement before they are
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
				 * @see mshop/media/manager/standard/update/ansi
				 * @see mshop/media/manager/standard/newid/ansi
				 * @see mshop/media/manager/standard/delete/ansi
				 * @see mshop/media/manager/standard/search/ansi
				 * @see mshop/media/manager/standard/count/ansi
				 */
				$path = 'mshop/media/manager/standard/insert';
			}
			else
			{
				/** mshop/media/manager/standard/update/mysql
				 * Updates an existing media record in the database
				 *
				 * @see mshop/media/manager/standard/update/ansi
				 */

				/** mshop/media/manager/standard/update/ansi
				 * Updates an existing media record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the media item to the statement before they are
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
				 * @see mshop/media/manager/standard/insert/ansi
				 * @see mshop/media/manager/standard/newid/ansi
				 * @see mshop/media/manager/standard/delete/ansi
				 * @see mshop/media/manager/standard/search/ansi
				 * @see mshop/media/manager/standard/count/ansi
				 */
				$path = 'mshop/media/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getLanguageId() );
			$stmt->bind( 3, $item->getTypeId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 4, $item->getLabel() );
			$stmt->bind( 5, $item->getMimeType() );
			$stmt->bind( 6, $item->getUrl() );
			$stmt->bind( 7, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 8, $item->getDomain() );
			$stmt->bind( 9, $item->getPreview() );
			$stmt->bind( 10, $date ); // mtime
			$stmt->bind( 11, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 12, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id ); //is not modified anymore
			} else {
				$stmt->bind( 12, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/media/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/media/manager/standard/newid/ansi
				 */

				/** mshop/media/manager/standard/newid/ansi
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
				 *  SELECT currval('seq_mmed_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mmed_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/media/manager/standard/insert/ansi
				 * @see mshop/media/manager/standard/update/ansi
				 * @see mshop/media/manager/standard/delete/ansi
				 * @see mshop/media/manager/standard/search/ansi
				 * @see mshop/media/manager/standard/count/ansi
				 */
				$path = 'mshop/media/manager/standard/newid';
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
	 * @return array List of items implementing \Aimeos\MShop\Media\Item\Iface
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
			$required = array( 'media' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;

			/** mshop/media/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/media/manager/standard/search/ansi
			 */

			/** mshop/media/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the media
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
			 * @see mshop/media/manager/standard/insert/ansi
			 * @see mshop/media/manager/standard/update/ansi
			 * @see mshop/media/manager/standard/newid/ansi
			 * @see mshop/media/manager/standard/delete/ansi
			 * @see mshop/media/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/media/manager/standard/search';

			/** mshop/media/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/media/manager/standard/count/ansi
			 */

			/** mshop/media/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the media
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
			 * @see mshop/media/manager/standard/insert/ansi
			 * @see mshop/media/manager/standard/update/ansi
			 * @see mshop/media/manager/standard/newid/ansi
			 * @see mshop/media/manager/standard/delete/ansi
			 * @see mshop/media/manager/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/media/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[$row['media.id']] = $row;
				$typeIds[$row['media.typeid']] = null;
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
			$typeSearch->setConditions( $typeSearch->compare( '==', 'media.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[$row['media.typeid']] ) )
				{
					$map[$id]['media.type'] = $typeItems[$row['media.typeid']]->getCode();
					$map[$id]['media.typename'] = $typeItems[$row['media.typeid']]->getName();
				}
			}
		}

		return $this->buildItems( $map, $ref, 'media' );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return \Aimeos\MW\Criteria\Iface
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$object = $this->createSearchBase( 'media' );
			$langid = $this->getContext()->getLocale()->getLanguageId();

			if( $langid !== null )
			{
				$temp = array(
					$object->compare( '==', 'media.languageid', $langid ),
					$object->compare( '==', 'media.languageid', null ),
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
	 * Returns a new manager for product extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'media', $manager, $name );
	}


	/**
	 * Creates a new media item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listItems List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 * @param array $refItems List of items reference to this item
	 * @return \Aimeos\MShop\Media\Item\Iface New product item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [] )
	{
		return new \Aimeos\MShop\Media\Item\Standard( $values, $listItems, $refItems );
	}
}
