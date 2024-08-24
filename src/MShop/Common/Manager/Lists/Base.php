<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
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
	implements \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	use \Aimeos\MShop\Common\Manager\Methods;
	use \Aimeos\MShop\Common\Manager\Site;
	use \Aimeos\MShop\Common\Manager\DB;
	use \Aimeos\Macro\Macroable;


	private string $date;


	/**
	 * Creates the common list manager using the given context object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 *
	 * @throws \Aimeos\MShop\Exception if no configuration is available
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		$this->date = $context->datetime();
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Common\Manager\Lists\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/' . $this->getDomain() . 'manager/lists/submanagers';

		foreach( $this->context()->config()->get( $path, ['type'] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/common/manager/lists/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface New list item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values[$this->prefix() . 'siteid'] = $values[$this->prefix() . 'siteid'] ?? $this->context()->locale()->getSiteId();
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
		return $this->deleteItemsBase( $itemIds, 'mshop/common/manager/lists/delete' );
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
			$prefix = rtrim( $this->prefix(), '.' );
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
		$code = $this->prefix() . 'id';
		$criteria = $this->object()->filter( $default )->add( [$code => $id] );

		if( ( $item = $this->object()->search( $criteria, $ref )->first() ) ) {
			return $item;
		}

		$msg = sprintf( 'List item with ID "%2$s" in "%1$s" not found', $code, $id );
		throw new \Aimeos\MShop\Exception( $msg );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/' . $this->getDomain() . 'manager/lists/submanagers';
		return $this->getResourceTypeBase( $this->getDomain() . '/lists', $path, [], $withsub );
	}


	/**
	 * Returns the list attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$prefix = $this->getDomain() . '.lists.';

		return $this->createAttributes( [
			$prefix . 'id' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."id"',
				'label' => 'List ID',
				'type' => 'int',
				'public' => false,
			],
			$prefix . 'siteid' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."siteid"',
				'label' => 'List site ID',
				'public' => false,
			],
			$prefix . 'ctime' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."ctime"',
				'label' => 'List create date/time',
				'type' => 'datetime',
				'public' => false,
			],
			$prefix . 'mtime' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."mtime"',
				'label' => 'List modify date/time',
				'type' => 'datetime',
				'public' => false,
			],
			$prefix . 'editor' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."editor"',
				'label' => 'List editor',
				'public' => false,
			],
			$prefix . 'parentid' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."parentid"',
				'label' => 'List parent ID',
				'type' => 'int',
				'public' => false,
			],
			$prefix . 'key' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."key"',
				'label' => 'List key',
				'public' => false,
			],
			$prefix . 'type' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."type"',
				'label' => 'List type',
			],
			$prefix . 'refid' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."refid"',
				'label' => 'List reference ID',
			],
			$prefix . 'datestart' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."start"',
				'label' => 'List start date',
				'type' => 'datetime',
			],
			$prefix . 'dateend' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."end"',
				'label' => 'List end date',
				'type' => 'datetime',
			],
			$prefix . 'domain' => [
				'code' => $this->getDomain() . '.lists.domain',
				'internalcode' => $this->alias( $prefix . 'id' ) . '."domain"',
				'label' => 'List domain',
			],
			$prefix . 'position' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."pos"',
				'label' => 'List position',
				'type' => 'int',
			],
			$prefix . 'status' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."status"',
				'label' => 'List status',
				'type' => 'int',
			],
			$prefix . 'config' => [
				'internalcode' => $this->alias( $prefix . 'id' ) . '."config"',
				'label' => 'List config',
				'type' => 'json',
				'public' => false,
			],
		] );
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
		return $this->getSubManagerBase( $this->getDomain(), 'lists/' . $manager, $name );
	}


	/**
	 * Adds or updates an item object or a list of them.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface $items Item or list of items whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Saved item or items
	 */
	public function save( $items, bool $fetch = true )
	{
		foreach( map( $items ) as $item ) {
			$this->saveItem( $item, $fetch );
		}

		return is_array( $items ) ? map( $items ) : $items;
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
		$required = [$this->getDomain() . '.lists'];
		$conn = $this->context()->db( $this->getResourceName() );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$cfgPathSearch = 'mshop/common/manager/lists/search';
		$cfgPathCount = 'mshop/common/manager/lists/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		while( $row = $results->fetch() )
		{
			if( ( $row[$this->prefix() . 'config'] = json_decode( $row[$this->prefix() . 'config'], true ) ) === null ) {
				$row[$this->prefix() . 'config'] = [];
			}

			if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
				$items[$row[$this->prefix() . 'id']] = $item;
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

		return new \Aimeos\MShop\Common\Item\Lists\Standard( $this->prefix(), $values );
	}


	/**
	 * Returns the domain prefix.
	 *
	 * @return string Domain prefix with sub-domains separated by "."
	 */
	protected function prefix() : string
	{
		return $this->getDomain() . '.lists.';
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

			if( ( $attr = current( $manager->getSearchAttributes() ) ) === false )
			{
				$msg = sprintf( 'No search configuration available for domain "%1$s"', $domain );
				throw new \Aimeos\MShop\Exception( $msg );
			}

			$search = $manager->filter()->slice( 0, count( $list ) )->add( [$attr->getCode() => $list] );

			foreach( $manager->search( $search, $domains ) as $id => $item ) {
				$items[$domain][$id] = $item;
			}
		}

		return $items;
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
		$path = 'mshop/common/manager/lists/';
		$columns = $this->object()->getSaveAttributes();

		if( $id === null ) {
			$type = 'insert'; $mode = true;
		} else {
			$type = 'update'; $mode = false;
		}

		$idx = 1;
		$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path . $type ), $mode );
		$stmt = $this->getCachedStatement( $conn, 'mshop/' . $this->getDomain() . '/manager/lists/' . $type, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt->bind( $idx++, $item->getParentId(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getKey() );
		$stmt->bind( $idx++, $item->getType() );
		$stmt->bind( $idx++, $item->getDomain() );
		$stmt->bind( $idx++, $item->getRefId() );
		$stmt->bind( $idx++, $item->getDateStart() );
		$stmt->bind( $idx++, $item->getDateEnd() );
		$stmt->bind( $idx++, json_encode( $item->getConfig(), JSON_FORCE_OBJECT ) );
		$stmt->bind( $idx++, $item->getPosition(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $item->getStatus(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $context->datetime() ); //mtime
		$stmt->bind( $idx++, $this->context()->editor() );


		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $context->datetime() ); //ctime
		}

		$stmt->execute()->finish();

		if( $id === null && $fetch === true ) {
			$id = $this->newId( $conn, 'mshop/common/manager/lists/newid' );
		}

		$item->setId( $id );

		return $item;
	}


	/**
	 * Returns the name of the used table
	 *
	 * @return string Table name
	 */
	protected function getTable() : string
	{
		return substr( parent::getTable(), 0, -1 ); // cuts of the "s" from "lists"
	}
}
