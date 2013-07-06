<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Provides common methods required by most of the manager classes.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Manager_Abstract extends MW_Common_Manager_Abstract
{
	private $_context;
	private $_stmts = array();
	private $_keySeparator = '.';


	/**
	 * Initialization of class.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		$this->_context = $context;
	}


	/**
	 * Creates a search object.
	 *
	 * @param boolean $default Add default criteria; Optional
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		$dbm = $this->_context->getDatabaseManager();
		$conn = $dbm->acquire();

		$object = new MW_Common_Criteria_SQL( $conn );

		$dbm->release( $conn );

		return $object;
	}


	/**
	 * Deletes an item from storage.
	 *
	 * @param integer $itemId Unique ID of the item in the storage
	 */
	public function deleteItem( $itemId )
	{
		$this->deleteItems( array( $itemId ) );
	}


	/**
	 * Returns the newly created ID for the last record which was inserted.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection used to insert the new record
	 * @param string $sql SQL statement for retrieving the new ID of the last record which was inserted
	 * @return string ID of the last record that was inserted by using the given connection
	 * @throws MShop_Common_Exception if there's no ID of the last record available
	 */
	protected function _newId( MW_DB_Connection_Interface $conn, $sql )
	{
		$result = $conn->create( $sql )->execute();

		if( ( $row = $result->fetch( MW_DB_Result_Abstract::FETCH_NUM ) ) === false ) {
			throw new MShop_Exception( sprintf( 'ID of last inserted database record not available' ) );
		}
		$result->finish();

		return $row[0];
	}


	/**
	 * Sets the base criteria "status".
	 * (setConditions overwrites the base criteria)
	 *
	 * @param $domain
	 * @return MW_Common_Criteria_Interface
	 */
	protected function _createSearch( $domain )
	{
		$dbm = $this->_context->getDatabaseManager();
		$conn = $dbm->acquire();

		$object = new MW_Common_Criteria_SQL( $conn );
		$object->setConditions( $object->compare( '>', $domain . '.status', 0 ) );

		$dbm->release( $conn );

		return $object;
	}


	/**
	 * Returns a new manager depending on given domain.
	 *
	 * @param string $domain Name of the domain, e.g. text, media, etc.
	 * @return mixed Manager of the given Domain
	 * @throws MShop_Common_Exception if manager couldn't be instanciated
	 */
	protected function _createDomainManager( $domain )
	{
		$domain = strtolower( $domain );
		$parts = explode( '/', $domain );

		if( count( $parts ) < 1 ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid characters in domain name "%1$s"', $domain ) );
		}

		foreach( $parts as $part )
		{
			if( ctype_alnum( $part ) === false ) {
				throw new Controller_ExtJS_Exception( sprintf( 'Invalid characters in domain name "%1$s"', $domain ) );
			}
		}

		$classname = 'MShop_' . ucfirst( array_shift( $parts ) ) . '_Manager_Factory';

		if( class_exists( $classname ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		if( ( $manager = call_user_func_array( $classname . '::createManager', array( $this->_context ) ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Domain manager for class "%1$s" not available', $classname ) );
		}

		foreach( $parts as $part ) {
			$manager = $manager->getSubManager( $part );
		}

		return $manager;
	}


	/**
	 * Returns the cached statement for the given key or creates a new prepared statement.
	 * If no SQL string is given, the key is used to retrieve the SQL string from the configuration.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param string $key Unique key for the SQL
	 * @param string|null $sql SQL string if it shouldn't be retrieved from the configuration
	 */
	protected function _getCachedStatement( MW_DB_Connection_Interface $conn, $key, $sql = null )
	{
		if( !isset( $this->_stmts['stmt'][$key] ) || !isset( $this->_stmts['conn'][$key] ) || $conn !== $this->_stmts['conn'][$key] )
		{
			if( $sql === null ) {
				$sql = $this->_context->getConfig()->get( $key, $key );
			}

			$this->_stmts['stmt'][$key] = $conn->create( $sql );
			$this->_stmts['conn'][$key] = $conn;
		}

		return $this->_stmts['stmt'][$key];
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns a new list manager including the approriate type manager.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string $manager Name of the sub manager type in lower case (can contain a path like base/product)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @param array Associative list of search configuration entries
	 * @param MShop_Common_Manager_Type_Interface $typeManager Type manager object
	 * @return MShop_Common_Manager_List_Interface List manager
	 */
	protected function _getListManager( $domain, $manager, $name, array $searchConfig,
		MShop_Common_Manager_Interface $typeManager )
	{
		$domain = strtolower( $domain );
		$manager = strtolower( $manager );
		$config = $this->_context->getConfig();


		if( empty( $domain ) || ctype_alnum( $domain ) === false ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in domain name "%1$s"', $domain ) );
		}

		if( $name === null ) {
			$name = $config->get( 'classes/' . $domain . '/manager/' . $manager . '/name', 'Default' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in manager name "%1$s"', $name ) );
		}

		$classname = 'MShop_Common_Manager_List_' . $name;
		$interface = 'MShop_Common_Manager_List_Interface';

		if( class_exists( $classname ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$confpath = 'mshop/' . $domain . '/manager/' . $manager . '/' . strtolower( $name ) . '/item/';
		$conf = array(
			'getposmax' => $config->get( $confpath . 'getposmax' ),
			'insert' => $config->get( $confpath . 'insert' ),
			'update' => $config->get( $confpath . 'update' ),
			'updatepos' => $config->get( $confpath . 'updatepos' ),
			'delete' => $config->get( $confpath . 'delete' ),
			'move' => $config->get( $confpath . 'move' ),
			'search' => $config->get( $confpath . 'search' ),
			'count' => $config->get( $confpath . 'count' ),
			'newid' => $config->get( $confpath . 'newid' ),
		);

		$listManager = new $classname( $this->_context, $conf, $searchConfig, $typeManager );

		if( ( $listManager instanceof $interface ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $this->_addManagerDecorators( $listManager, $manager, $domain );
	}


	/**
	 * Returns a new type manager object.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string $manager Name of the sub manager type in lower case (can contain a path like base/product)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @param array Associative list of search configuration entries
	 * @return MShop_Common_Manager_Type_Interface Type manager object
	 */
	protected function _getTypeManager( $domain, $manager, $name, array $searchConfig )
	{
		$domain = strtolower( $domain );
		$manager = strtolower( $manager );
		$config = $this->_context->getConfig();


		if( empty( $domain ) || ctype_alnum( $domain ) === false ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in domain name "%1$s"', $domain ) );
		}

		if( $name === null ) {
			$name = $config->get( 'classes/' . $domain . '/manager/' . $manager . '/name', 'Default' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in manager name "%1$s"', $name ) );
		}

		$classname = 'MShop_Common_Manager_Type_' . $name;
		$interface = 'MShop_Common_Manager_Type_Interface';

		if( class_exists( $classname ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$confpath = 'mshop/' . $domain . '/manager/' . $manager . '/' . strtolower( $name ) . '/item/';
		$conf = array(
			'insert' => $config->get( $confpath . 'insert' ),
			'update' => $config->get( $confpath . 'update' ),
			'delete' => $config->get( $confpath . 'delete' ),
			'search' => $config->get( $confpath . 'search' ),
			'count' => $config->get( $confpath . 'count' ),
			'newid' => $config->get( $confpath . 'newid' ),
		);

		$typeManager = new $classname( $this->_context, $conf, $searchConfig );

		if( ( $typeManager instanceof $interface ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $this->_addManagerDecorators( $typeManager, $manager, $domain );
	}


	/**
	 * Returns a new manager the given extension name.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string $manager Name of the sub manager type in lower case (can contain a path like base/product)
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions
	 */
	protected function _getSubManager( $domain, $manager, $name )
	{
		$domain = strtolower( $domain );
		$manager = strtolower( $manager );


		if( empty( $domain ) || ctype_alnum( $domain ) === false ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in domain name "%1$s"', $domain ) );
		}

		if( $name === null ) {
			$path = 'classes/' . $domain . '/manager/' . $manager . '/name';
			$name = $this->_context->getConfig()->get( $path, 'Default' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in manager name "%1$s"', $name ) );
		}

		$domainname = ucfirst( $domain );
		$subnames = $this->_createSubNames( $manager );

		$classname = 'MShop_'. $domainname . '_Manager_' . $subnames . '_' . $name;
		$interface = 'MShop_'. $domainname . '_Manager_' . $subnames . '_Interface';

		if( class_exists( $classname ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$subManager = new $classname( $this->_context );

		if( ( $subManager instanceof $interface ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $this->_addManagerDecorators( $subManager, $manager, $domain );
	}


	/**
	 * Returns a new address manager object.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string $name Name of the type manager implementation
	 * @param string $confpath Path to configuration array for address manager
	 * @param array $addressSearchConfig List of search configuration entries for address manager
	 * @return MShop_Common_Manager_Address_Interface Address manager object
	 */
	protected function _createAddressManager( $domain, $name, $confpath, array $addressSearchConfig )
	{
		if( $name === null ) {
			$name = $this->_context->getConfig()->get( 'classes/' . $domain . '/manager/address/name', 'Default' );
		}

		if( empty( $name ) || ctype_alnum( $name ) === false ) {
			throw new MShop_Exception( sprintf( 'Invalid characters in manager name "%1$s"', $name ) );
		}

		$classname = 'MShop_Common_Manager_Address_' . $name;
		$interface = 'MShop_Common_Manager_Address_Interface';

		if( class_exists( $classname ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$config = $this->_context->getConfig()->get( $confpath, $confpath );
		$manager = new $classname( $this->_context, $config, $addressSearchConfig );

		if( ( $manager instanceof $interface ) === false ) {
			throw new MShop_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $manager;
	}


	/**
	 * Returns a list of unique criteria names shortend by the last element after the ''
	 *
	 * @param array $prefix Required base prefixes of the search keys
	 * @param MW_Common_Criteria_Expression_Interface|null Criteria object
	 * @return array List of shortend criteria names
	 */
	protected function _getCriteriaKeys( array $prefix, MW_Common_Criteria_Expression_Interface $expr = null )
	{
		if( $expr === null ) { return array(); }

		$result = array();

		foreach( $this->_getCriteriaNames( $expr ) as $item )
		{
			if( ( $pos = strpos( $item, '(' ) ) !== false ) {
				$item = substr( $item, 0, $pos );
			}

			if( ( $pos = strpos( $item, ':' ) ) !== false ) {
				$item = substr( $item, $pos + 1 );
			}

			$result = array_merge( $result, $this->_cutNameTail( $prefix, $item ) );
		}

		return $result;
	}


	/**
	 * Cuts the last part separated by a dot repeatedly and returns the list of resulting string.
	 *
	 * @param array $prefix Required base prefixes of the search keys
	 * @param string $string String containing parts separated by dots
	 * @return array List of resulting strings
	 */
	private function _cutNameTail( array $prefix, $string )
	{
		$result = array();
		$noprefix = true;
		$strlen = strlen( $string );
		$sep = $this->_getKeySeparator();

		foreach( $prefix as $key )
		{
			$len = strlen( $key );

			if( strncmp( $string, $key, $len ) === 0 )
			{
				if( $strlen > $len && ( $pos = strrpos( $string, $sep ) ) !== false )
				{
					$result[] = $string = substr( $string, 0, $pos );
					$result = array_merge( $result, $this->_cutNameTail( $prefix, $string ) );
				}

				$noprefix = false;
				break;
			}
		}

		if( $noprefix )
		{
			if( ( $pos = strrpos( $string, $sep ) ) !== false ) {
				$result[] = $string = substr( $string, 0, $pos );
			} else {
				$result[] = $string;
			}
		}

		return $result;
	}


	/**
	 * Adds the decorators to the manager object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param MShop_Common_Manager_Interface $manager Manager object
	 * @param string $classprefix Decorator class prefix, e.g. "MShop_Product_Manager_Decorator_"
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	protected function _addDecorators( MShop_Context_Item_Interface $context,
		MShop_Common_Manager_Interface $manager, array $decorators, $classprefix )
	{
		$iface = 'MShop_Common_Manager_Decorator_Interface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new MShop_Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$manager =  new $classname( $context, $manager );

			if( !( $manager instanceof $iface ) ) {
				throw new MShop_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}
		}

		return $manager;
	}


	/**
	 * Adds the configured decorators to the given manager object.
	 *
	 * @param MShop_Common_Manager_Interface $manager Manager object
	 * @param string $managerpath Manager sub-names separated by slashes, e.g. "list/type"
	 * @param string $domain Domain name in lower case, e.g. "product"
	 */
	protected function _addManagerDecorators( MShop_Common_Manager_Interface $manager, $managerpath, $domain )
	{
		$config = $this->_context->getConfig();

		$decorators = $config->get( 'mshop/common/manager/decorators/default', array() );
		$excludes = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[ $key ] );
			}
		}

		$classprefix = 'MShop_Common_Manager_Decorator_';
		$manager =  $this->_addDecorators( $this->_context, $manager,
			$decorators, $classprefix );

		$classprefix = 'MShop_Common_Manager_Decorator_';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/global', array() );
		$manager =  $this->_addDecorators( $this->_context, $manager,
			$decorators, $classprefix );

		$subpath = $this->_createSubNames( $managerpath );
		$classprefix = 'MShop_'. ucfirst( $domain ) . '_Manager_' . $subpath . '_Decorator_';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/' . $managerpath . '/decorators/local', array() );
		$manager =  $this->_addDecorators( $this->_context, $manager,
			$decorators, $classprefix );

		return $manager;
	}


	/**
	 * Transforms the manager path to the appropriate class names.
	 *
	 * @param string $manager Path of manager names, e.g. "list/type"
	 * @return string Class names, e.g. "List_Type"
	 */
	protected function _createSubNames( $manager )
	{
		$names = explode( '/', $manager );

		foreach( $names as $key => $subname )
		{
			if( empty( $subname ) || ctype_alnum( $subname ) === false ) {
				throw new MShop_Exception( sprintf( 'Invalid characters in manager name "%1$s"', $manager ) );
			}

			$names[$key] = ucfirst( $subname );
		}

		return implode( '_', $names );
	}


	/**
	 * Returns a list of criteria names from a expression and its sub-expressions.
	 *
	 * @param MW_Common_Criteria_Expression_Interface Criteria object
	 * @return array List of criteria names
	 */
	private function _getCriteriaNames( MW_Common_Criteria_Expression_Interface $expr )
	{
		if( $expr instanceof MW_Common_Criteria_Expression_Compare_Interface ) {
			return array( $expr->getName() );
		}

		if( $expr instanceof MW_Common_Criteria_Expression_Combine_Interface )
		{
			$list = array();
			foreach( $expr->getExpressions() as $item ) {
				$list = array_merge( $list, $this->_getCriteriaNames( $item ) );
			}
			return $list;
		}

		if( $expr instanceof MW_Common_Criteria_Expression_Sort_Interface ) {
			return array( $expr->getName() );
		}

		return array();
	}


	/**
	 * Creates the items with address item, list items and referenced items.
	 *
	 * @param array $map Associative list of IDs as keys and the associative array of values
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param string $prefix Domain prefix
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	protected function _buildItems( array $map, array $domains, $prefix )
	{
		$items = $listItemMap = $refItemMap = $refIdMap = array();

		if( count( $domains ) > 0 )
		{
			$listItems = $this->_getListItems( array_keys( $map ), $domains, $prefix );

			foreach( $listItems as $listItem )
			{
				$domain = $listItem->getDomain();
				$parentid = $listItem->getParentId();

				$listItemMap[ $parentid ][ $domain ][ $listItem->getId() ] = $listItem;
				$refIdMap[ $domain ][ $listItem->getRefId() ][] = $parentid;
			}

			$refItemMap = $this->_getRefItems( $refIdMap );
		}

		foreach ( $map as $id => $values )
		{
			$listItems = array();
			if ( isset( $listItemMap[$id] ) ) {
				$listItems = $listItemMap[$id];
			}

			$refItems = array();
			if ( isset( $refItemMap[$id] ) ) {
				$refItems = $refItemMap[$id];
			}

			$items[ $id ] = $this->_createItem( $values, $listItems, $refItems );
		}

		return $items;
	}


	/**
	 * Returns the list items that belong to the given IDs.
	 *
	 * @param array $ids List of IDs
	 * @param array $domains List of domain names whose referenced items should be attached
	 * @param string $prefix Domain prefix
	 * @return array List of items implementing MShop_Common_List_Item_Interface
	 */
	protected function _getListItems( array $ids, array $domains, $prefix )
	{
		$manager = $this->getSubManager('list');

		$search = $manager->createSearch( true );

		$expr[] = $search->compare( '==', $prefix . '.list.parentid', $ids );
		$expr[] = $search->compare( '==', $prefix . '.list.domain', $domains );
		$expr[] = $search->getConditions();

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		return $manager->searchItems( $search );
	}


	/**
	 * Returns the referenced items for the given IDs.
	 *
	 * @param array $textIdMap Associative list of domain/ref-ID/parent-item-ID key/value pairs
	 * @return array Associative list of parent-item-ID/domain/items key/value pairs
	 */
	protected function _getRefItems( array $refIdMap )
	{
		$items = array();

		foreach( $refIdMap as $domain => $list )
		{
			try
			{
				$manager = $this->_createDomainManager( $domain );

				$search = $manager->createSearch( true );
				$expr = array(
					$search->compare( '==', str_replace( '/', '.', $domain ) . '.id', array_keys( $list ) ),
					$search->getConditions(),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );
				$search->setSlice( 0, 0x7fffffff );

				foreach( $manager->searchItems( $search ) as $id => $item )
				{
					foreach( $list[ $id ] as $parentId ) {
						$items[ $parentId ][ $domain ][ $id ] = $item;
					}
				}
			}
			catch( MShop_Exception $e )
			{
				$logger = $this->_context->getLogger();
				$logger->log( sprintf( 'Item referenced in domain "%1$s" not found: %2$s', $domain, $e->getMessage() ) );
				$logger->log( $e->getTraceAsString() );
			}
		}

		return $items;
	}


	/**
	 * Returns the item for the given search key and ID.
	 *
	 * @param string $key Search key for the requested ID
	 * @param integer $id Unique ID to search for
	 * @return MShop_Common_Item_Interface Requested item
	 * @throws MShop_Exception if no item with the given ID found
	 */
	protected function _getItem( $key, $id, array $ref = array() )
	{
		$criteria = $this->createSearch();
		$criteria->setConditions( $criteria->compare( '==', $key, $id ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Item with ID "%2$s" in "%1$s" not found', $key, $id ) );
		}

		return $item;
	}


	/**
	 * Returns the used separator inside the search keys.
	 *
	 * @return string Separator string (default: ".")
	 */
	protected function _getKeySeparator()
	{
		return $this->_keySeparator;
	}


	/**
	 * Replaces ":site" marker in a search config item array.
	 *
	 * @param array &$searchAttr Single search config definition including the "internalcode" key
	 * @param string $column Name (including alias) of the column containing the site ID in the storage
	 * @param integer|array $value Site ID or list of site IDs
	 * @param string $marker Marker to replace
	 */
	protected function _replaceSiteMarker( &$searchAttr, $column, $value, $marker = ':site' )
	{
		$types = array( 'siteid' => MW_DB_Statement_Abstract::PARAM_INT );
		$translations = array( 'siteid' => $column );

		$search = $this->createSearch();
		$expr = $search->compare( '==', 'siteid', $value );
		$string = $expr->toString( $types, $translations );

		$searchAttr['internalcode'] = str_replace( $marker, $string, $searchAttr['internalcode'] );
	}


	/**
	 * Returns the search result of the statement combined with the given criteria.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param string $cfgPathSearch Path to SQL statement in configuration for searching
	 * @param string $cfgPathCount Path to SQL statement in configuration for counting
	 * @param array $required Additional search keys to add conditions for even if no conditions are available
	 * @param integer|null $total Contains the number of all records matching the criteria if not null
	 * @param integer $sitelevel Constant from MShop_Locale_Manager_Abstract for defining which site IDs should be used for searching
	 * @return MW_DB_Result_Interface SQL result object for accessing the found records
	 * @throws MShop_Exception if no number of all matching records is available
	 */
	protected function _searchItems( MW_DB_Connection_Interface $conn, MW_Common_Criteria_Interface $search,
		$cfgPathSearch, $cfgPathCount, array $required, &$total = null,
		$sitelevel = MShop_Locale_Manager_Abstract::SITE_ONE, array $plugins = array() )
	{
		$joins = array();
		$conditions = $search->getConditions();
		$attributes = $this->getSearchAttributes();
		$iface = 'MW_Common_Criteria_Attribute_Interface';


		$locale = $this->_context->getLocale();
		$siteIds = array( $locale->getSiteId() );

		if( $sitelevel & MShop_Locale_Manager_Abstract::SITE_PATH ) {
			$siteIds = array_merge( $siteIds, $locale->getSitePath() );
		}

		if( $sitelevel & MShop_Locale_Manager_Abstract::SITE_SUBTREE ) {
			$siteIds = array_merge( $siteIds, $locale->getSiteSubTree() );
		}

		$siteIds = array_unique( $siteIds );


		$keys = array_merge( $required, $this->_getCriteriaKeys( $required, $conditions ) );

		foreach( $search->getSortations() as $sortation ) {
			$keys = array_merge( $keys, $this->_getCriteriaKeys( $required, $sortation ) );
		}

		$sep = $this->_getKeySeparator();
		$basekey = array_shift( $required );
		$keys = array_unique( array_merge( $required, $keys ) );
		sort( $keys );

		foreach( $keys as $key )
		{
			if( $key !== $basekey )
			{
				$name = $key . $sep . 'id';

				if( isset( $attributes[$name] ) && $attributes[$name] instanceof $iface ) {
					$joins = array_merge( $joins, $attributes[$name]->getInternalDeps() );
				}
				else if( isset( $attributes['id'] ) && $attributes['id'] instanceof $iface ) {
					$joins = array_merge( $joins, $attributes['id']->getInternalDeps() );
				}
			}

			$name = $key . $sep . 'siteid';

			if( isset( $attributes[$name] ) ) {
				$cond[] = $search->compare( '==', $name, $siteIds );
			}
		}

		if( $conditions !== null ) {
			$cond[] = $conditions;
		}

		$search = clone $search;
		$search->setConditions( $search->combine( '&&', $cond ) );


		$types = $this->_getSearchTypes( $attributes );
		$translations = $this->_getSearchTranslations( $attributes );

		$find = array( ':joins', ':cond', ':start', ':size' );
		$replace = array(
			implode( "\n", array_unique( $joins ) ),
			$search->getConditionString( $types, $translations, $plugins ),
			$search->getSliceStart(),
			$search->getSliceSize(),
		);

		if( count( $search->getSortations() ) > 0 )
		{
			$keys[] = 'orderby';
			$find[] = ':order';
			$replace[] = $search->getSortationString( $types, $translations );
		}


		if( $total !== null )
		{
			$sql = new MW_Template_SQL( $this->_context->getConfig()->get( $cfgPathCount, $cfgPathCount ) );
			$sql->replace( $find, $replace )->enable( $keys );

			$time = microtime( true );
			$stmt = $conn->create( $sql->str() );
			$results = $stmt->execute();
			$row = $results->fetch();
			$results->finish();
			$this->_context->getLogger()->log( __METHOD__ . '(' . ( ( microtime( true ) - $time ) * 1000 ) . 'ms): SQL statement: ' . $stmt, MW_Logger_Abstract::DEBUG );

			if ( $row === false ) {
				throw new MShop_Exception( sprintf( 'Total results value not found' ) );
			}

			$total = (int) $row['count'];
		}


		$sql = new MW_Template_SQL( $this->_context->getConfig()->get( $cfgPathSearch, $cfgPathSearch ) );
		$sql->replace( $find, $replace )->enable( $keys );

		$time = microtime( true );
		$stmt = $conn->create( $sql->str() );
		$results = $stmt->execute();
		$this->_context->getLogger()->log( __METHOD__ . '(' . ( ( microtime( true ) - $time ) * 1000 ) . 'ms): SQL statement: ' . $stmt, MW_Logger_Abstract::DEBUG );

		return $results;
	}


	/**
	 * Deletes items specified by ids in array.
	 *
	 * @param array $ids List of IDs
	 * @param string $sql Sql statement
	 * @param boolean $siteidcheck If siteid is used in the statement
	 */
	protected function _deleteItems( array $ids, $sql, $siteidcheck = true )
	{
		if( empty( $ids ) ) { return; }

		$context = $this->_getContext();

		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', 'id', $ids ) );

		$types = array( 'id' => MW_DB_Statement_Abstract::PARAM_STR );
		$translations = array( 'id' => '"id"' );

		$cond = $search->getConditionString( $types, $translations );
		$sql = str_replace( ':cond', $cond, $sql );


		try
		{
			$dbm = $context->getDatabaseManager();
			$conn = $dbm->acquire();

			$stmt = $conn->create( $sql );

			if( $siteidcheck ) {
				$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			}

			$stmt->execute()->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Starts a database transaction on the connection identified by the given name.
	 *
	 * @param string $name Connection name as named in the resource file
	 */
	protected function _begin( $name = 'db' )
	{
		$dbm = $this->_context->getDatabaseManager();

		$conn = $dbm->acquire( $name );
		$conn->begin();
		$dbm->release( $conn, $name );
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name.
	 *
	 * @param string $name Connection name as named in the resource file
	 */
	protected function _commit( $name = 'db' )
	{
		$dbm = $this->_context->getDatabaseManager();

		$conn = $dbm->acquire( $name );
		$conn->commit();
		$dbm->release( $conn, $name );
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name.
	 *
	 * @param string $name Connection name as named in the resource file
	 */
	protected function _rollback( $name = 'db' )
	{
		$dbm = $this->_context->getDatabaseManager();

		$conn = $dbm->acquire( $name );
		$conn->rollback();
		$dbm->release( $conn, $name );
	}
}
