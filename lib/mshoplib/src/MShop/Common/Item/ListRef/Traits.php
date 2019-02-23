<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\ListRef;


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
	}


	/**
	 * Adds a new or overwrite an existing list item which references the given domain item (created if it doesn't exist)
	 *
	 * @param string $domain Name of the domain (e.g. media, text, etc.)
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the new domain item
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem New item added to the given domain or null if no item should be referenced
	 * @return \Aimeos\MShop\Common\Item\ListRef\Iface Self object for method chaining
	 */
	public function addListItem( $domain, \Aimeos\MShop\Common\Item\Lists\Iface $listItem, \Aimeos\MShop\Common\Item\Iface $refItem = null )
	{
		$num = $this->listMax++;

		if( $refItem !== null )
		{
			$id = $refItem->getId() ?: '#' . $num;
			$listItem->setRefId( $id );

			if( $refItem instanceof \Aimeos\MShop\Common\Item\Domain\Iface ) {
				$refItem->setDomain( $this->getResourceType() );
			}

			$this->listRefItems[$domain][$id] = $refItem;
		}

		$id = $listItem->getId() ?: '_' . $num;
		$this->listItems[$domain][$id] = $listItem->setDomain( $domain )->setRefItem( $refItem );

		return $this;
	}


	/**
	 * Removes a list item which references the given domain item (removed as well if it exists)
	 *
	 * @param string $domain Name of the domain (e.g. media, text, etc.)
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the domain item
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem Existing item removed from the given domain or null if item shouldn't be removed
	 * @return \Aimeos\MShop\Common\Item\ListRef\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If given list item isn't found
	 */
	public function deleteListItem( $domain, \Aimeos\MShop\Common\Item\Lists\Iface $listItem, \Aimeos\MShop\Common\Item\Iface $refItem = null )
	{
		if( isset( $this->listItems[$domain] ) )
		{
			foreach( $this->listItems[$domain] as $key => $litem )
			{
				if( $litem === $listItem )
				{
					$this->listRmItems[] = $listItem->setRefItem( $refItem );
					unset( $this->listItems[$domain][$key] );

					return $this;
				}
			}
		}

		throw new \Aimeos\MShop\Exception( sprintf( 'List item for removal from domain "%1$s" not found', $domain ) );
	}


	/**
	 * Removes a list of list items which references their domain items (removed as well if it exists)
	 *
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $items Existing list items
	 * @param boolean $all True to delete referenced items as well, false for list items only
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If an item isn't a list item or isn't found
	 */
	public function deleteListItems( array $items, $all = false )
	{
		foreach( $items as $item )
		{
			\Aimeos\MW\Common\Base::checkClass( '\Aimeos\MShop\Common\Item\Lists\Iface', $item );

			$refItem = ( $all === true ? $item->getRefItem() : null );
			$this->deleteListItem( $item->getDomain(), $item, $refItem );
		}

		return $this;
	}


	/**
	 * Returns the deleted list items which include the domain items if available
	 *
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface[] List items with referenced items attached (optional)
	 */
	public function getListItemsDeleted()
	{
		return $this->listRmItems;
	}


	/**
	 * Returns the list item for the given reference ID, domain and list type
	 *
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param string $listtype Name of the list item type
	 * @param string $refId Unique ID of the referenced item
	 * @param boolean $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface|null Matching list item or null if none
	 */
	public function getListItem( $domain, $listtype, $refId, $active = true )
	{
		if( isset( $this->listItems[$domain] ) )
		{
			foreach( $this->listItems[$domain] as $listItem )
			{
				if( $listItem->getRefId() == $refId && $listItem->getType() === $listtype
					&& ( $active === false || $listItem->isAvailable() )
				) {
					if( isset( $this->listRefItems[$domain][$refId] ) ) {
						$listItem->setRefItem( $this->listRefItems[$domain][$refId] );
					}

					return $listItem;
				}
			}
		}
	}


	/**
	 * Returns the list items attached, optionally filtered by domain and list type.
	 *
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param array|string|null $domain Name/Names of the domain (e.g. product, text, etc.) or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @param boolean $active True to return only active items, false to return all
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	public function getListItems( $domain = null, $listtype = null, $type = null, $active = true )
	{
		$result = [];
		$this->prepareListItems();

		$iface = '\\Aimeos\\MShop\\Common\\Item\\Typeid\\Iface';
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
				$iface = '\Aimeos\MShop\Common\Item\Typeid\Iface';

				if( $type && ( !$refItem || !($refItem instanceof $iface) || !in_array( $refItem->getType(), $types ) ) ) {
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

		return $result;
	}


	/**
	 * Returns the product, text, etc. items filtered by domain and optionally by type and list type.
	 *
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param array|string|null $domain Name/Names of the domain (e.g. product, text, etc.) or null for all
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
	 * @param boolean $active True to return only active items, false to return all
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function getRefItems( $domain = null, $type = null, $listtype = null, $active = true )
	{
		$list = [];

		foreach( $this->getListItems( $domain, $listtype, $type, $active ) as $listItem )
		{
			if( ( $refItem = $listItem->getRefItem() ) !== null && ( $active === false || $refItem->isAvailable() ) ) {
				$list[$listItem->getDomain()][$listItem->getRefId()] = $refItem;
			}
		}

		if( is_array( $domain ) || $domain === null ) {
			return $list;
		}

		if( isset( $list[$domain] ) ) {
			return $list[$domain];
		}

		return [];
	}


	/**
	 * Returns the label of the item.
	 * This method should be implemented in the derived class if a label column is available.
	 *
	 * @return string Label of the item
	 */
	public function getLabel()
	{
		return '';
	}


	/**
	 * Returns the localized text type of the item or the internal label if no name is available.
	 *
	 * @param string $type Text type to be returned
	 * @return string Specified text type or label of the item
	 */
	public function getName( $type = 'name' )
	{
		$items = $this->getRefItems( 'text', $type );

		if( ( $item = reset( $items ) ) !== false ) {
			return $item->getContent();
		}

		return $this->getLabel();
	}


	/**
	 * Compares the positions of two items for sorting.
	 *
	 * @param \Aimeos\MShop\Common\Item\Position\Iface $a First item
	 * @param \Aimeos\MShop\Common\Item\Position\Iface $b Second item
	 * @return integer -1 if position of $a < $b, 1 if position of $a > $b and 0 if both positions are equal
	 */
	protected function comparePosition( \Aimeos\MShop\Common\Item\Position\Iface $a, \Aimeos\MShop\Common\Item\Position\Iface $b )
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
