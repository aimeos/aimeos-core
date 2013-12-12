<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Default list manager implementation
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Manager_List_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Common_Manager_List_Interface
{
	private $_prefix;
	private $_config;
	private $_searchConfig;
	private $_typeManager;


	/**
	 * Creates the common list manager using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param array $config array with SQL statements
	 * @param array $searchConfig array with search configuration
	 * @param MShop_Common_Manager_Type_Interface $typeManager Common type manager
	 *
	 * @throws MShop_Common_Exception if no configuration is available
	 */
	public function __construct( MShop_Context_Item_Interface $context,
		array $config = array(), array $searchConfig = array(), $typeManager = null )
	{
		$whitelistItem = array( 'insert', 'update', 'delete', 'move', 'search', 'count', 'newid', 'updatepos', 'getposmax' );
		$isList = array_keys( $config );

		foreach($whitelistItem as $str)
		{
			if ( !in_array($str, $isList ) ) {
				throw new MShop_Exception( sprintf( 'Configuration of necessary SQL statement for "%1$s" not available', $str ) );
			}
		}

		if( ( $entry = reset( $searchConfig ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration not available' ) );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) == false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}

		if( ( $this->_prefix = substr( $entry['code'], 0, $pos+1 ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}

		parent::__construct( $context );

		$this->_config = $config;
		$this->_searchConfig = $searchConfig;
		$this->_typeManager = $typeManager;
	}


	/**
	 * Creates new common list item object.
	 *
	 * @return MShop_Common_Item_List_Interface New list item object
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);

	}


	/**
	 * Updates or adds a common list item object.
	 *
	 * @param MShop_Common_Item_List_Interface $item List item object which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Common_Item_List_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		$config = $this->_getContext()->getConfig();
		$locale = $this->_getContext()->getLocale();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$id = $item->getId();

			if( $id === null ) {
				$sql = $this->_config['insert'];
				$path = $this->_prefix . 'insert';
			} else {
				$sql = $this->_config['update'];
				$path = $this->_prefix . 'update';
			}

			$time = date( 'Y-m-d H:i:s' );
			$statement = $this->_getCachedStatement($conn, $path, $sql);

			$statement->bind( 1, $item->getParentId(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 2, $locale->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 3, $item->getTypeId(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 4, $item->getDomain(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 5, $item->getRefId(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 6, $item->getDateStart(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 7, $item->getDateEnd(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 8, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 9, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );

			$statement->bind( 10, $time);//mtime
			$statement->bind( 11, $this->_getContext()->getEditor());


			if( $id !== null ) {
				$statement->bind( 12, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$statement->bind( 12, $time ); //ctime
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

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
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
	 * Creates common list item object for the given common list item id.
	 *
	 * @param integer $id Id of common list item object
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Common_Item_List_Interface Returns common list item object of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		if( ( $conf = reset( $this->_searchConfig ) ) === false || !isset( $conf['code'] ) ) {
			throw new MShop_Exception( sprintf( 'Search configuration not available' ) );
		}

		$criteria = $this->createSearch();
		$criteria->setConditions( $criteria->compare( '==', $conf['code'], $id ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false )
		{
			$msg = sprintf( 'List item with ID "%2$s" in "%1$s" not found', $conf['code'], $id );
			throw new MShop_Exception( $msg );
		}

		return $item;
	}


	/**
	 * Moves the common list item object with Id in the list of Id's before the
	 * common list item object with reference Id of the given node.
	 *
	 * @param integer $id Id of the item which should be moved
	 * @param integer|null $ref Id where the given Id should be inserted before (null for appending)
	 */
	public function moveItem( $id, $ref = null )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$siteid = $context->getLocale()->getSiteId();

		try
		{
			$listItem = $this->getItem( $id );

			$newpos = 0;
			$oldpos = $listItem->getPosition();
			$parentid = $listItem->getParentId();
			$typeid = $listItem->getTypeId();
			$domain = $listItem->getDomain();

			if( !is_null( $ref ) ) {
				$refListItem = $this->getItem( $ref );
			}

			$conn = $dbm->acquire();

			if( !is_null( $ref ) )
			{
				$newpos = $refListItem->getPosition();

				$sql = $this->_config['move'];

				$stmt = $conn->create( $sql );
				$stmt->bind( 1, +1, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 2, date('Y-m-d H:i:s', time()));//mtime
				$stmt->bind( 3, $this->_getContext()->getEditor());
				$stmt->bind( 4, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 5, $parentid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 6, $typeid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 7, $domain, MW_DB_Statement_Abstract::PARAM_STR );
				$stmt->bind( 8, $refListItem->getPosition(), MW_DB_Statement_Abstract::PARAM_INT );

				$result = $stmt->execute()->finish();
			}
			else
			{
				$sql = $this->_config['getposmax'];

				$statement = $conn->create( $sql );

				$statement->bind( 1, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
				$statement->bind( 2, $parentid, MW_DB_Statement_Abstract::PARAM_INT );
				$statement->bind( 3, $typeid, MW_DB_Statement_Abstract::PARAM_INT );
				$statement->bind( 4, $domain, MW_DB_Statement_Abstract::PARAM_STR );

				$result = $statement->execute();
				$row = $result->fetch();
				$result->finish();

				if ( $row !== false ) {
					$newpos = $row['pos'] + 1;
				}
			}

			$sql = $this->_config['updatepos'];

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $newpos, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, date('Y-m-d H:i:s', time())); // mtime
			$stmt->bind( 3, $this->_getContext()->getEditor());
			$stmt->bind( 4, $id, MW_DB_Statement_Abstract::PARAM_INT );

			$result = $stmt->execute()->finish();

			if ( $oldpos > $newpos ) {
				$oldpos++;
			}

			$sql = $this->_config['move'];

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, -1, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, date('Y-m-d H:i:s', time())); // mtime
			$stmt->bind( 3, $this->_getContext()->getEditor());
			$stmt->bind( 4, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, $parentid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 6, $typeid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $domain, MW_DB_Statement_Abstract::PARAM_STR );
			$stmt->bind( 8, $oldpos, MW_DB_Statement_Abstract::PARAM_INT );

			$result = $stmt->execute()->finish();

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true ) {
			$list = array_merge( $list, $this->getSubManager( 'type' )->getSearchAttributes() );
		}

		return $list;
	}


	/**
	 * Search for all text items based on the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param integer &$total Number of items that are available in total
	 * @return array List of list items implementing MShop_Common_Item_List_Interface
	 * @throws MShop_Common_Exception if creating items failed
	 * @see MW_Common_Criteria_SQL
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = $map = $typeIds = array();
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		try
		{
			$domain = explode( '.', $this->_prefix);

			if ( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new MShop_Exception( sprintf( 'Configuration not available' ) );
			}

			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = $this->_config['search'];
			$cfgPathCount =  $this->_config['count'];

			$name = trim( $this->_prefix, '.' );
			$required = array( $name );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[ $row['id'] ] = $row;
				$typeIds[ $row['typeid'] ] = null;
			}

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
			throw $e;
		}

		if( !empty( $typeIds ) )
		{
			$typeManager = $this->getSubManager( 'type' );
			$typeSearch = $typeManager->createSearch();
			$typeSearch->setConditions( $typeSearch->compare( '==', $name . '.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row['typeid'] ] ) ) {
					$row['type'] = $typeItems[ $row['typeid'] ]->getCode();
				}
				$items[ $row['id'] ] = $this->_createItem( $row );
			}
		}

		return $items;
	}


	/**
	 * Creates a search object including the base criteria (optionally).
	 *
	 * @param boolean $default Include default criteria
	 * @return MW_Common_Criteria_Interface Critera object
	 */
	public function createSearch( $default = false )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire();

		$object = new MW_Common_Criteria_SQL( $conn );

		$dbm->release( $conn );


		if( $default === true )
		{
			$item = reset( $this->_searchConfig );
			$prefix = substr( $item['code'], 0, strrpos( $item['code'], '.' ) );

			$expr = array();
			$curDate = date( 'Y-m-d H:i:00', time() );

			$expr[] = $object->compare( '>', $prefix . '.status', 0 );

			$exprTwo = array();
			$exprTwo[] = $object->compare( '<=', $prefix . '.datestart', $curDate );
			$exprTwo[] = $object->compare( '==', $prefix . '.datestart', null );
			$expr[] = $object->combine( '||', $exprTwo );

			$exprTwo = array();
			$exprTwo[] = $object->compare( '>=', $prefix . '.dateend', $curDate );
			$exprTwo[] = $object->compare( '==', $prefix . '.dateend', null );
			$expr[] = $object->combine( '||', $exprTwo );

			$searchConditions = $object->combine( '&&', $expr );
			$object->setConditions( $searchConditions );
		}

		return $object;
	}


	/**
	 * Creates a new manager for list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		switch( $manager )
		{
			case 'type':
				if( isset( $this->_typeManager ) ) {
					return $this->_typeManager;
				}
			default:
				return $this->_getSubManager( 'common', 'list/' . $manager, $name );
		}
	}


	/**
	 * Creates new common list item object.
	 *
	 * @see MShop_Common_Item_List_Default Default list item
	 * @param array $values Possible optional array keys can be given: id, parentid, refid, domain, pos, start, end
	 * @return MShop_Common_Item_List_Default New common list item object
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Common_Item_List_Default( $this->_prefix, $values );
	}


	/**
	* Returns the domain prefix.
	*
	* @return string Domain prefix with sub-domains separated by "."
	*/
	protected function _getPrefix()
	{
		return $this->_prefix;
	}


	/**
	* Returns the config array with SQL statements.
	*
	* @return array Associative list of operation as key and the SQL statement as value
	*/
	protected function _getConfig()
	{
		return $this->_config;
	}
}
