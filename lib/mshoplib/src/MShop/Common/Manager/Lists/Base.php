<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Lists;


/**
 * Abstract list manager implementation
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\Lists\Iface
{
	private $prefix;
	private $searchConfig;


	/**
	 * Creates the common list manager using the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 *
	 * @throws \Aimeos\MShop\Exception if no configuration is available
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$this->searchConfig = $this->getSearchConfig();

		if( ( $entry = reset( $this->searchConfig ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration not available' ) );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}

		if( ( $this->prefix = substr( $entry['code'], 0, $pos + 1 ) ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration for "%1$s" not available', $entry['code'] ) );
		}

		parent::__construct( $context );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key to aggregate items for
	 * @return array List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key )
	{
		$required = array( trim( $this->prefix, '.' ) );
		return $this->aggregateBase( $search, $key, $this->getConfigPath() . 'aggregate', $required );
	}


	/**
	 * Creates new common list item object.
	 *
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface New list item object
	 */
	public function createItem()
	{
		$values = array(
			$this->prefix . 'siteid' => $this->getContext()->getLocale()->getSiteId()
		);
		return $this->createItemBase( $values );

	}


	/**
	 * Updates or adds a common list item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $item List item object which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			if( $id === null ) {
				$type = 'insert';
			} else {
				$type = 'update';
			}

			$time = date( 'Y-m-d H:i:s' );
			$statement = $this->getCachedStatement( $conn, $this->getConfigPath() . $type );

			$statement->bind( 1, $item->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$statement->bind( 2, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$statement->bind( 3, $item->getTypeId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$statement->bind( 4, $item->getDomain(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 5, $item->getRefId(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 6, $item->getDateStart(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 7, $item->getDateEnd(), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 8, json_encode( $item->getConfig() ), \Aimeos\MW\DB\Statement\Base::PARAM_STR );
			$statement->bind( 9, $item->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$statement->bind( 10, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			$statement->bind( 11, $time ); //mtime
			$statement->bind( 12, $this->getContext()->getEditor() );


			if( $id !== null ) {
				$statement->bind( 13, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$statement->bind( 13, $time ); //ctime
			}

			$statement->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = $this->getConfigPath() . 'newid';
					$item->setId( $this->newId( $conn, $path ) );
				} else {
					$item->setId( $id ); // modified false
				}
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
		$this->deleteItemsBase( $ids, $this->getConfigPath() . 'delete' );
	}


	/**
	 * Creates common list item object for the given common list item id.
	 *
	 * @param integer $id Id of common list item object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Returns common list item object of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		if( ( $conf = reset( $this->searchConfig ) ) === false || !isset( $conf['code'] ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration not available' ) );
		}

		$criteria = $this->createSearch( $default );
		$expr = [
			$criteria->compare( '==', $conf['code'], $id ),
			$criteria->getConditions()
		];
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false )
		{
			$msg = sprintf( 'List item with ID "%2$s" in "%1$s" not found', $conf['code'], $id );
			throw new \Aimeos\MShop\Exception( $msg );
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
		$siteid = $context->getLocale()->getSiteId();
		$cfgPath = $this->getConfigPath();

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

				$sql = $this->getSqlConfig( $cfgPath . 'move' );

				$stmt = $conn->create( $sql );
				$stmt->bind( 1, +1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); //mtime
				$stmt->bind( 3, $this->getContext()->getEditor() );
				$stmt->bind( 4, $siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 5, $parentid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 6, $typeid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 7, $domain );
				$stmt->bind( 8, $pos, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

				$stmt->execute()->finish();
			}
			else
			{
				$sql = $this->getSqlConfig( $cfgPath . 'getposmax' );

				$stmt = $conn->create( $sql );

				$stmt->bind( 1, $siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 2, $parentid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 3, $typeid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 4, $domain );

				$result = $stmt->execute();
				$row = $result->fetch();
				$result->finish();

				if( $row !== false ) {
					$newpos = $row['pos'] + 1;
				}
			}

			$sql = $this->getSqlConfig( $cfgPath . 'updatepos' );

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $newpos, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); // mtime
			$stmt->bind( 3, $this->getContext()->getEditor() );
			$stmt->bind( 4, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			$stmt->execute()->finish();

			if( $oldpos > $newpos ) {
				$oldpos++;
			}

			if( $oldpos > 0 )
			{
				$sql = $this->getSqlConfig( $cfgPath . 'move' );

				$stmt = $conn->create( $sql );
				$stmt->bind( 1, -1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); // mtime
				$stmt->bind( 3, $this->getContext()->getEditor() );
				$stmt->bind( 4, $siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 5, $parentid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 6, $typeid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 7, $domain );
				$stmt->bind( 8, $oldpos, \Aimeos\MW\DB\Statement\Base::PARAM_INT );

				$stmt->execute()->finish();
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
	 * Search for all list items based on the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of list items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$items = $map = $typeIds = [];

		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$domain = explode( '.', $this->prefix );

			if( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Configuration not available' ) );
			}

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$cfgPathSearch = $this->getConfigPath() . 'search';
			$cfgPathCount = $this->getConfigPath() . 'count';

			$name = trim( $this->prefix, '.' );
			$required = array( $name );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				if( ( $row[$this->prefix . 'config'] = json_decode( $row[$this->prefix . 'config'], true ) ) === null ) {
					$row[$this->prefix . 'config'] = [];
				}

				$map[$row[$this->prefix . 'id']] = $row;
				$typeIds[$row[$this->prefix . 'typeid']] = null;
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
			$typeSearch->setConditions( $typeSearch->compare( '==', $name . '.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[$row[$this->prefix . 'typeid']] ) )
				{
					$row[$this->prefix . 'type'] = $typeItems[$row[$this->prefix . 'typeid']]->getCode();
					$row[$this->prefix . 'typename'] = $typeItems[$row[$this->prefix . 'typeid']]->getName();
				}

				$items[$row[$this->prefix . 'id']] = $this->createItemBase( $row );
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
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array Associative list of domains as keys and lists with pairs of IDs and items implementing \Aimeos\MShop\Common\Item\Iface
	 * @throws \Aimeos\MShop\Exception If creating items failed
	 * @see \Aimeos\MW\Criteria\SQL
	 */
	public function searchRefItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$items = $map = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$domain = explode( '.', $this->prefix );

			if( ( $topdomain = array_shift( $domain ) ) === null ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Configuration not available' ) );
			}

			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$cfgPathSearch = $this->getConfigPath() . 'search';
			$cfgPathCount = $this->getConfigPath() . 'count';

			$name = trim( $this->prefix, '.' );
			$required = array( $name );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$map[$row[$this->prefix . 'domain']][] = $row[$this->prefix . 'refid'];
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		foreach( $map as $domain => $list )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, $domain );

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
	 * @return \Aimeos\MW\Criteria\Iface Critera object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$prefix = rtrim( $this->getPrefix(), '.' );
			$object = $this->createSearchBase( $prefix );

			$expr = [];
			$curDate = date( 'Y-m-d H:i:00' );

			$expr[] = $object->getConditions();

			$exprTwo = [];
			$exprTwo[] = $object->compare( '<=', $prefix . '.datestart', $curDate );
			$exprTwo[] = $object->compare( '==', $prefix . '.datestart', null );
			$expr[] = $object->combine( '||', $exprTwo );

			$exprTwo = [];
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
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g type, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'common', 'lists/' . $manager, $name );
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
	 * Creates new common list item object.
	 *
	 * @see \Aimeos\MShop\Common\Item\Lists\Standard Default list item
	 * @param array $values Possible optional array keys can be given: id, parentid, refid, domain, pos, start, end
	 * @return \Aimeos\MShop\Common\Item\Lists\Standard New common list item object
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Common\Item\Lists\Standard( $this->prefix, $values );
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
