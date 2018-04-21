<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\ListRef;


/**
 * Abstract class for items containing referenced list items.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base extends \Aimeos\MShop\Common\Item\Base
{
	private $refItems;
	private $listItems;
	private $rmItems = [];
	private $prepared = false;
	private $max = 0;


	/**
	 * Initializes the item with the given values.
	 *
	 * @param string $prefix Prefix for the keys returned by toArray()
	 * @param array $values Associative list of key/value pairs of the item properties
	 * @param array $listItems Two dimensional associative list of domain / ID / list items that implement \Aimeos\MShop\Common\Item\Lists\Iface
	 * @param array $refItems Two dimensional associative list of domain / ID / domain items that implement \Aimeos\MShop\Common\Item\Iface
	 */
	public function __construct( $prefix, array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( $prefix, $values );

		$this->listItems = $listItems;
		$this->refItems = $refItems;
	}


	/**
	 * Adds a new item to the given domain and references it by a list item
	 *
	 * @param string $domain Name of the domain (e.g. media, text, etc.)
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the new domain item
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem New item added to the given domain or null if no item should be referenced
	 * @return \Aimeos\MShop\Common\Item\ListRef\Iface Self object for method chaining
	 */
	public function addRefItem( $domain, \Aimeos\MShop\Common\Item\Lists\Iface $listItem, \Aimeos\MShop\Common\Item\Iface $refItem = null )
	{
		$id = $listItem->getId() ?: 'tmp-' . $this->max++;
		$this->listItems[$domain][$id] = $listItem->setDomain( $domain )->setRefItem( $refItem );

		if( $refItem !== null )
		{
			$id = $refItem->getId() ?: 'tmp-' . $this->max++;
			$listItem->setRefId( $id );

			$this->refItems[$domain][$id] = $refItem;
		}

		return $this;
	}


	/**
	 * Removes an item from the given domain and its list item referencing it
	 *
	 * @param string $domain Name of the domain (e.g. media, text, etc.)
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface $listItem List item referencing the domain item
	 * @param \Aimeos\MShop\Common\Item\Iface|null $refItem Existing item removed from the given domain or null if item shouldn't be removed
	 * @return \Aimeos\MShop\Common\Item\ListRef\Iface Self object for method chaining
	 */
	public function deleteRefItem( $domain, \Aimeos\MShop\Common\Item\Lists\Iface $listItem, \Aimeos\MShop\Common\Item\Iface $refItem = null )
	{
		if( isset( $this->listItems[$domain] ) )
		{
			foreach( $this->listItems[$domain] as $key => $litem )
			{
				if( $litem === $listItem && $listItem->getDomain() === $domain )
				{
					$this->rmItems[] = $listItem->setRefItem( $refItem );
					unset( $this->listItems[$domain][$key] );

					return $this;
				}
			}
		}

		throw new \Aimeos\MShop\Exception( sprintf( 'List item for removal from domain "%1$s" not found', $domain ) );
	}


	/**
	 * Returns the deleted list items
	 *
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface[] List items with referenced items attached (optional)
	 */
	public function getDeletedItems()
	{
		return $this->rmItems;
	}


	/**
	 * Returns the list item for the given reference ID, domain, list type and type.
	 *
	 * @param string $refId Unique ID of the referenced item
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param string $listtype Name of the list item type
	 * @param boolean $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface|null Matching list item or null if none
	 */
	public function getListItem( $refId, $domain, $listtype, $active = true )
	{
		if( isset( $this->listItems[$domain] ) )
		{
			foreach( $this->listItems[$domain] as $listItem )
			{
				if( $listItem->getRefId() == $refId && $listItem->getType() === $listtype
					&& ( $active === false || $listItem->isAvailable() )
				) {
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
			if( is_array( $domain ) && !in_array( $dname, $domain ) || $domain !== null && $dname !== $domain ) {
				continue;
			}

			foreach( $list as $id => $item )
			{
				if( $listtype && ( !($item instanceof $iface) || !in_array( $item->getType(), $listTypes ) ) ) {
					continue;
				}

				if( $type && ( !($item->getRefItem() instanceof $iface) || !in_array( $item->getRefItem()->getType(), $types ) ) ) {
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
	 * Sorts the list items according to their position value and attaches the referenced item
	 */
	protected function prepareListItems()
	{
		if( $this->prepared === true ) {
			return;
		}

		foreach( $this->listItems as $domain => $list )
		{
			foreach( $list as $listItem )
			{
				$refId = $listItem->getRefId();

				if( isset( $this->refItems[$domain][$refId] ) ) {
					$listItem->setRefItem( $this->refItems[$domain][$refId] );
				}
			}

			uasort( $this->listItems[$domain], array( $this, 'comparePosition' ) );
		}

		$this->prepared = true;
	}
}
