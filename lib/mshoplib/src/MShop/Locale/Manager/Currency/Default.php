<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 * @version $Id: Default.php 14854 2012-01-13 12:54:14Z doleiynyk $
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
			throw new MShop_Locale_Exception(sprintf('Object does not implement "%1$s"', $iface));
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		$config = $context->getConfig();

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

			$dbm->release($conn);
		}
		catch ( Exception $e )
		{
			$dbm->release($conn);
			throw $e;
		}
	}


	/**
	 * Deletes a currency.
	 *
	 * @param string $currencyId Currency ID of an existing currency in the storage which should be deleted
	 * @throws MShop_Locale_Exception
	 */
	public function deleteItem( $currencyId )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$stmt = $this->_getCachedStatement($conn, 'mshop/locale/manager/currency/default/item/delete');
			$stmt->bind(1, $currencyId, MW_DB_Statement_Abstract::PARAM_STR);
			$stmt->execute()->finish();

			$dbm->release($conn);
		}
		catch ( Exception $e )
		{
			$dbm->release($conn);
			throw $e;
		}
	}


	/**
	 * Returns the currency object with the given currency ID.
	 *
	 * @param string $id Currency ID indentifying the currency object
	 * @return MShop_Locale_Item_Currency_Interface Currency object
	 * @throws MShop_Locale_Exception If no currency object was found
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
		$list = array( );

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default($fields);
		}

		if ( $withsub === true ) {
			foreach ( $this->_getContext()->getConfig()->get('classes/locale/manager/currency/submanagers', array( )) as $domain ) {
				$list = array_merge($list, $this->getSubManager($domain)->getSearchAttributes());
			}
		}

		return $list;
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
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

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
			$sql = $context->getConfig()->get($path, $path);
			$results = $this->_getSearchResults($conn, str_replace($find, $replace, $sql));

			try {
				while ( ($row = $results->fetch()) !== false ) {
					$items[ $row['id'] ] = $this->_createItem($row);
				}
			} catch ( Exception $e ) {
				$results->finish();
				throw $e;
			}

			if ( $total !== null ) {
				$path = 'mshop/locale/manager/currency/default/item/count';
				$sql = $context->getConfig()->get($path, $path);
				$results = $this->_getSearchResults($conn, str_replace($find, $replace, $sql));

				$row = $results->fetch();
				$results->finish();

				if ( $row === false ) {
					throw new MShop_Locale_Exception('No total results value found.');
				}

				$total = $row['count'];
			}

			$dbm->release($conn);
		}
		catch ( Exception $e )
		{
			$dbm->release($conn);
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
