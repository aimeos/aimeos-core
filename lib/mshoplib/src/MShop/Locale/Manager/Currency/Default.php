<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 */


/**
 * Default implementation for managing currencies.
 *
 * @package MShop
 * @subpackage Locale
 */
class MShop_Locale_Manager_Currency_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Locale_Manager_Currency_Interface
{
	private $_searchConfig = array(
		'locale.currency.id' => array(
			'code' => 'locale.currency.id',
			'internalcode' => 'mloccu."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_locale_currency" AS mloccu ON (mloc."currencyid" = mloccu."id")' ),
			'label' => 'Locale currency ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
			'public' => false,
		),
		'locale.currency.siteid' => array(
			'code' => 'locale.currency.siteid',
			'internalcode' => 'mloccu."siteid"',
			'label' => 'Locale currency site ID',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'locale.currency.label' => array(
			'code' => 'locale.currency.label',
			'internalcode' => 'mloccu."label"',
			'label' => 'Locale currency label',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'locale.currency.code' => array(
			'code' => 'locale.currency.code',
			'internalcode' => 'mloccu."id"',
			'label' => 'Locale currency code',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'locale.currency.status' => array(
			'code' => 'locale.currency.status',
			'internalcode' => 'mloccu."status"',
			'label' => 'Locale currency status',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'locale.currency.ctime'=> array(
			'code'=>'locale.currency.ctime',
			'internalcode'=>'mloccu."ctime"',
			'label'=>'Locale currency create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'locale.currency.mtime'=> array(
			'code'=>'locale.currency.mtime',
			'internalcode'=>'mloccu."mtime"',
			'label'=>'Locale currency modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
		),
		'locale.currency.editor'=> array(
			'code'=>'locale.currency.editor',
			'internalcode'=>'mloccu."editor"',
			'label'=>'Locale currency editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR
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
		$this->_setResourceName( 'db-locale' );
	}


	/**
	 * Creates new currency object.
	 *
	 * @return MShop_Locale_Item_Currency_Interface
	 * @throws MShop_Locale_Exception On failures with the language item object
	 */
	public function createItem()
	{
		try{
			$values = array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() );
		} catch (Exception $ex) {
			$values = array( 'siteid' => null );
		}

		return $this->_createItem($values);
	}


	/**
	 * Saves a currency item to the storage.
	 *
	 * @param MShop_Common_Item_Interface $currency Currency item to save in the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 *
	 * @throws MW_DB_Exception If currency object couldn't be saved
	 * @throws MShop_Locale_Exception If failures with currency item object
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Locale_Item_Currency_Interface';
		if ( !( $item instanceof $iface ) ) {
			throw new MShop_Locale_Exception(sprintf('Object is not of required type "%1$s"', $iface));
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			$path = 'mshop/locale/manager/currency/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind(1, $item->getLabel(), MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->bind(2, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(3, $item->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(4, date( 'Y-m-d H:i:s', time() ) ); // mtime
			$stmt->bind(5, $context->getEditor() );
			// bind ID but code and id are identical after saveing the stuff
			// id is the flag to detect updates or inserts!
			$stmt->bind(6, $item->getCode(), MW_DB_Statement_Abstract::PARAM_STR);

			if ( $id === null ) {
				$stmt->bind(7, date( 'Y-m-d H:i:s', time() ) ); // ctime
			}

			$stmt->execute()->finish();

			$item->setId($item->getCode()); // set modified flag to false

			$dbm->release( $conn, $dbname );
		}
		catch ( Exception $e )
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
		$path = 'mshop/locale/manager/currency/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the currency object with the given currency ID.
	 *
	 * @param string $id Currency ID indentifying the currency object
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Locale_Item_Currency_Interface Returns the currency item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'locale.currency.id', $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/locale/manager/language/submanagers
		 * List of manager names that can be instantiated by the locale currency manager
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
		$path = 'classes/locale/manager/currency/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Search for currency items.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object
	 * @param integer &$total Number of items that are available in total
	 *
	 * @return array List of items implementing MShop_Locale_Item_Currency_Interface
	 *
	 * @throws MW_DB_Exception On failures with the db object
	 * @throws MShop_Common_Exception On failures with the MW_Common_Criteria_ object
	 * @throws MShop_Locale_Exception On failures with the currency item object
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = array( );

		try
		{
			$attributes = $this->getSearchAttributes();
			$types = $this->_getSearchTypes($attributes);
			$translations = $this->_getSearchTranslations($attributes);

			$find = array( ':cond', ':order', ':start', ':size' );
			$replace = array(
				$search->getConditionString($types, $translations),
				$search->getSortationString($types, $translations),
				$search->getSliceStart(),
				$search->getSliceSize(),
			);

			$path = 'mshop/locale/manager/currency/default/item/search';
			$sql = $config->get($path, $path);
			$results = $this->_getSearchResults($conn, str_replace($find, $replace, $sql));

			try
			{
				while ( ($row = $results->fetch()) !== false ) {
					$items[ $row['id'] ] = $this->_createItem($row);
				}
			}
			catch ( Exception $e )
			{
				$results->finish();
				throw $e;
			}

			if ( $total !== null )
			{
				$path = 'mshop/locale/manager/currency/default/item/count';
				$sql = $config->get($path, $path);
				$results = $this->_getSearchResults($conn, str_replace($find, $replace, $sql));

				$row = $results->fetch();
				$results->finish();

				if ( $row === false ) {
					throw new MShop_Locale_Exception('No total results value found.');
				}

				$total = $row['count'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch ( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Locale_Manager_Interface manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/locale/manager/currency/name
		 * Class name of the used locale currency manager implementation
		 *
		 * Each default locale currency manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Locale_Manager_Currency_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Locale_Manager_Currency_Mycurrency
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/locale/manager/currency/name = Mycurrency
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCurrency"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/locale/manager/currency/decorators/excludes
		 * Excludes decorators added by the "common" option from the locale currency manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the locale currency manager.
		 *
		 *  mshop/locale/manager/currency/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the locale currency manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/locale/manager/currency/decorators/global
		 * @see mshop/locale/manager/currency/decorators/local
		 */

		/** mshop/locale/manager/currency/decorators/global
		 * Adds a list of globally available decorators only to the locale currency manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the locale currency manager.
		 *
		 *  mshop/locale/manager/currency/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the locale controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/locale/manager/currency/decorators/excludes
		 * @see mshop/locale/manager/currency/decorators/local
		 */

		/** mshop/locale/manager/currency/decorators/local
		 * Adds a list of local decorators only to the locale currency manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the locale currency manager.
		 *
		 *  mshop/locale/manager/currency/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the locale
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/locale/manager/currency/decorators/excludes
		 * @see mshop/locale/manager/currency/decorators/global
		 */

		return $this->_getSubManager( 'locale', 'currency/' . $manager, $name );
	}


	/**
	 * Creates a search object and sets base criteria.
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		if ( $default === true ) {
			return parent::_createSearch('locale.currency');
		}

		return parent::createSearch();
	}


	/**
	 * Returns the search results for the given SQL statement.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param $sql SQL statement
	 * @return MW_DB_Result_Interface Search result object
	 */
	protected function _getSearchResults( MW_DB_Connection_Interface $conn, $sql )
	{
		$statement = $conn->create($sql);
		$this->_getContext()->getLogger()->log(__METHOD__ . ': SQL statement: ' . $statement, MW_Logger_Abstract::DEBUG);

		$results = $statement->execute();

		return $results;
	}


	/**
	 * Create new item object initialized with given parameters.
	 * @return MShop_Locale_Item_Currency_Interface
	 * @throws MShop_Locale_Exception On failures with the language item object
	 */
	protected function _createItem( array $data=array( ) )
	{
		return new MShop_Locale_Item_Currency_Default($data);
	}
}
