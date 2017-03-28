<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	private $sortedLists;


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
	 * Returns the list items attached, optionally filtered by domain and list type.
	 *
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param string|null $domain Name of the domain (e.g. product, text, etc.) or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	public function getListItems( $domain = null, $listtype = null, $type = null )
	{
		$list = [];
		$this->sortListItems();

		if( $domain === null )
		{
			foreach( $this->listItems as $domain => $items ) {
				$list += $items;
			}

			return $list;
		}

		if( !isset( $this->listItems[$domain] ) ) {
			return [];
		}

		$list = $this->listItems[$domain];

		if( $listtype !== null )
		{
			$list = [];
			$iface = '\\Aimeos\\MShop\\Common\\Item\\Typeid\\Iface';
			$listTypes = ( is_array( $listtype ) ? $listtype : array( $listtype ) );

			foreach( $this->listItems[$domain] as $id => $item )
			{
				if( $item instanceof $iface && in_array( $item->getType(), $listTypes ) ) {
					$list[$id] = $item;
				}
			}
		}

		if( $type !== null )
		{
			$iface = '\\Aimeos\\MShop\\Common\\Item\\Typeid\\Iface';
			$types = ( is_array( $type ) ? $type : array( $type ) );

			foreach( $list as $id => $item )
			{
				if( !( $item->getRefItem() instanceof $iface ) || !in_array( $item->getRefItem()->getType(), $types ) ) {
					unset( $list[$id] );
				}
			}
		}

		return $list;
	}


	/**
	 * Returns the product, text, etc. items filtered by domain and optionally by type and list type.
	 *
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param string|null $domain Name of the domain (e.g. product, text, etc.) or null for all
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function getRefItems( $domain = null, $type = null, $listtype = null )
	{
		if( $domain === null ) {
			return $this->refItems;
		}

		if( !isset( $this->refItems[$domain] ) || !isset( $this->listItems[$domain] ) ) {
			return [];
		}

		$list = [];
		$iface = '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface';
		$types = ( is_array( $type ) ? $type : array( $type ) );
		$listtypes = ( is_array( $listtype ) ? $listtype : array( $listtype ) );

		foreach( $this->listItems[$domain] as $listItem )
		{
			$refId = $listItem->getRefId();

			if( isset( $this->refItems[$domain][$refId] ) && $listItem instanceof $iface
				&& ( $type === null || in_array( $this->refItems[$domain][$refId]->getType(), $types ) )
				&& ( $listtype === null || in_array( $listItem->getType(), $listtypes ) )
			) {
				$list[$refId] = $this->refItems[$domain][$refId];
				$list[$refId]->position = $listItem->getPosition();
			}
		}

		uasort( $list, array( $this, 'compareRefPosition' ) );

		return $list;
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
	 * Compares the positions of two referenced items for sorting.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $a First referenced item
	 * @param \Aimeos\MShop\Common\Item\Iface $b Second referenced item
	 * @return integer -1 if position of $a < $b, 1 if position of $a > $b and 0 if both positions are equal
	 */
	protected function compareRefPosition( \Aimeos\MShop\Common\Item\Iface $a, \Aimeos\MShop\Common\Item\Iface $b )
	{
		if( $a->position === $b->position ) {
			return 0;
		}

		return ( $a->position < $b->position ) ? -1 : 1;
	}


	/**
	 * Sorts the list items according to their position value and attaches the referenced item
	 */
	protected function sortListItems()
	{
		if( isset( $this->sortedLists ) ) {
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

		$this->sortedLists = true;
	}
}
