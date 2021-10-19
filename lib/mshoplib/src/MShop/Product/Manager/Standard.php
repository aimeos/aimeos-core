<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Manager;


/**
 * Default product manager.
 *
 * @package MShop
 * @subpackage Product
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Product\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface,
		\Aimeos\MShop\Common\Manager\ListsRef\Iface, \Aimeos\MShop\Common\Manager\PropertyRef\Iface
{
	use \Aimeos\MShop\Common\Manager\ListsRef\Traits;
	use \Aimeos\MShop\Common\Manager\PropertyRef\Traits;


	private $searchConfig = array(
		'product.id' => array(
			'code' => 'product.id',
			'internalcode' => 'mpro."id"',
			'label' => 'ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'product.siteid' => array(
			'code' => 'product.siteid',
			'internalcode' => 'mpro."siteid"',
			'label' => 'Site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'product.type' => array(
			'code' => 'product.type',
			'internalcode' => 'mpro."type"',
			'label' => 'Type',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'product.label' => array(
			'code' => 'product.label',
			'internalcode' => 'mpro."label"',
			'label' => 'Label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'product.code' => array(
			'code' => 'product.code',
			'internalcode' => 'mpro."code"',
			'label' => 'SKU',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'product.url' => array(
			'code' => 'product.url',
			'internalcode' => 'mpro."url"',
			'label' => 'URL segment',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'product.dataset' => array(
			'code' => 'product.dataset',
			'internalcode' => 'mpro."dataset"',
			'label' => 'Data set',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'product.datestart' => array(
			'code' => 'product.datestart',
			'internalcode' => 'mpro."start"',
			'label' => 'Start date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'product.dateend' => array(
			'code' => 'product.dateend',
			'internalcode' => 'mpro."end"',
			'label' => 'End date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'product.instock' => array(
			'code' => 'product.instock',
			'internalcode' => 'mpro."instock"',
			'label' => 'Product in stock',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'product.status' => array(
			'code' => 'product.status',
			'internalcode' => 'mpro."status"',
			'label' => 'Status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'product.scale' => array(
			'code' => 'product.scale',
			'internalcode' => 'mpro."scale"',
			'label' => 'Quantity scale',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
		),
		'product.config' => array(
			'code' => 'product.config',
			'internalcode' => 'mpro."config"',
			'label' => 'Config',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'product.target' => array(
			'code' => 'product.target',
			'internalcode' => 'mpro."target"',
			'label' => 'URL target',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'product.ctime' => array(
			'code' => 'product.ctime',
			'internalcode' => 'mpro."ctime"',
			'label' => 'Create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'product.mtime' => array(
			'code' => 'product.mtime',
			'internalcode' => 'mpro."mtime"',
			'label' => 'Modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'product.editor' => array(
			'code' => 'product.editor',
			'internalcode' => 'mpro."editor"',
			'label' => 'Editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'product.rating' => array(
			'code' => 'product.rating',
			'internalcode' => 'mpro."rating"',
			'label' => 'Rating value',
			'type' => 'decimal',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'product.ratings' => array(
			'code' => 'product.ratings',
			'internalcode' => 'mpro."ratings"',
			'label' => 'Number of ratings',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'product:has' => array(
			'code' => 'product:has()',
			'internalcode' => ':site AND :key AND mproli."id"',
			'internaldeps' => ['LEFT JOIN "mshop_product_list" AS mproli ON ( mproli."parentid" = mpro."id" )'],
			'label' => 'Product has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
			'type' => 'null',
			'internaltype' => 'null',
			'public' => false,
		),
		'product:prop' => array(
			'code' => 'product:prop()',
			'internalcode' => ':site AND :key AND mpropr."id"',
			'internaldeps' => ['LEFT JOIN "mshop_product_property" AS mpropr ON ( mpropr."parentid" = mpro."id" )'],
			'label' => 'Product has property item, parameter(<property type>[,<language code>[,<property value>]])',
			'type' => 'null',
			'internaltype' => 'null',
			'public' => false,
		),
	);

	private $date;


	/**
	 * Creates the product manager that will use the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->setResourceName( 'db-product' );
		$this->date = $context->getDateTime();

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$level = $context->getConfig()->get( 'mshop/product/manager/sitemode', $level );


		$this->searchConfig['product:has']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];

			foreach( (array) ( $params[1] ?? '' ) as $type ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id;
				}
			}

			$sitestr = $this->getSiteString( 'mproli."siteid"', $level );
			$keystr = $this->toExpression( 'mproli."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};


		$this->searchConfig['product:prop']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];
			$langs = array_key_exists( 1, $params ) ? ( $params[1] ?? 'null' ) : '';

			foreach( (array) $langs as $lang ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = $params[0] . '|' . ( $lang === null ? 'null|' : ( $lang ? $lang . '|' : '' ) ) . ( $id != '' ? md5( $id ) : '' );
				}
			}

			$sitestr = $this->getSiteString( 'mpropr."siteid"', $level );
			$keystr = $this->toExpression( 'mpropr."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Product\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/product/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, ['lists', 'property', 'type'] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/product/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Product\Item\Iface New product item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['product.siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
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
			$object = $this->filterBase( 'product', $default );

			$expr = [$object->getConditions()];

			$temp = array(
				$object->compare( '==', 'product.type', 'event' ),
				$object->compare( '==', 'product.datestart', null ),
				$object->compare( '<=', 'product.datestart', $this->date ),
			);
			$expr[] = $object->or( $temp );

			$temp = array(
				$object->compare( '==', 'product.dateend', null ),
				$object->compare( '>=', 'product.dateend', $this->date ),
			);

			/** mshop/product/manager/strict-events
			 * Hide events automatically if they are over
			 *
			 * Events are hidden by default if they are finished, removed from the
			 * list view and can't be bought any more. If you sell webinars including
			 * an archive of old ones you want to continue to sell for example, then
			 * these webinars should be still shown.
			 *
			 * Setting this configuration option to false will display event products
			 * that are already over and customers can still buy them.
			 *
			 * @param bool TRUE to hide events after they are over (default), FALSE to continue to show them
			 * @category Developer
			 * @category User
			 * @since 2019.10
			 */
			if( !$this->getContext()->getConfig()->get( 'mshop/product/manager/strict-events', true ) ) {
				$temp[] = $object->compare( '==', 'product.type', 'event' );
			}

			$expr[] = $object->or( $temp );

			$object->setConditions( $object->and( $expr ) );

			return $object;
		}

		return parent::filter();
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Product\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/product/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/product/manager/delete/ansi
		 */

		/** mshop/product/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the product database.
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
		 * @see mshop/product/manager/insert/ansi
		 * @see mshop/product/manager/update/ansi
		 * @see mshop/product/manager/newid/ansi
		 * @see mshop/product/manager/search/ansi
		 * @see mshop/product/manager/count/ansi
		 * @see mshop/product/manager/rate/ansi
		 * @see mshop/product/manager/stock/ansi
		 */
		$path = 'mshop/product/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path )->deleteRefItems( $itemIds );
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
		return $this->findBase( array( 'product.code' => $code ), $ref, $default );
	}


	/**
	 * Returns the product item for the given product ID.
	 *
	 * @param string $id Unique ID of the product item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Product\Item\Iface Returns the product item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'product.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/product/manager/submanagers';
		return $this->getResourceTypeBase( 'product', $path, ['lists', 'property'], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/product/manager/submanagers
		 * List of manager names that can be instantiated by the product manager
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
		$path = 'mshop/product/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Submanager, e.g. type, property, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'product', $manager, $name );
	}


	/**
	 * Updates the rating of the item
	 *
	 * @param string $id ID of the item
	 * @param string $rating Decimal value of the rating
	 * @param int $ratings Total number of ratings for the item
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function rate( string $id, string $rating, int $ratings ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/product/manager/rate/mysql
			 * Updates the rating of the product in the database
			 *
			 * @see mshop/product/manager/rate/ansi
			 */

			/** mshop/product/manager/rate/ansi
			 * Updates the rating of the product in the database
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values for the rating to the statement before they are
			 * sent to the database server. The order of the columns must
			 * correspond to the order in the rate() method, so the
			 * correct values are bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for update ratings
			 * @since 2020.10
			 * @category Developer
			 * @see mshop/product/manager/insert/ansi
			 * @see mshop/product/manager/update/ansi
			 * @see mshop/product/manager/newid/ansi
			 * @see mshop/product/manager/delete/ansi
			 * @see mshop/product/manager/search/ansi
			 * @see mshop/product/manager/count/ansi
			 * @see mshop/product/manager/stock/ansi
			 */
			$path = 'mshop/product/manager/rate';

			$stmt = $this->getCachedStatement( $conn, $path, $this->getSqlConfig( $path ) );

			$stmt->bind( 1, $rating );
			$stmt->bind( 2, $ratings, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 3, $context->getLocale()->getSiteId() );
			$stmt->bind( 4, (int) $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

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
	 * Updates if the product is in stock
	 *
	 * @param string $id ID of the procuct item
	 * @param int $value "0" or "1" if product is in stock or not
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function stock( string $id, int $value ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/product/manager/stock/mysql
			 * Updates the rating of the product in the database
			 *
			 * @see mshop/product/manager/stock/ansi
			 */

			/** mshop/product/manager/stock/ansi
			 * Updates the rating of the product in the database
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values for the rating to the statement before they are
			 * sent to the database server. The order of the columns must
			 * correspond to the order in the stock() method, so the
			 * correct values are bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for update ratings
			 * @since 2021.10
			 * @category Developer
			 * @see mshop/product/manager/insert/ansi
			 * @see mshop/product/manager/update/ansi
			 * @see mshop/product/manager/newid/ansi
			 * @see mshop/product/manager/delete/ansi
			 * @see mshop/product/manager/search/ansi
			 * @see mshop/product/manager/count/ansi
			 * @see mshop/product/manager/rate/ansi
			 */
			$path = 'mshop/product/manager/stock';

			$stmt = $this->getCachedStatement( $conn, $path, $this->getSqlConfig( $path ) );

			$stmt->bind( 1, $value, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $context->getLocale()->getSiteId() );
			$stmt->bind( 3, (int) $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

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
	 * Adds a new product to the storage.
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Product\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Product\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Product\Item\Iface
	{
		if( !$item->isModified() )
		{
			$item = $this->savePropertyItems( $item, 'product', $fetch );
			return $this->saveListItems( $item, 'product', $fetch );
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
				/** mshop/product/manager/insert/mysql
				 * Inserts a new product record into the database table
				 *
				 * @see mshop/product/manager/insert/ansi
				 */

				/** mshop/product/manager/insert/ansi
				 * Inserts a new product record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the product item to the statement before they are
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
				 * @see mshop/product/manager/update/ansi
				 * @see mshop/product/manager/newid/ansi
				 * @see mshop/product/manager/delete/ansi
				 * @see mshop/product/manager/search/ansi
				 * @see mshop/product/manager/count/ansi
				 * @see mshop/product/manager/rate/ansi
				 * @see mshop/product/manager/stock/ansi
				 */
				$path = 'mshop/product/manager/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** mshop/product/manager/update/mysql
				 * Updates an existing product record in the database
				 *
				 * @see mshop/product/manager/update/ansi
				 */

				/** mshop/product/manager/update/ansi
				 * Updates an existing product record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the product item to the statement before they are
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
				 * @see mshop/product/manager/insert/ansi
				 * @see mshop/product/manager/newid/ansi
				 * @see mshop/product/manager/delete/ansi
				 * @see mshop/product/manager/search/ansi
				 * @see mshop/product/manager/count/ansi
				 * @see mshop/product/manager/rate/ansi
				 * @see mshop/product/manager/stock/ansi
				 */
				$path = 'mshop/product/manager/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getType() );
			$stmt->bind( $idx++, $item->getCode() );
			$stmt->bind( $idx++, $item->getDataset() );
			$stmt->bind( $idx++, $item->getLabel() );
			$stmt->bind( $idx++, $item->getUrl() );
			$stmt->bind( $idx++, $item->inStock(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getScale(), \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT );
			$stmt->bind( $idx++, $item->getDateStart() );
			$stmt->bind( $idx++, $item->getDateEnd() );
			$stmt->bind( $idx++, json_encode( $item->getConfig() ) );
			$stmt->bind( $idx++, $item->getTarget() );
			$stmt->bind( $idx++, $context->getEditor() );
			$stmt->bind( $idx++, $date ); // mtime
			$stmt->bind( $idx++, $item->getTimeCreated() ?: $date );
			$stmt->bind( $idx++, $context->getLocale()->getSiteId() );

			if( $id !== null ) {
				$stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			}

			$stmt->execute()->finish();

			if( $id === null )
			{
				/** mshop/product/manager/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/product/manager/newid/ansi
				 */

				/** mshop/product/manager/newid/ansi
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
				 *  SELECT currval('seq_mpro_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mpro_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/product/manager/insert/ansi
				 * @see mshop/product/manager/update/ansi
				 * @see mshop/product/manager/delete/ansi
				 * @see mshop/product/manager/search/ansi
				 * @see mshop/product/manager/count/ansi
				 * @see mshop/product/manager/rate/ansi
				 * @see mshop/product/manager/stock/ansi
				 */
				$path = 'mshop/product/manager/newid';
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

		$item = $this->savePropertyItems( $item, 'product', $fetch );
		return $this->saveListItems( $item, 'product', $fetch );
	}


	/**
	 * Search for products based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Product\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$map = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'product' );

			/** mshop/product/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole product domain if items from parent sites are inherited,
			 * sites from child sites are aggregated or both.
			 *
			 * Available constants for the site mode are:
			 * * 0 = only items from the current site
			 * * 1 = inherit items from parent sites
			 * * 2 = aggregate items from child sites
			 * * 3 = inherit and aggregate items at the same time
			 *
			 * You also need to set the mode in the locale manager
			 * (mshop/locale/manager/sitelevel) to one of the constants.
			 * If you set it to the same value, it will work as described but you
			 * can also use different modes. For example, if inheritance and
			 * aggregation is configured the locale manager but only inheritance
			 * in the domain manager because aggregating items makes no sense in
			 * this domain, then items wil be only inherited. Thus, you have full
			 * control over inheritance and aggregation in each domain.
			 *
			 * @param int Constant from Aimeos\MShop\Locale\Manager\Base class
			 * @category Developer
			 * @since 2018.01
			 * @see mshop/locale/manager/sitelevel
			 */
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$level = $context->getConfig()->get( 'mshop/product/manager/sitemode', $level );

			/** mshop/product/manager/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/product/manager/search/ansi
			 */

			/** mshop/product/manager/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the product
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
			 * @see mshop/product/manager/insert/ansi
			 * @see mshop/product/manager/update/ansi
			 * @see mshop/product/manager/newid/ansi
			 * @see mshop/product/manager/delete/ansi
			 * @see mshop/product/manager/count/ansi
			 * @see mshop/product/manager/rate/ansi
			 * @see mshop/product/manager/stock/ansi
			 */
			$cfgPathSearch = 'mshop/product/manager/search';

			/** mshop/product/manager/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/product/manager/count/ansi
			 */

			/** mshop/product/manager/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the product
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
			 * @see mshop/product/manager/insert/ansi
			 * @see mshop/product/manager/update/ansi
			 * @see mshop/product/manager/newid/ansi
			 * @see mshop/product/manager/delete/ansi
			 * @see mshop/product/manager/search/ansi
			 * @see mshop/product/manager/rate/ansi
			 * @see mshop/product/manager/stock/ansi
			 */
			$cfgPathCount = 'mshop/product/manager/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null )
			{
				if( ( $row['product.config'] = json_decode( $config = $row['product.config'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_product.config', $row['product.id'], $config );
					$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN, 'core/product' );
				}

				$map[$row['product.id']] = $row;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		$propItems = []; $name = 'product/property';
		if( isset( $ref[$name] ) || in_array( $name, $ref, true ) )
		{
			$propTypes = isset( $ref[$name] ) && is_array( $ref[$name] ) ? $ref[$name] : null;
			$propItems = $this->getPropertyItems( array_keys( $map ), 'product', $propTypes );
		}

		if( isset( $ref['catalog'] ) || in_array( 'catalog', $ref, true ) )
		{
			$domains = isset( $ref['catalog'] ) && is_array( $ref['catalog'] ) ? $ref['catalog'] : [];

			foreach( $this->getDomainRefItems( array_keys( $map ), 'catalog', $domains ) as $prodId => $list ) {
				$map[$prodId]['.catalog'] = $list;
			}
		}

		if( isset( $ref['supplier'] ) || in_array( 'supplier', $ref, true ) )
		{
			$domains = isset( $ref['supplier'] ) && is_array( $ref['supplier'] ) ? $ref['supplier'] : [];

			foreach( $this->getDomainRefItems( array_keys( $map ), 'supplier', $domains ) as $prodId => $list ) {
				$map[$prodId]['.supplier'] = $list;
			}
		}

		if( isset( $ref['stock'] ) || in_array( 'stock', $ref, true ) )
		{
			foreach( $this->getStockItems( array_keys( $map ), $ref ) as $stockId => $stockItem )
			{
				if( isset( $map[$stockItem->getProductId()] ) ) {
					$map[$stockItem->getProductId()]['.stock'][$stockId] = $stockItem;
				}
			}
		}

		return $this->buildItems( $map, $ref, 'product', $propItems );
	}


	/**
	 * Create new product item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propertyItems List of property items
	 * @return \Aimeos\MShop\Product\Item\Iface New product item
	 */
	protected function createItemBase( array $values = [], array $listItems = [],
		array $refItems = [], array $propertyItems = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['.date'] = $this->date;

		return new \Aimeos\MShop\Product\Item\Standard( $values, $listItems, $refItems, $propertyItems );
	}


	/**
	 * Returns the associative list of domain items referencing the product
	 *
	 * @param array $ids List of product IDs
	 * @param string $domain Domain name, e.g. "catalog" or "supplier"
	 * @param array $ref List of referenced items that should be fetched too
	 * @return array Associative list of product IDs as keys and list of domain items as values
	 */
	protected function getDomainRefItems( array $ids, string $domain, array $ref ) : array
	{
		$keys = $map = $result = [];
		$context = $this->getContext();

		foreach( $ids as $id ) {
			$keys[] = 'product|default|' . $id;
		}


		$manager = \Aimeos\MShop::create( $context, $domain . '/lists' );

		$search = $manager->filter( true )->order( $domain . '.lists.position' )->slice( 0, 0x7fffffff );
		$search->setConditions( $search->and( [
			$search->compare( '==', $domain . '.lists.key', $keys ),
			$search->getConditions(),
		] ) );

		foreach( $manager->search( $search ) as $listItem ) {
			$map[$listItem->getParentId()][] = $listItem->getRefId();
		}

		$manager = \Aimeos\MShop::create( $context, $domain );

		$search = $manager->filter( true )->slice( 0, 0x7fffffff );
		$search->setConditions( $search->and( [
			$search->compare( '==', $domain . '.id', array_keys( $map ) ),
			$search->getConditions(),
		] ) );

		$items = $manager->search( $search, $ref );


		foreach( $map as $parentId => $list )
		{
			if( isset( $items[$parentId] ) )
			{
				foreach( $list as $prodId ) {
					$result[$prodId][$parentId] = $items[$parentId];
				}
			}
		}

		return $result;
	}


	/**
	 * Returns the stock items for the given product codes
	 *
	 * @param string[] $ids Unique product codes
	 * @param string[] $ref List of domains to fetch referenced items for
	 * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Stock\Item\Iface as values
	 */
	protected function getStockItems( array $ids, array $ref ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'stock' );

		$search = $manager->filter( true )->slice( 0, 0x7fffffff );
		$expr = [
			$search->compare( '==', 'stock.productid', $ids ),
			$search->getConditions(),
		];

		if( isset( $ref['stock'] ) && is_array( $ref['stock'] ) ) {
			$expr[] = $search->compare( '==', 'stock.type', $ref['stock'] );
		}

		$search->setConditions( $search->and( $expr ) );

		return $manager->search( $search );
	}
}
