<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Tag
 */


namespace Aimeos\MShop\Tag\Manager;


/**
 * Default tag manager implementation.
 *
 * @package MShop
 * @subpackage Tag
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Tag\Manager\Iface
{
	private $searchConfig = array(
		'tag.id'=> array(
			'code'=>'tag.id',
			'internalcode'=>'mtag."id"',
			'label'=>'Tag ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'tag.siteid'=> array(
			'code'=>'tag.siteid',
			'internalcode'=>'mtag."siteid"',
			'label'=>'Tag site ID',
			'type'=> 'integer',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'tag.typeid' => array(
			'code'=>'tag.typeid',
			'internalcode'=>'mtag."typeid"',
			'label'=>'Tag type id',
			'type'=> 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'tag.domain' => array(
			'code'=>'tag.domain',
			'internalcode'=>'mtag."domain"',
			'label'=>'Tag domain',
			'type'=> 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'tag.languageid' => array(
			'code'=>'tag.languageid',
			'internalcode'=>'mtag."langid"',
			'label'=>'Tag language id',
			'type'=> 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'tag.label' => array(
			'code'=>'tag.label',
			'internalcode'=>'mtag."label"',
			'label'=>'Tag label',
			'type'=> 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'tag.mtime'=> array(
			'code'=>'tag.mtime',
			'internalcode'=>'mtag."mtime"',
			'label'=>'Tag modification date',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'tag.ctime'=> array(
			'code'=>'tag.ctime',
			'internalcode'=>'mtag."ctime"',
			'label'=>'Tag creation date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'tag.editor'=> array(
			'code'=>'tag.editor',
			'internalcode'=>'mtag."editor"',
			'label'=>'Tag editor',
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
		$this->setResourceName( 'db-tag' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/tag/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/tag/manager/standard/delete' );
	}


	/**
	 * Creates new tag item object.
	 *
	 * @return \Aimeos\MShop\Tag\Item\Iface New tag item object
	 */
	public function createItem()
	{
		$values = array( 'tag.siteid' => $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Inserts the new tag items for tag item
	 *
	 * @param \Aimeos\MShop\Tag\Item\Iface $item Tag item which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Tag\\Item\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Tag\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
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
				/** mshop/tag/manager/standard/insert/mysql
				 * Inserts a new tag tag record into the database table
				 *
				 * @see mshop/tag/manager/standard/insert/ansi
				 */

				/** mshop/tag/manager/standard/insert/ansi
				 * Inserts a new tag tag record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the tag tag item to the statement before they are
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
				 * @since 2015.12
				 * @category Developer
				 * @see mshop/tag/manager/standard/update/ansi
				 * @see mshop/tag/manager/standard/newid/ansi
				 * @see mshop/tag/manager/standard/delete/ansi
				 * @see mshop/tag/manager/standard/search/ansi
				 * @see mshop/tag/manager/standard/count/ansi
				 */
				$path = 'mshop/tag/manager/standard/insert';
			}
			else
			{
				/** mshop/tag/manager/standard/update/mysql
				 * Updates an existing tag tag record in the database
				 *
				 * @see mshop/tag/manager/standard/update/ansi
				 */

				/** mshop/tag/manager/standard/update/ansi
				 * Updates an existing tag tag record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the tag tag item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2015.12
				 * @category Developer
				 * @see mshop/tag/manager/standard/insert/ansi
				 * @see mshop/tag/manager/standard/newid/ansi
				 * @see mshop/tag/manager/standard/delete/ansi
				 * @see mshop/tag/manager/standard/search/ansi
				 * @see mshop/tag/manager/standard/count/ansi
				 */
				$path = 'mshop/tag/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getLanguageId() );
			$stmt->bind( 3, $item->getTypeId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 4, $item->getDomain() );
			$stmt->bind( 5, $item->getLabel() );
			$stmt->bind( 6, $date ); //mtime
			$stmt->bind( 7, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 8, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id ); //is not modified anymore
			} else {
				$stmt->bind( 8, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/tag/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/tag/manager/standard/newid/ansi
				 */

				/** mshop/tag/manager/standard/newid/ansi
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
				 *  SELECT currval('seq_mtag_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mtag_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2015.12
				 * @category Developer
				 * @see mshop/tag/manager/standard/insert/ansi
				 * @see mshop/tag/manager/standard/update/ansi
				 * @see mshop/tag/manager/standard/delete/ansi
				 * @see mshop/tag/manager/standard/search/ansi
				 * @see mshop/tag/manager/standard/count/ansi
				 */
				$path = 'mshop/tag/manager/standard/newid';
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
		/** mshop/tag/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/tag/manager/standard/delete/ansi
		 */

		/** mshop/tag/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the tag database.
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
		 * @since 2015.12
		 * @category Developer
		 * @see mshop/tag/manager/standard/insert/ansi
		 * @see mshop/tag/manager/standard/update/ansi
		 * @see mshop/tag/manager/standard/newid/ansi
		 * @see mshop/tag/manager/standard/search/ansi
		 * @see mshop/tag/manager/standard/count/ansi
		 */
		$path = 'mshop/tag/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns tag tag item with given Id.
	 *
	 * @param integer $id Id of the tag tag item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Tag\Item\Iface Returns the tag tag item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'tag.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/tag/manager/submanagers';

		return $this->getResourceTypeBase( 'tag', $path, array( 'type' ), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/tag/manager/submanagers
		 * List of manager names that can be instantiated by the tag tag manager
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
		 * @since 2015.12
		 * @category Developer
		 */
		$path = 'mshop/tag/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'type' ), $withsub );
	}


	/**
	 * Search for all tag items based on the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of tag items implementing \Aimeos\MShop\Tag\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$items = $map = $typeIds = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'tag' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;

			/** mshop/tag/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/tag/manager/standard/search/ansi
			 */

			/** mshop/tag/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the tag
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
			 * @since 2015.12
			 * @category Developer
			 * @see mshop/tag/manager/standard/insert/ansi
			 * @see mshop/tag/manager/standard/update/ansi
			 * @see mshop/tag/manager/standard/newid/ansi
			 * @see mshop/tag/manager/standard/delete/ansi
			 * @see mshop/tag/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/tag/manager/standard/search';

			/** mshop/tag/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/tag/manager/standard/count/ansi
			 */

			/** mshop/tag/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the tag
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
			 * @since 2015.12
			 * @category Developer
			 * @see mshop/tag/manager/standard/insert/ansi
			 * @see mshop/tag/manager/standard/update/ansi
			 * @see mshop/tag/manager/standard/newid/ansi
			 * @see mshop/tag/manager/standard/delete/ansi
			 * @see mshop/tag/manager/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/tag/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false )
			{
				$map[$row['tag.id']] = $row;
				$typeIds[$row['tag.typeid']] = null;
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
			$typeSearch->setConditions( $typeSearch->compare( '==', 'tag.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[$row['tag.typeid']] ) )
				{
					$row['tag.type'] = $typeItems[$row['tag.typeid']]->getCode();
					$row['tag.typename'] = $typeItems[$row['tag.typeid']]->getName();
				}

				$items[$id] = $this->createItemBase( $row );
			}
		}

		return $items;
	}


	/**
	 * Returns a new manager for tag extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from
	 * configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g tag types, tag lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** mshop/tag/manager/name
		 * Class name of the used tag tag manager implementation
		 *
		 * Each default tag tag manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Tag\Manager\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Tag\Manager\Mytag
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/tag/manager/name = Mytag
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyTag"!
		 *
		 * @param string Last part of the class name
		 * @since 2015.12
		 * @category Developer
		 */

		/** mshop/tag/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the tag tag manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the tag tag manager.
		 *
		 *  mshop/tag/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the tag tag manager.
		 *
		 * @param array List of decorator names
		 * @since 2015.12
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/tag/manager/decorators/global
		 * @see mshop/tag/manager/decorators/local
		 */

		/** mshop/tag/manager/decorators/global
		 * Adds a list of globally available decorators only to the tag tag manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the tag tag manager.
		 *
		 *  mshop/tag/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the tag controller.
		 *
		 * @param array List of decorator names
		 * @since 2015.12
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/tag/manager/decorators/excludes
		 * @see mshop/tag/manager/decorators/local
		 */

		/** mshop/tag/manager/decorators/local
		 * Adds a list of local decorators only to the tag tag manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the tag tag manager.
		 *
		 *  mshop/tag/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the tag
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2015.12
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/tag/manager/decorators/excludes
		 * @see mshop/tag/manager/decorators/global
		 */

		return $this->getSubManagerBase( 'tag', '' . $manager, $name );
	}


	/**
	 * Creates new tag item object.
	 *
	 * @see \Aimeos\MShop\Tag\Item\Standard Default tag item
	 * @param array $values Possible optional array keys can be given: id, typeid, langid, type, label
	 * @return \Aimeos\MShop\Tag\Item\Standard New tag item object
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Tag\Item\Standard( $values );
	}
}
