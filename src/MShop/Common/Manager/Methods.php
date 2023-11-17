<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager;


/**
 * Method trait for managers
 *
 * @package MShop
 * @subpackage Common
 */
trait Methods
{
	private ?\Aimeos\MShop\Common\Manager\Iface $object = null;
	private array $filterFcn = [];
	private string $domain;
	private string $subpath;


	/**
	 * Adds a filter callback for an item type
	 *
	 * @param string $iface Interface name of the item to apply the filter to
	 * @param \Closure $fcn Anonymous function receiving the item to check as first parameter
	 */
	public function addFilter( string $iface, \Closure $fcn )
	{
		if( !isset( $this->filterFcn[$iface] ) ) {
			$this->filterFcn[$iface] = [];
		}

		$this->filterFcn[$iface][] = $fcn;
	}


	/**
	 * Returns the class names of the manager and used decorators.
	 *
	 * @return array List of class names
	 */
	public function classes() : array
	{
		return [get_class( $this )];
	}


	/**
	 * Removes old entries from the storage
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this;
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Attribute\Item\Iface New attribute item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return new \Aimeos\MShop\Common\Item\Base( '', $values );
	}


	/**
	 * Creates a new cursor based on the filter criteria
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @return \Aimeos\MShop\Common\Cursor\Iface Cursor object
	 */
	public function cursor( \Aimeos\Base\Criteria\Iface $filter ) : \Aimeos\MShop\Common\Cursor\Iface
	{
		return new \Aimeos\MShop\Common\Cursor\Standard( $filter );
	}


	/**
	 * Deletes one or more items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|\Aimeos\Map|array|string $items Item object, ID or a list of them
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this;
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
		throw new \LogicException( 'Not implemented' );
	}


	/**
	 * Returns the item specified by its ID
	 *
	 * @param string $id Id of item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		throw new \LogicException( 'Not implemented' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		return [$this->getManagerPath()];
	}


	/**
	 * Returns the additional column/search definitions
	 *
	 * @return array Associative list of column names as keys and items implementing \Aimeos\Base\Criteria\Attribute\Iface
	 */
	public function getSaveAttributes() : array
	{
		return [];
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		return [];
	}


	/**
	 * Returns a new manager for attribute extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g Type, List's etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		throw new \LogicException( 'Not implemented' );
	}


	/**
	 * Iterates over all matched items and returns the found ones
	 *
	 * @param \Aimeos\MShop\Common\Cursor\Iface $cursor Cursor object with filter, domains and cursor
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @return \Aimeos\Map|null List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function iterate( \Aimeos\MShop\Common\Cursor\Iface $cursor, array $ref = [] ) : ?\Aimeos\Map
	{
		return null;
	}


	/**
	 * Adds or updates an item object or a list of them.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface $items Item or list of items whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface Saved item or items
	 */
	public function save( $items, bool $fetch = true )
	{
		return $items;
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $filter, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		return map();
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Reference to the outmost manager or decorator
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function setObject( \Aimeos\MShop\Common\Manager\Iface $object ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$this->object = $object;
		return $this;
	}


	/**
	 * Starts a database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function begin() : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this;
	}


	/**
	 * Commits the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function commit() : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this;
	}


	/**
	 * Rolls back the running database transaction on the connection identified by the given name
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	public function rollback() : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this;
	}


	/**
	 * Applies the filters for the item type to the item
	 *
	 * @param object $item Item to apply the filter to
	 * @return object|null Object if the item should be used, null if not
	 */
	protected function applyFilter( $item )
	{
		foreach( $this->filterFcn as $iface => $fcnList )
		{
			if( is_object( $item ) && $item instanceof $iface )
			{
				foreach( $fcnList as $fcn )
				{
					if( $fcn( $item ) === null ) {
						return null;
					}
				}
			}
		}

		return $item;
	}


	/**
	 * Creates the criteria attribute items from the list of entries
	 *
	 * @param array $list Associative array of code as key and array with properties as values
	 * @return \Aimeos\Base\Criteria\Attribute\Standard[] List of criteria attribute items
	 */
	protected function createAttributes( array $list ) : array
	{
		$attr = [];

		foreach( $list as $key => $fields )
		{
			$fields['code'] = $fields['code'] ?? $key;
			$attr[$key] = new \Aimeos\Base\Criteria\Attribute\Standard( $fields );
		}

		return $attr;
	}


	/**
	 * Returns the full configuration key for the passed last part
	 *
	 * @param string $name Configuration last part
	 * @return string Full configuration key
	 */
	protected function getConfigKey( string $name ) : string
	{
		$subPath = $this->getSubPath();
		return 'mshop/' . $this->getDomain() . '/manager/' . ( $subPath ? $subPath . '/' : '' ) . $name;
	}


	/**
	 * Returns the manager domain
	 *
	 * @return string Manager domain e.g. "product"
	 */
	protected function getDomain() : string
	{
		if( !isset( $this->domain ) ) {
			$this->initMethods();
		}

		return $this->domain;
	}


	/**
	 * Returns the manager path
	 *
	 * @return string Manager path e.g. "product/lists/type"
	 */
	protected function getManagerPath() : string
	{
		$subPath = $this->getSubPath();
		return $this->getDomain() . ( $subPath ? '/' . $subPath : '' );
	}


	/**
	 * Returns the attribute helper functions for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and helper function
	 */
	protected function getSearchFunctions( array $attributes ) : array
	{
		$list = [];
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$list[$item->getCode()] = $item->getFunction();
			} else if( isset( $item['code'] ) ) {
				$list[$item['code']] = $item['function'] ?? null;
			} else {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $list;
	}


	/**
	 * Returns the item search key for the passed name
	 *
	 * @return string Item prefix e.g. "product.lists.type.id"
	 */
	protected function getSearchKey( string $name = '' ) : string
	{
		$subPath = $this->getSubPath();
		return $this->getDomain() . ( $subPath ? '.' . $subPath : '' ) . ( $name ? '.' . $name : '' );
	}


	/**
	 * Returns the attribute translations for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute code
	 */
	protected function getSearchTranslations( array $attributes ) : array
	{
		$translations = [];
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$translations[$item->getCode()] = $item->getInternalCode();
			} else if( isset( $item['code'] ) ) {
				$translations[$item['code']] = $item['internalcode'];
			} else {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $translations;
	}


	/**
	 * Returns the attribute types for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute type
	 */
	protected function getSearchTypes( array $attributes ) : array
	{
		$types = [];
		$iface = \Aimeos\Base\Criteria\Attribute\Iface::class;

		foreach( $attributes as $key => $item )
		{
			if( $item instanceof $iface ) {
				$types[$item->getCode()] = $item->getInternalType();
			} else if( isset( $item['code'] ) ) {
				$types[$item['code']] = $item['internaltype'];
			} else {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid attribute at position "%1$d"', $key ) );
			}
		}

		return $types;
	}


	/**
	 * Returns the manager domain sub-path
	 *
	 * @return string Manager domain sub-path e.g. "lists/type"
	 */
	protected function getSubPath() : string
	{
		if( !isset( $this->subpath ) ) {
			$this->initMethods();
		}

		return $this->subpath;
	}


	/**
	 * Returns the name of the used table
	 *
	 * @return string Table name e.g. "mshop_product_lists_type"
	 */
	protected function getTable() : string
	{
		$subPath = $this->getSubPath();
		return 'mshop_' . $this->getDomain() . ( $subPath ? '_' . str_replace( '/', '_', $subPath ) : '' );
	}


	/**
	 * Initializes the trait
	 */
	protected function initMethods()
	{
		$parts = explode( '\\', strtolower( get_class( $this ) ) );
		array_shift( $parts ); array_shift( $parts ); // remove "Aimeos\MShop"
		array_pop( $parts );

		$this->domain = array_shift( $parts ) ?: '';
		array_shift( $parts ); // remove "manager"
		$this->subpath = join( '/', $parts );
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	protected function object() : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->object ?? $this;
	}
}
