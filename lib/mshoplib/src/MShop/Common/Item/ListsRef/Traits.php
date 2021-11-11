<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\ListsRef;


/**
 * Common trait for items containing list items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	private $listItems = [];
	private $listRefItems = [];
	private $listRmItems = [];
	private $listPrepared = false;
	private $listRmMap = [];
	private $listMap = [];
	private $listMax = 0;


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		parent::__clone();

		foreach( $this->listItems as $domain => $list )
		{
			foreach( $list as $id => $item ) {
				$this->listItems[$domain][$id] = clone $item;
			}
		}

		foreach( $this->listRefItems as $domain => $list )
		{
			foreach( $list as $id => $item ) {
				$this->listRefItems[$domain][$id] = clone $item;
			}
		}

		foreach( $this->listRmItems as $key => $item ) {
			$this->listRmItems[$key] = clone $item;
		}

		$this->listPrepared = false;
	}


	/**
	 * Adds a new or overwrite an existing list item which references the given domain item (created if it doesn't exist)
	 *
	 * @param string $domain Name of the domain (e.g. media, text, etc.)
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the new domain item
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem New item added to the given domain or null if no item should be referenced
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface Self object for method chaining
	 */
	public function addListItem( string $domain, \Aimeos\MShop\Common\Item\Lists\Iface $listItem, \Aimeos\MShop\Common\Item\Iface $refItem = null ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		if( $refItem !== null )
		{
			$id = $refItem->getId() ?: '#' . $this->listMax++;
			$listItem->setRefId( $id );

			if( $refItem instanceof \Aimeos\MShop\Common\Item\Domain\Iface ) {
				$refItem->setDomain( $this->getResourceType() );
			}

			$this->listRefItems[$domain][$id] = $refItem;
		}

		$id = $listItem->getId() ?: '_' . $this->getId() . '_' . $domain . '_' . $listItem->getType() . '_' . $listItem->getRefId();

		unset( $this->listItems[$domain][$id] ); // append at the end
		$this->listItems[$domain][$id] = $listItem->setDomain( $domain )->setRefItem( $refItem );

		if( isset( $this->listMap[$domain] ) )
		{
			unset( $this->listMap[$domain][$listItem->getType()][$listItem->getRefId()] ); // append at the end
			$this->listMap[$domain][$listItem->getType()][$listItem->getRefId()] = $listItem;
		}

		return $this;
	}


	/**
	 * Removes a list item which references the given domain item (removed as well if it exists)
	 *
	 * @param string $domain Name of the domain (e.g. media, text, etc.)
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the domain item
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem Existing item removed from the given domain or null if item shouldn't be removed
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface Self object for method chaining
	 */
	public function deleteListItem( string $domain, \Aimeos\MShop\Common\Item\Lists\Iface $listItem, \Aimeos\MShop\Common\Item\Iface $refItem = null ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		if( isset( $this->listItems[$domain] )
			&& ( $key = array_search( $listItem, $this->listItems[$domain], true ) ) !== false
		) {
			$this->listRmItems[] = $this->listRmMap[$domain][] = $listItem->setRefItem( $refItem );

			unset( $this->listMap[$domain][$listItem->getType()][$listItem->getRefId()] );
			unset( $this->listItems[$domain][$key] );
		}

		return $this;
	}


	/**
	 * Removes a list of list items which references their domain items (removed as well if it exists)
	 *
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $items Existing list items
	 * @param bool $all True to delete referenced items as well, false for list items only
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If an item isn't a list item or isn't found
	 */
	public function deleteListItems( iterable $items, bool $all = false ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		foreach( $items as $item )
		{
			\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Common\Item\Lists\Iface::class, $item );

			$refItem = ( $all === true ? $item->getRefItem() : null );
			$this->deleteListItem( $item->getDomain(), $item, $refItem );
		}

		return $this;
	}


	/**
	 * Returns the domains for which items are available
	 *
	 * @return string[] List of domain names
	 */
	public function getDomains() : array
	{
		return array_keys( $this->listItems );
	}


	/**
	 * Returns the deleted list items which include the domain items if available
	 *
	 * @param string|null $domain Domain name to get the deleted list items for
	 * @return \Aimeos\Map Associative list of domains as keys list items as values or list items only
	 */
	public function getListItemsDeleted( string $domain = null ) : \Aimeos\Map
	{
		if( $domain !== null ) {
			return map( $this->listRmMap[$domain] ?: [] );
		}

		return map( $this->listRmItems );
	}


	/**
	 * Returns the list item for the given reference ID, domain and list type
	 *
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param string $listtype Name of the list item type
	 * @param string $refId Unique ID of the referenced item
	 * @param bool $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface|null Matching list item or null if none
	 */
	public function getListItem( string $domain, string $listtype, string $refId, bool $active = true ) : ?\Aimeos\MShop\Common\Item\Lists\Iface
	{
		if( !isset( $this->listMap[$domain] ) && isset( $this->listItems[$domain] ) )
		{
			$map = [];

			foreach( $this->listItems[$domain] as $listItem ) {
				$map[$listItem->getType()][$listItem->getRefId()] = $listItem;
			}

			$this->listMap[$domain] = $map;
		}

		if( isset( $this->listMap[$domain][$listtype][$refId] ) )
		{
			$listItem = $this->listMap[$domain][$listtype][$refId];

			if( $active === true && $listItem->isAvailable() === false ) {
				return null;
			}

			if( isset( $this->listRefItems[$domain][$refId] ) ) {
				$listItem->setRefItem( $this->listRefItems[$domain][$refId] );
			}

			return $listItem;
		}

		return null;
	}


	/**
	 * Returns the list items attached, optionally filtered by domain and list type.
	 *
	 * The reference parameter in search() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param array|string|null $domain Name/Names of the domain (e.g. product, text, etc.) or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @param bool $active True to return only active items, false to return all
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	public function getListItems( $domain = null, $listtype = null, $type = null, bool $active = true ) : \Aimeos\Map
	{
		$result = [];
		$this->prepareListItems();

		$iface = \Aimeos\MShop\Common\Item\TypeRef\Iface::class;
		$listTypes = ( is_array( $listtype ) ? $listtype : array( $listtype ) );
		$types = ( is_array( $type ) ? $type : array( $type ) );


		foreach( $this->listItems as $dname => $list )
		{
			if( is_array( $domain ) && !in_array( $dname, $domain ) || is_string( $domain ) && $dname !== $domain ) {
				continue;
			}

			foreach( $list as $id => $item )
			{
				$refItem = $item->getRefItem();

				if( $type && ( !$refItem || !( $refItem instanceof $iface ) || !in_array( $refItem->getType(), $types ) ) ) {
					continue;
				}

				if( $listtype && !in_array( $item->getType(), $listTypes ) ) {
					continue;
				}

				if( $active && !$item->isAvailable() ) {
					continue;
				}

				$result[$id] = $item;
			}
		}

		return map( $result );
	}


	/**
	 * Returns the product, text, etc. items filtered by domain and optionally by type and list type.
	 *
	 * The reference parameter in search() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param array|string|null $domain Name/Names of the domain (e.g. product, text, etc.) or null for all
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
	 * @param bool $active True to return only active items, false to return all
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function getRefItems( $domain = null, $type = null, $listtype = null, bool $active = true ) : \Aimeos\Map
	{
		$list = [];

		foreach( $this->getListItems( $domain, $listtype, $type, $active ) as $listItem )
		{
			if( ( $refItem = $listItem->getRefItem() ) !== null && ( $active === false || $refItem->isAvailable() ) ) {
				$list[$listItem->getDomain()][$listItem->getRefId()] = $refItem;
			}
		}

		if( is_array( $domain ) || $domain === null ) {
			return map( $list );
		}

		if( isset( $list[$domain] ) ) {
			return map( $list[$domain] );
		}

		return map();
	}


	/**
	 * Returns the label of the item.
	 * This method should be implemented in the derived class if a label column is available.
	 *
	 * @return string Label of the item
	 */
	public function getLabel() : string
	{
		return '';
	}


	/**
	 * Returns the localized text type of the item or the internal label if no name is available.
	 *
	 * @param string $type Text type to be returned
	 * @param string|null $langId Two letter ISO Language code of the text
	 * @return string Specified text type or label of the item
	 */
	public function getName( string $type = 'name', string $langId = null ) : string
	{
		foreach( $this->getRefItems( 'text', $type ) as $textItem )
		{
			if( $textItem->getLanguageId() === $langId || $langId === null ) {
				return $textItem->getContent();
			}
		}

		return $this->getLabel();
	}


	/**
	 * Returns the unique ID of the item.
	 *
	 * @return string|null ID of the item
	 */
	abstract public function getId() : ?string;


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	abstract public function getResourceType() : string;


	/**
	 * Compares the positions of two items for sorting.
	 *
	 * @param \Aimeos\MShop\Common\Item\Position\Iface $a First item
	 * @param \Aimeos\MShop\Common\Item\Position\Iface $b Second item
	 * @return int -1 if position of $a < $b, 1 if position of $a > $b and 0 if both positions are equal
	 */
	protected function comparePosition( \Aimeos\MShop\Common\Item\Position\Iface $a, \Aimeos\MShop\Common\Item\Position\Iface $b ) : int
	{
		if( $a->getPosition() === $b->getPosition() ) {
			return 0;
		}

		return ( $a->getPosition() < $b->getPosition() ) ? -1 : 1;
	}


	/**
	 * Initializes the list items in the trait
	 *
	 * @param array $listItems Two dimensional associative list of domain / ID / list items that implement \Aimeos\MShop\Common\Item\Lists\Iface
	 * @param array $refItems Two dimensional associative list of domain / ID / domain items that implement \Aimeos\MShop\Common\Item\Iface
	 */
	protected function initListItems( array $listItems, array $refItems )
	{
		$this->listItems = $listItems;
		$this->listRefItems = $refItems;

		foreach( $listItems as $list ) {
			$this->listMax += count( $list );
		}
	}


	/**
	 * Sorts the list items according to their position value and attaches the referenced item
	 */
	protected function prepareListItems()
	{
		if( $this->listPrepared === true ) {
			return;
		}

		foreach( $this->listItems as $domain => $list )
		{
			foreach( $list as $listItem )
			{
				$refId = $listItem->getRefId();

				if( isset( $this->listRefItems[$domain][$refId] ) ) {
					$listItem->setRefItem( $this->listRefItems[$domain][$refId] );
				}
			}

			uasort( $this->listItems[$domain], array( $this, 'comparePosition' ) );
		}

		$this->listPrepared = true;
	}
}
