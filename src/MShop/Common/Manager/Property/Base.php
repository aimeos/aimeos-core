<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2023
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Property;


/**
 * Abstract property manager implementation.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	private array $searchConfig;
	private ?string $languageId;
	private string $prefix;


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		$this->languageId = $context->locale()->getLanguageId();
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
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Property\Iface New property item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values[$this->prefix . 'siteid'] = $values[$this->prefix . 'siteid'] ?? $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
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
		$object = parent::filter();

		if( $default !== false )
		{
			$langid = $this->context()->locale()->getLanguageId();

			$expr = array(
				$object->compare( '==', $this->prefix . 'languageid', null ),
				$object->compare( '==', $this->prefix . 'languageid', $langid ),
			);

			$object->setConditions( $object->or( $expr ) );
		}

		return $object;
	}


	/**
	 * Inserts the new property items for product item
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface $item Property item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Property\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Common\Item\Property\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Property\Iface
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
		$stmt->bind( $idx++, $item->getLanguageId() );
		$stmt->bind( $idx++, $item->getValue() );
		$stmt->bind( $idx++, $date ); //mtime
		$stmt->bind( $idx++, $context->editor() );

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
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Common\Manager\Property\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->deleteItemsBase( $itemIds, $this->getConfigPath() . 'delete' );
	}


	/**
	 * Returns product property item with given Id.
	 *
	 * @param string $id Id of the product property item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Property\Iface Returns the product property item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( $this->prefix . 'id', $id, $ref, $default );
	}


	/**
	 * Search for all property items based on the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Property\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
		$cfgPathSearch = $this->getConfigPath() . 'search';
		$cfgPathCount = $this->getConfigPath() . 'count';
		$required = array( trim( $this->prefix, '.' ) );

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		while( ( $row = $results->fetch() ) !== null )
		{
			if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
				$items[$row[$this->prefix . 'id']] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Returns a new manager for product extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or "Standard") if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g property types, property lists etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'common', 'property/' . $manager, $name );
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
	 * Creates new property item object.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @return \Aimeos\MShop\Common\Item\Property\Iface New property item object
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MShop\Common\Item\Property\Iface
	{
		$values['.languageid'] = $this->languageId;
		return new \Aimeos\MShop\Common\Item\Property\Standard( $this->prefix, $values );
	}


	/**
	 * Returns the prefix used for the item keys.
	 *
	 * @return string Item key prefix
	 */
	protected function getPrefix() : string
	{
		return $this->prefix;
	}
}
