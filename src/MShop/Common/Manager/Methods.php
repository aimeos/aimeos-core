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
	private array $type;


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
		return new \Aimeos\MShop\Common\Item\Base( $this->prefix(), $values );
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
	public function getSubManager( string $manager, ?string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
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
	 * Saves the dependent items of the item
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface Updated item
	 */
	public function saveRefs( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $item;
	}


	/**
	 * Searches for all items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $filter, array $ref = [], ?int &$total = null ) : \Aimeos\Map
	{
		return map();
	}


	/**
	 * Merges the data from the given map and the referenced items
	 *
	 * @param array $entries Associative list of ID as key and the associative list of property key/value pairs as values
	 * @param array $ref List of referenced items to fetch and add to the entries
	 * @return array Associative list of ID as key and the updated entries as value
	 */
	public function searchRefs( array $entries, array $ref ) : array
	{
		return $entries;
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
	 * Returns the type of the mananger as separate parts
	 *
	 * @return string[] List of manager part names
	 */
	public function type() : array
	{
		if( !isset( $this->type ) )
		{
			$parts = array_slice( explode( '\\', strtolower( get_class( $this ) ) ), 2, -1 );
			unset( $parts[1] );
			$this->type = $parts;
		}

		return $this->type;
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
			if( $item instanceof $iface )
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
			$fields['internalcode'] = $fields['internalcode'] ?? $key;
			$attr[$key] = new \Aimeos\Base\Criteria\Attribute\Standard( $fields );
		}

		return $attr;
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function prefix() : string
	{
		return '';
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

		foreach( $attributes as $key => $item ) {
			$list[$item->getCode()] = $item->getFunction();
		}

		return $list;
	}


	/**
	 * Returns the attribute translations for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute code
	 */
	protected function getSearchTranslations( array $attributes ) : array
	{
		$list = [];

		foreach( $attributes as $key => $item ) {
			$list[$item->getCode()] = $item->getInternalCode();
		}

		return $list;
	}


	/**
	 * Returns the attribute types for searching defined by the manager.
	 *
	 * @param \Aimeos\Base\Criteria\Attribute\Iface[] $attributes List of search attribute items
	 * @return array Associative array of attribute code and internal attribute type
	 */
	protected function getSearchTypes( array $attributes ) : array
	{
		$list = [];

		foreach( $attributes as $key => $item ) {
			$list[$item->getCode()] = $item->getInternalType();
		}

		return $list;
	}


	/**
	 * Checks if the given domain is in the list of domains
	 *
	 * @param array $ref List of domains
	 * @param string $domain Domain to check for
	 * @return bool True if domain is in the list, false if not
	 */
	protected function hasRef( array $ref, string $domain ) : bool
	{
		return isset( $ref[$domain] ) || in_array( $domain, $ref );
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
