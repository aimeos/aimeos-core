<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
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
{
	private $date;
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
		$this->date = $context->getDateTime();
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
	 * @return integer[] List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, string $key ) : array
	{
		$required = array( trim( $this->prefix, '.' ) );
		return $this->aggregateBase( $search, $key, $this->getConfigPath() . 'aggregate', $required );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface New list item object
	 */
	public function createItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values[$this->prefix . 'siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Updates or adds a common list item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $item List item object which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Lists\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Lists\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );
			$path = $this->getConfigPath();
			$columns = $this->getObject()->getSaveAttributes();

			if( $id === null ) {
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path .= 'insert' ) );
			} else {
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path .= 'update' ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}

			$stmt->bind( $idx++, $item->getParentId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getKey() );
			$stmt->bind( $idx++, $item->getType() );
			$stmt->bind( $idx++, $item->getDomain() );
			$stmt->bind( $idx++, $item->getRefId() );
			$stmt->bind( $idx++, $item->getDateStart() );
			$stmt->bind( $idx++, $item->getDateEnd() );
			$stmt->bind( $idx++, json_encode( $item->getConfig() ) );
			$stmt->bind( $idx++, $item->getPosition(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $date ); //mtime
			$stmt->bind( $idx++, $this->getContext()->getEditor() );
			$stmt->bind( $idx++, $context->getLocale()->getSiteId() );


			if( $id !== null ) {
				$stmt->bind( 14, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			} else {
				$stmt->bind( 14, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$item->setId( $this->newId( $conn, $this->getConfigPath() . 'newid' ) );
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

		return $item;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Common\Manager\Lists\Iface Manager object for chaining method calls
	 */
	public function deleteItems( array $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->deleteItemsBase( $itemIds, $this->getConfigPath() . 'delete' );
	}


	/**
	 * Creates common list item object for the given common list item id.
	 *
	 * @param string $id Id of common list item object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool $default Add default criteria
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Returns common list item object of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( string $id, array $ref = [], bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( ( $conf = reset( $this->searchConfig ) ) === false || !isset( $conf['code'] ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Search configuration not available' ) );
		}

		$criteria = $this->getObject()->createSearch( $default );
		$expr = [
			$criteria->compare( '==', $conf['code'], $id ),
			$criteria->getConditions()
		];
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$items = $this->getObject()->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false )
		{
			$msg = sprintf( 'List item with ID "%2$s" in "%1$s" not found', $conf['code'], $id );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		return $item;
	}


	/**
	 * Search for all list items based on the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return array List of list items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : array
	{
		$items = [];

		$dbm = $this->getContext()->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$cfgPathSearch = $this->getConfigPath() . 'search';
			$cfgPathCount = $this->getConfigPath() . 'count';

			$name = trim( $this->prefix, '.' );
			$required = array( $name );

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null )
			{
				if( ( $row[$this->prefix . 'config'] = json_decode( $config = $row[$this->prefix . 'config'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', $this->prefix . 'list.config', $row['id'], $config );
					$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN );
				}

				$items[(string) $row[$this->prefix . 'id']] = $this->createItemBase( $row );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Creates a search object including the base criteria (optional)
	 *
	 * @param bool $default Include default criteria
	 * @return \Aimeos\MW\Criteria\Iface Search critera object
	 */
	public function createSearch( bool $default = false ) : \Aimeos\MW\Criteria\Iface
	{
		if( $default === true )
		{
			$prefix = rtrim( $this->getPrefix(), '.' );
			$object = $this->createSearchBase( $prefix );

			$expr = [$object->getConditions()];

			$exprTwo = [];
			$exprTwo[] = $object->compare( '<=', $prefix . '.datestart', $this->date );
			$exprTwo[] = $object->compare( '==', $prefix . '.datestart', null );
			$expr[] = $object->combine( '||', $exprTwo );

			$exprTwo = [];
			$exprTwo[] = $object->compare( '>=', $prefix . '.dateend', $this->date );
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
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'common', 'lists/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	abstract protected function getConfigPath() : string;


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	abstract protected function getSearchConfig() : array;


	/**
	 * Creates new common list item object.
	 *
	 * @see \Aimeos\MShop\Common\Item\Lists\Standard Default list item
	 * @param array $values Possible optional array keys can be given: id, parentid, refid, domain, pos, start, end
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface New common list item object
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MShop\Common\Item\Lists\Iface
	{
		$values['.date'] = $this->date;

		return new \Aimeos\MShop\Common\Item\Lists\Standard( $this->prefix, $values );
	}


	/**
	 * Returns the domain prefix.
	 *
	 * @return string Domain prefix with sub-domains separated by "."
	 */
	protected function getPrefix() : string
	{
		return $this->prefix;
	}
}
