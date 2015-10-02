<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Abstract list manager implementation
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Manager_List_Abstract
	extends MShop_Common_Manager_Abstract
	implements MShop_Common_Manager_List_Interface
{
	private $prefix;
	private $configPath;
	private $searchConfig;


	/**
	 * Creates the common list manager using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 *
	 * @throws MShop_Exception if no configuration is available
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		$this->configPath = $this->getConfigPath();
		$this->searchConfig = $this->getSearchConfig();

		if( ( $entry = reset( $this->searchConfig ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration not available' ) );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}

		if( ( $this->prefix = substr( $entry['code'], 0, $pos + 1 ) ) === false ) {
			throw new MShop_Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}

		parent::__construct( $context );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param string $key Search key to aggregate items for
	 * @return array List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( MW_Common_Criteria_Interface $search, $key )
	{
		$required = array( trim( $this->prefix, '.' ) );
		return $this->aggregateBase( $search, $key, $this->configPath . 'aggregate', $required );
	}


	/**
	 * Creates new common list item object.
	 *
	 * @return MShop_Common_Item_List_Interface New list item object
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->getContext()->getLocale()->getSiteId() );
		return $this->createItemBase( $values );

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

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			if( $id === null ) {
				$path = $this->configPath . 'insert';
			} else {
				$path = $this->configPath . 'update';
			}

			$time = date( 'Y-m-d H:i:s' );
			$statement = $this->getCachedStatement( $conn, $path );

			$statement->bind( 1, $item->getParentId(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 2, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 3, $item->getTypeId(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 4, $item->getDomain(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 5, $item->getRefId(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 6, $item->getDateStart(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 7, $item->getDateEnd(), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 8, json_encode( $item->getConfig() ), MW_DB_Statement_Abstract::PARAM_STR );
			$statement->bind( 9, $item->getPosition(), MW_DB_Statement_Abstract::PARAM_INT );
			$statement->bind( 10, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );

			$statement->bind( 11, $time ); //mtime
			$statement->bind( 12, $this->getContext()->getEditor() );


			if( $id !== null ) {
				$statement->bind( 13, $id, MW_DB_Statement_Abstract::PARAM_INT );
			} else {
				$statement->bind( 13, $time ); //ctime
			}

			$statement->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = $this->configPath . 'newid';
					$item->setId( $this->newId( $conn, $context->getConfig()->get( $path, $path ) ) );
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
		$path = $this->configPath . 'delete';
		$this->deleteItemsBase( $ids, $this->getContext()->getConfig()->get( $path, $path ) );
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
		if( ( $conf = reset( $this->searchConfig ) ) === false || !isset( $conf['code'] ) ) {
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
		$context = $this->getContext();
		$config = $context->getConfig();
		$siteid = $context->getLocale()->getSiteId();

		$listItem = $this->getItem( $id );

		$newpos = $pos = 0;
		$oldpos = $listItem->getPosition();
		$parentid = $listItem->getParentId();
		$typeid = $listItem->getTypeId();
		$domain = $listItem->getDomain();

		if( $ref !== null ) {
			$pos = $this->getItem( $ref )->getPosition();
		}

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			if( $ref !== null )
			{
				$newpos = $pos;

				$sql = $config->get( $this->configPath . 'move' );

				$stmt = $conn->create( $sql );
				$stmt->bind( 1, +1, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); //mtime
				$stmt->bind( 3, $this->getContext()->getEditor() );
				$stmt->bind( 4, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 5, $parentid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 6, $typeid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 7, $domain );
				$stmt->bind( 8, $pos, MW_DB_Statement_Abstract::PARAM_INT );

				$stmt->execute()->finish();
			}
			else
			{
				$sql = $config->get( $this->configPath . 'getposmax' );

				$stmt = $conn->create( $sql );

				$stmt->bind( 1, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 2, $parentid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 3, $typeid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 4, $domain );

				$result = $stmt->execute();
				$row = $result->fetch();
				$result->finish();

				if( $row !== false ) {
					$newpos = $row['pos'] + 1;
				}
			}

			$sql = $config->get( $this->configPath . 'updatepos' );

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $newpos, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); // mtime
			$stmt->bind( 3, $this->getContext()->getEditor() );
			$stmt->bind( 4, $id, MW_DB_Statement_Abstract::PARAM_INT );

			$stmt->execute()->finish();

			if( $oldpos > $newpos ) {
				$oldpos++;
			}

			$sql = $config->get( $this->configPath . 'move' );

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, -1, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); // mtime
			$stmt->bind( 3, $this->getContext()->getEditor() );
			$stmt->bind( 4, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, $parentid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 6, $typeid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $domain );
			$stmt->bind( 8, $oldpos, MW_DB_Statement_Abstract::PARAM_INT );

			$stmt->execute()->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of items implementing MW_Common_Criteria_Attribute_Interface
	 * @deprecated Use getSearchAttributesBase() instead
	 * @todo 2015.03 Remove method
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true ) {
			$list = array_merge( $list, $this->getSubManager( 'type' )->getSearchAttributes() );
		}

		return $list;
	}


	/**
	 * Search for all list items based on the given critera.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param array $ref List of domains to fetch referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of list items implementing MShop_Common_Item_List_Interface
	 * @throws MShop_Exception if creating items failed
	 * @see MW_Common_Criteria_SQL
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = $map = $typeIds = array();

		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$domain = explode( '.', $this->prefix );

			if( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new MShop_Exception( sprintf( 'Configuration not available' ) );
			}

			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = $this->configPath . 'search';
			$cfgPathCount = $this->configPath . 'count';

			$name = trim( $this->prefix, '.' );
			$required = array( $name );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				if( ( $row['config'] = json_decode( $row['config'], true ) ) === null ) {
					$row['config'] = array();
				}

				$map[$row['id']] = $row;
				$typeIds[$row['typeid']] = null;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
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
				if( isset( $typeItems[$row['typeid']] ) ) {
					$row['type'] = $typeItems[$row['typeid']]->getCode();
				}
				$items[$row['id']] = $this->createItemBase( $row );
			}
		}

		return $items;
	}


	/**
	 * Search for all referenced items from the list based on the given critera.
	 *
	 * Only criteria from the list and list type can be used for searching and
	 * sorting, but no criteria from the referenced items.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param array $ref List of domains to fetch referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array Associative list of domains as keys and lists with pairs
	 *	of IDs and items implementing MShop_Common_Item_Interface
	 * @throws MShop_Exception If creating items failed
	 * @see MW_Common_Criteria_SQL
	 */
	public function searchRefItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = $map = array();
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$domain = explode( '.', $this->prefix );

			if( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new MShop_Exception( sprintf( 'Configuration not available' ) );
			}

			$level = MShop_Locale_Manager_Abstract::SITE_ALL;
			$cfgPathSearch = $this->configPath . 'search';
			$cfgPathCount = $this->configPath . 'count';

			$name = trim( $this->prefix, '.' );
			$required = array( $name );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$map[$row['domain']][] = $row['refid'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		foreach( $map as $domain => $list )
		{
			$manager = MShop_Factory::createManager( $context, $domain );

			$search = $manager->createSearch( true );
			$expr = array(
				$search->compare( '==', str_replace( '/', '.', $domain ) . '.id', $list ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSlice( 0, 0x7fffffff );

			$items[$domain] = $manager->searchItems( $search, $ref );
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
		if( $default === true )
		{
			$prefix = rtrim( $this->getPrefix(), '.' );
			$object = $this->createSearchBase( $prefix );

			$expr = array();
			$curDate = date( 'Y-m-d H:i:00' );

			$expr[] = $object->getConditions();

			$exprTwo = array();
			$exprTwo[] = $object->compare( '<=', $prefix . '.datestart', $curDate );
			$exprTwo[] = $object->compare( '==', $prefix . '.datestart', null );
			$expr[] = $object->combine( '||', $exprTwo );

			$exprTwo = array();
			$exprTwo[] = $object->compare( '>=', $prefix . '.dateend', $curDate );
			$exprTwo[] = $object->compare( '==', $prefix . '.dateend', null );
			$expr[] = $object->combine( '||', $exprTwo );

			$object->setConditions( $object->combine( '&&', $expr ) );

			return $object;
		}

		return parent::createSearch();
	}


	/**
	 * Creates a new manager for list extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'common', 'list/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	abstract protected function getConfigPath();


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	abstract protected function getSearchConfig();


	/**
	 * Returns the search attribute objects used for searching.
	 *
	 * @param array $list Associative list of search keys and the lists of search definitions
	 * @param string $path Configuration path to the sub-domains for fetching the search definitions
	 * @param string[] $default List of sub-domains if no others are configured
	 * @param boolean $withsub True to include search definitions of sub-domains, false if not
	 * @return array Associative list of search keys and objects implementing the MW_Common_Criteria_Attribute_Interface
	 * @todo 2015.03 Remove method as it's a workaround for backward compatibility
	 * @since 2014.09
	 */
	protected function getSearchAttributesBase( array $list, $path, array $default, $withsub )
	{
		return parent::getSearchAttributesBase( $this->getSearchConfig(), $path, $default, $withsub );
	}


	/**
	 * Creates new common list item object.
	 *
	 * @see MShop_Common_Item_List_Default Default list item
	 * @param array $values Possible optional array keys can be given: id, parentid, refid, domain, pos, start, end
	 * @return MShop_Common_Item_List_Default New common list item object
	 */
	protected function createItemBase( array $values = array() )
	{
		return new MShop_Common_Item_List_Default( $this->prefix, $values );
	}


	/**
	 * Returns the domain prefix.
	 *
	 * @return string Domain prefix with sub-domains separated by "."
	 */
	protected function getPrefix()
	{
		return $this->prefix;
	}
}
