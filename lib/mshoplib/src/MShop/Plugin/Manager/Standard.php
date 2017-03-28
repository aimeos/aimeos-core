<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Manager;


/**
 * Default plugin manager implementation.
 *
 * @package MShop
 * @subpackage Plugin
 */
class Standard
	extends \Aimeos\MShop\Plugin\Manager\Base
	implements \Aimeos\MShop\Plugin\Manager\Iface
{
	private $plugins = [];

	private $searchConfig = array(
		'plugin.id' => array(
			'label' => 'Plugin ID',
			'code' => 'plugin.id',
			'internalcode' => 'mplu."id"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'plugin.siteid' => array(
			'label' => 'Plugin site ID',
			'code' => 'plugin.siteid',
			'internalcode' => 'mplu."siteid"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'plugin.typeid' => array(
			'label' => 'Plugin type ID',
			'code' => 'plugin.typeid',
			'internalcode' => 'mplu."typeid"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'plugin.label' => array(
			'label' => 'Plugin label',
			'code' => 'plugin.label',
			'internalcode' => 'mplu."label"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'plugin.provider' => array(
			'label' => 'Plugin provider',
			'code' => 'plugin.provider',
			'internalcode' => 'mplu."provider"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'plugin.config' => array(
			'label' => 'Plugin config',
			'code' => 'plugin.config',
			'internalcode' => 'mplu."config"',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'plugin.position' => array(
			'label' => 'Plugin position',
			'code' => 'plugin.position',
			'internalcode' => 'mplu."pos"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'plugin.status' => array(
			'label' => 'Plugin status',
			'code' => 'plugin.status',
			'internalcode' => 'mplu."status"',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'plugin.mtime'=> array(
			'code'=>'plugin.mtime',
			'internalcode'=>'mplu."mtime"',
			'label'=>'Plugin modification date',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'plugin.ctime'=> array(
			'code'=>'plugin.ctime',
			'internalcode'=>'mplu."ctime"',
			'label'=>'Plugin creation date/time',
			'type'=> 'datetime',
			'internaltype'=> \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'plugin.editor'=> array(
			'code'=>'plugin.editor',
			'internalcode'=>'mplu."editor"',
			'label'=>'Plugin editor',
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
		$this->setResourceName( 'db-plugin' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/plugin/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array( 'type' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/plugin/manager/standard/delete' );
	}


	/**
	 * Creates a new plugin object.
	 *
	 * @return \Aimeos\MShop\Plugin\Item\Iface New plugin object
	 */
	public function createItem()
	{
		$values = array( 'plugin.siteid' => $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );
	}


	/**
	 * Creates a criteria object for searching.
	 *
	 * @param boolean $default Prepopulate object with default criterias
	 * @return \Aimeos\MW\Criteria\Iface
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->createSearchBase( 'plugin' );
		}

		return parent::createSearch();
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/plugin/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/plugin/manager/standard/delete/ansi
		 */

		/** mshop/plugin/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the plugin database.
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
		 * @see mshop/plugin/manager/standard/insert/ansi
		 * @see mshop/plugin/manager/standard/update/ansi
		 * @see mshop/plugin/manager/standard/newid/ansi
		 * @see mshop/plugin/manager/standard/search/ansi
		 * @see mshop/plugin/manager/standard/count/ansi
		 */
		$path = 'mshop/plugin/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/plugin/manager/submanagers';

		return $this->getResourceTypeBase( 'plugin', $path, array( 'type'), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/plugin/manager/submanagers
		 * List of manager names that can be instantiated by the plugin manager
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
		$path = 'mshop/plugin/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array( 'type' ), $withsub );
	}


	/**
	 * Returns a new manager for plugin extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'plugin', $manager, $name );
	}


	/**
	 * Returns plugin item specified by the given ID.
	 *
	 * @param integer $id Unique ID of the plugin item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Plugin\Item\Iface Returns the plugin item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'plugin.id', $id, $ref, $default );
	}


	/**
	 * Returns the plugin provider which is responsible for the plugin item.
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Returns the decoratad plugin provider object
	 * @throws \Aimeos\MShop\Plugin\Exception If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Plugin\Item\Iface $item )
	{
		$type = ucwords( $item->getType() );
		$names = explode( ',', $item->getProvider() );

		if( ctype_alnum( $type ) === false ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Invalid characters in type name "%1$s"', $type ) );
		}

		if( ( $provider = array_shift( $names ) ) === null ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ) );
		}

		if( ctype_alnum( $provider ) === false ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Invalid characters in provider name "%1$s"', $provider ) );
		}

		$interface = '\\Aimeos\\MShop\\Plugin\\Provider\\Factory\\Iface';
		$classname = '\\Aimeos\\MShop\\Plugin\\Provider\\' . $type . '\\' . $provider;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$context = $this->getContext();
		$config = $context->getConfig();
		$provider = new $classname( $context, $item );

		if( ( $provider instanceof $interface ) === false )
		{
			$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface );
			throw new \Aimeos\MShop\Plugin\Exception( $msg );
		}

		/** mshop/plugin/provider/order/decorators
		 * Adds a list of decorators to all order plugin provider objects automatcally
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap decorators
		 * ("\Aimeos\MShop\Plugin\Provider\Decorator\*") around the order provider.
		 *
		 *  mshop/plugin/provider/order/decorators = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Plugin\Provider\Decorator\Decorator1" to all order provider
		 * objects.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/plugin/provider/order/decorators
		 */
		$decorators = $config->get( 'mshop/plugin/provider/' . $item->getType() . '/decorators', [] );

		$provider = $this->addPluginDecorators( $item, $provider, $names );
		return $this->addPluginDecorators( $item, $provider, $decorators );
	}


	/**
	 * Registers plugins to the given publisher.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $publisher Publisher object
	 * @param string $type Unique plugin type code
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $publisher, $type )
	{
		if( !isset( $this->plugins[$type] ) )
		{
			$search = $this->createSearch( true );

			$expr = array(
				$search->compare( '==', 'plugin.type.code', $type ),
				$search->getConditions(),
			);

			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', 'plugin.position' ) ) );

			$this->plugins[$type] = [];

			foreach( $this->searchItems( $search ) as $item ) {
				$this->plugins[$type][$item->getId()] = $this->getProvider( $item );
			}
		}

		foreach( $this->plugins[$type] as $plugin ) {
			$plugin->register( $publisher );
		}
	}


	/**
	 * Saves a new or modified plugin to the storage.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Plugin item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Plugin\\Item\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
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
				/** mshop/plugin/manager/standard/insert/mysql
				 * Inserts a new plugin record into the database table
				 *
				 * @see mshop/plugin/manager/standard/insert/ansi
				 */

				/** mshop/plugin/manager/standard/insert/ansi
				 * Inserts a new plugin record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the plugin item to the statement before they are
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
				 * @see mshop/plugin/manager/standard/update/ansi
				 * @see mshop/plugin/manager/standard/newid/ansi
				 * @see mshop/plugin/manager/standard/delete/ansi
				 * @see mshop/plugin/manager/standard/search/ansi
				 * @see mshop/plugin/manager/standard/count/ansi
				 */
				$path = 'mshop/plugin/manager/standard/insert';
			}
			else
			{
				/** mshop/plugin/manager/standard/update/mysql
				 * Updates an existing plugin record in the database
				 *
				 * @see mshop/plugin/manager/standard/update/ansi
				 */

				/** mshop/plugin/manager/standard/update/ansi
				 * Updates an existing plugin record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the plugin item to the statement before they are
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
				 * @see mshop/plugin/manager/standard/insert/ansi
				 * @see mshop/plugin/manager/standard/newid/ansi
				 * @see mshop/plugin/manager/standard/delete/ansi
				 * @see mshop/plugin/manager/standard/search/ansi
				 * @see mshop/plugin/manager/standard/count/ansi
				 */
				$path = 'mshop/plugin/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getTypeId() );
			$stmt->bind( 3, $item->getLabel() );
			$stmt->bind( 4, $item->getProvider() );
			$stmt->bind( 5, json_encode( $item->getConfig() ) );
			$stmt->bind( 6, $item->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 7, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 8, $date ); //mtime
			$stmt->bind( 9, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 10, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 10, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/plugin/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/plugin/manager/standard/newid/ansi
				 */

				/** mshop/plugin/manager/standard/newid/ansi
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
				 *  SELECT currval('seq_mplu_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mplu_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/plugin/manager/standard/insert/ansi
				 * @see mshop/plugin/manager/standard/update/ansi
				 * @see mshop/plugin/manager/standard/delete/ansi
				 * @see mshop/plugin/manager/standard/search/ansi
				 * @see mshop/plugin/manager/standard/count/ansi
				 */
				$path = 'mshop/plugin/manager/standard/newid';
				$item->setId( $this->newId( $conn, $path ) );
			}

			$this->plugins[$id] = $item;

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Searches for plugin items matching the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of plugin items implementing \Aimeos\MShop\Plugin\Item\Iface
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
			$required = array( 'plugin' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;

			/** mshop/plugin/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/plugin/manager/standard/search/ansi
			 */

			/** mshop/plugin/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the plugin
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
			 * @see mshop/plugin/manager/standard/insert/ansi
			 * @see mshop/plugin/manager/standard/update/ansi
			 * @see mshop/plugin/manager/standard/newid/ansi
			 * @see mshop/plugin/manager/standard/delete/ansi
			 * @see mshop/plugin/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/plugin/manager/standard/search';

			/** mshop/plugin/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/plugin/manager/standard/count/ansi
			 */

			/** mshop/plugin/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the plugin
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
			 * @see mshop/plugin/manager/standard/insert/ansi
			 * @see mshop/plugin/manager/standard/update/ansi
			 * @see mshop/plugin/manager/standard/newid/ansi
			 * @see mshop/plugin/manager/standard/delete/ansi
			 * @see mshop/plugin/manager/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/plugin/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$config = $row['plugin.config'];

				if( ( $row['plugin.config'] = json_decode( $row['plugin.config'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'plugin.config', $row['plugin.id'], $config );
					$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN );
				}

				$map[$row['plugin.id']] = $row;
				$typeIds[$row['plugin.typeid']] = null;
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
			$typeSearch->setConditions( $typeSearch->compare( '==', 'plugin.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[$row['plugin.typeid']] ) )
				{
					$row['plugin.type'] = $typeItems[$row['plugin.typeid']]->getCode();
					$row['plugin.typename'] = $typeItems[$row['plugin.typeid']]->getName();
				}

				$items[$id] = $this->createItemBase( $row );
			}
		}

		return $items;
	}


	/**
	 * Creates a new plugin object.
	 *
	 * @param array Associative list of item key/value pairs
	 * @return \Aimeos\MShop\Plugin\Item\Iface New plugin object
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Plugin\Item\Standard( $values );
	}
}
