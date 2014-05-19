<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Abstract type manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Manager_Type_Abstract
	extends MShop_Common_Manager_Abstract
	implements MShop_Common_Manager_Type_Interface
{
	private $_prefix;
	private $_context;
	private $_config;
	private $_searchConfig;


	/**
	 * Creates the type manager using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 *
	 * @throws MShop_Exception if no configuration is available
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		$conf = $context->getConfig();
		$confpath = $this->_getConfigPath();
		$this->_config = array(
			'insert' => $conf->get( $confpath . 'insert' ),
			'update' => $conf->get( $confpath . 'update' ),
			'delete' => $conf->get( $confpath . 'delete' ),
			'search' => $conf->get( $confpath . 'search' ),
			'count' => $conf->get( $confpath . 'count' ),
			'newid' => $conf->get( $confpath . 'newid' ),
		);

		$this->_searchConfig = $this->_getSearchConfig();

		$required = array( 'count', 'delete', 'insert', 'newid', 'search', 'update' );
		$isList = array_keys( $this->_config );

		foreach( $required as $key )
		{
			if( !in_array( $key, $isList ) ) {
				throw new MShop_Exception( sprintf( 'Configuration of necessary SQL statement for "%1$s" not available', $key ) );
			}
		}

		parent::__construct( $context );

		$this->_context = $context;

		if( ( $entry = reset( $this->_searchConfig ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration not available' ) );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) == false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code']) );
		}

		if( ( $this->_prefix = substr( $entry['code'], 0, $pos+1 ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}
	}


	/**
	 * Creates new type item object.
	 *
	 * @return MShop_Common_Item_Type_Interface New type item object
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_context->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->_createSearch( substr( $this->_prefix, 0, strlen( $this->_prefix ) - 1 ) );
		}

		return parent::createSearch();
	}


	/**
	 * Adds or updates a type item object.
	 *
	 * @param MShop_Common_Item_Type_Interface $item Type item object which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Common_Item_Type_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( $item->isModified() === false ) { return; }

		$dbm = $this->_context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			if( $id === null ) {
				$sql = $this->_config['insert'];
			} else {
				$sql = $this->_config['update'];
			}

			$statement = $conn->create( $sql );

			$statement->bind( 1, $this->_context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 2, $item->getCode(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 3, $item->getDomain(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 4, $item->getLabel(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 5, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 6, date('Y-m-d H:i:s', time()));//mtime
			$statement->bind( 7, $this->_context->getEditor());

			if( $id !== null ) {
				$statement->bind( 8, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$statement->bind( 8, date('Y-m-d H:i:s', time()));//ctime
			}

			$result = $statement->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$item->setId( $this->_newId( $conn, $this->_config['newid'] ) );
				} else {
					$item->setId( $id ); // modified false
				}
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
		$this->_deleteItems( $ids, $this->_config['delete'] );
	}


	/**
	 * Returns the type item specified by its ID
	 *
	 * @param integer $id Id of type item object
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Common_Item_Type_Interface Returns the type item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		$conf = reset( $this->_searchConfig );

		$criteria = $this->createSearch();
		$criteria->setConditions( $criteria->compare( '==', $conf['code'], $id ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Type item with ID "%1$s" in "%2$s" not found', $id, $conf['code'] ) );
		}

		return $item;
	}


	/**
	 * Searches for all type items matching the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of type items implementing MShop_Common_Item_Type_Interface
	 * @throws MShop_Common_Exception if creating items failed
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = array();

		$dbm = $this->_context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$domain = explode( '.', $this->_prefix);

			if ( ( $topdomain = array_shift($domain) ) === null ) {
				throw new MShop_Exception('No configuration available.');
			}

			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = $this->_config['search'];
			$cfgPathCount =  $this->_config['count'];
			$required = array( trim( $this->_prefix, '.' ) );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$items[ $row['id'] ] = $this->_createItem( $row );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		return $list;
	}


	/**
	 * Creates a new manager for type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'common', 'type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	abstract protected function _getConfigPath();


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	abstract protected function _getSearchConfig();


	/**
	 * Creates new type item object.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @return MShop_Common_Item_Type_Default New type item object
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Common_Item_Type_Default( $this->_prefix, $values );
	}


	protected function _getPrefix()
	{
		return $this->_prefix;
	}
}
