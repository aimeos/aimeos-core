<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
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
	private string $date;
	private string $prefix;
	private array $searchConfig;


	/**
	 * Creates the common list manager using the given context object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 *
	 * @throws \Aimeos\MShop\Exception if no configuration is available
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		$this->date = $context->datetime();
		$this->searchConfig = $this->getSearchConfig();

		if( ( $entry = reset( $this->searchConfig ) ) === false )
		{
			$msg = $this->context()->translate( 'mshop', 'Search configuration not available' );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		if( ( $pos = strrpos( $entry['code'], '.' ) ) === false )
		{
			$msg = $this->context()->translate( 'mshop', 'Search configuration for "%1$s" not available' );
			throw new \Aimeos\MShop\Exception( sprintf( $msg, $entry['code'] ) );
		}

		if( empty( $this->prefix = substr( $entry['code'], 0, $pos + 1 ) ) )
		{
			$msg = $this->context()->translate( 'mshop', 'Search configuration for "%1$s" not available' );
			throw new \Aimeos\MShop\Exception( sprintf( $msg, $entry['code'] ) );
		}

		parent::__construct( $context );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface New list item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values[$this->prefix . 'siteid'] = $values[$this->prefix . 'siteid'] ?? $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Common\Manager\Lists\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->deleteItemsBase( $itemIds, $this->getConfigPath() . 'delete' );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		if( $default !== false )
		{
			$prefix = rtrim( $this->getPrefix(), '.' );
			$object = $this->filterBase( $prefix, $default );

			$expr = [$object->getConditions()];

			$exprTwo = [];
			$exprTwo[] = $object->compare( '<=', $prefix . '.datestart', $this->date );
			$exprTwo[] = $object->compare( '==', $prefix . '.datestart', null );
			$expr[] = $object->or( $exprTwo );

			$exprTwo = [];
			$exprTwo[] = $object->compare( '>=', $prefix . '.dateend', $this->date );
			$exprTwo[] = $object->compare( '==', $prefix . '.dateend', null );
			$expr[] = $object->or( $exprTwo );

			$object->setConditions( $object->and( $expr ) );

			return $object;
		}

		return parent::filter();
	}


	/**
	 * Creates common list item object for the given common list item id.
	 *
	 * @param string $id Id of common list item object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface Returns common list item object of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( ( $conf = reset( $this->searchConfig ) ) === false || !isset( $conf['code'] ) )
		{
			$msg = $this->context()->translate( 'mshop', 'Search configuration not available' );
			throw new \Aimeos\MShop\Exception( $msg );
		}

		$criteria = $this->object()->filter( $default )->add( [$conf['code'] => $id] );

		if( ( $item = $this->object()->search( $criteria, $ref )->first() ) ) {
			return $item;
		}

		$msg = sprintf( 'List item with ID "%2$s" in "%1$s" not found', $conf['code'], $id );
		throw new \Aimeos\MShop\Exception( $msg );
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
	 * Updates or adds a common list item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $item List item object which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Common\Item\Lists\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Lists\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$date = date( 'Y-m-d H:i:s' );
		$path = $this->getConfigPath();
		$columns = $this->object()->getSaveAttributes();

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

		$stmt->bind( $idx++, $item->getParentId(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getKey() );
		$stmt->bind( $idx++, $item->getType() );
		$stmt->bind( $idx++, $item->getDomain() );
		$stmt->bind( $idx++, $item->getRefId() );
		$stmt->bind( $idx++, $item->getDateStart() );
		$stmt->bind( $idx++, $item->getDateEnd() );
		$stmt->bind( $idx++, json_encode( $item->getConfig() ) );
		$stmt->bind( $idx++, $item->getPosition(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getStatus(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $date ); //mtime
		$stmt->bind( $idx++, $this->context()->editor() );


		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $date ); //ctime
		}

		$stmt->execute()->finish();

		if( $id === null && $fetch === true ) {
			$id = $this->newId( $conn, $this->getConfigPath() . 'newid' );
		}

		$item->setId( $id );

		return $item;
	}


	/**
	 * Search for all list items based on the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of list items implementing \Aimeos\MShop\Common\Item\Lists\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$conn = $this->context()->db( $this->getResourceName() );

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
				$str = 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s';
				$msg = sprintf( $str, $this->prefix . 'config', $row[$this->prefix . 'id'], $config );
				$this->context()->logger()->warning( $msg, 'core' );
			}

			if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
				$items[$row[$this->prefix . 'id']] = $item;
			}
		}

		return $this->buildItems( $items, $ref );
	}


	/**
	 * Creates the items with address item, list items and referenced items.
	 *
	 * @param array $map Associative list of IDs as keys and the associative array of values
	 * @param string[] $domains List of domains to fetch list items and referenced items for
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface with IDs as keys
	 */
	protected function buildItems( array $map, array $domains ) : \Aimeos\Map
	{
		$refItemMap = $refIdMap = [];

		if( !empty( $domains ) )
		{
			foreach( $map as $listItem ) {
				$refIdMap[$listItem->getDomain()][] = $listItem->getRefId();
			}

			$refItemMap = $this->getRefItems( $refIdMap, $domains );
		}

		foreach( $map as $id => $listItem )
		{
			if( isset( $refItemMap[$listItem->getDomain()][$listItem->getRefId()] ) ) {
				$listItem->setRefItem( $refItemMap[$listItem->getDomain()][$listItem->getRefId()] );
			}
		}

		return map( $map );
	}


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


	/**
	 * Returns the referenced items for the given IDs.
	 *
	 * @param array $refIdMap Associative list of domain/ref-ID key/value pairs
	 * @param string[] $domains List of domain names whose referenced items should be attached
	 * @return array Associative list of parent-item-ID/domain/items key/value pairs
	 */
	protected function getRefItems( array $refIdMap, array $domains ) : array
	{
		$items = [];

		foreach( $refIdMap as $domain => $list )
		{
			$manager = \Aimeos\MShop::create( $this->context(), $domain );

			$search = $manager->filter()->slice( 0, count( $list ) )->add( [
				str_replace( '/', '.', $domain ) . '.id' => $list
			] );

			foreach( $manager->search( $search, $domains ) as $id => $item ) {
					$items[$domain][$id] = $item;
			}
		}

		return $items;
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
}
