<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Abstract class for items containing referenced list items.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Item_ListRef_Abstract extends MShop_Common_Item_Abstract
{
	private $_listItems;
	private $_refItems;
	private $_sortedLists = array();
	private $_sortedRefs = array();


	/**
	 * Initializes the item with the given values.
	 *
	 * @param string $prefix Prefix for the keys returned by toArray()
	 * @param array $values Associative list of key/value pairs of the item properties
	 * @param array $listItems Two dimensional associative list of domain / ID / list items that implement MShop_Common_Item_List_Interface
	 * @param array $refItems Two dimensional associative list of domain / ID / domain items that implement MShop_Common_Item_Interface
	 */
	public function __construct( $prefix, array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( $prefix, $values );

		$this->_listItems = $listItems;
		$this->_refItems = $refItems;
	}


	/**
	 * Returns the list items attached, optionally filtered by domain and list type.
	 *
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param string|null $domain Name of the domain (e.g. product, text, etc.) or null for all
	 * @param array|string|null $type Name/Names of the list item type or null for all
	 * @return array List of items implementing MShop_Common_Item_List_Interface
	 */
	public function getListItems( $domain = null, $type = null )
	{
		if( $domain === null )
		{
			$listItems = array();

			foreach( $this->_listItems as $domain => $items ) {
				$listItems += $items;
			}

			return $listItems;
		}

		if( !isset( $this->_listItems[$domain] ) ) {
			return array();
		}

		if( !isset( $this->_sortedLists[$domain] ) )
		{
			foreach( $this->_listItems[$domain] as $listItem )
			{
				$refId = $listItem->getRefId();

				if( isset( $this->_refItems[$domain][$refId] ) ) {
					$listItem->setRefItem( $this->_refItems[$domain][$refId] );
				}
			}

			uasort( $this->_listItems[$domain], array( $this, '_comparePosition' ) );
			$this->_sortedLists[$domain] = true;
		}

		if( $type !== null )
		{
			$list = array();
			$iface = 'MShop_Common_Item_Typeid_Interface';
			$listTypes = ( is_array( $type ) ? $type : array( $type ) );

			foreach( $this->_listItems[$domain] as $id => $item )
			{
				if( $item instanceof $iface && in_array( $item->getType(), $listTypes ) ) {
					$list[$id] = $item;
				}
			}
		}
		else
		{
			$list = $this->_listItems[$domain];
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
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param array|string|null $type Name/Names of the item type or null for all
	 * @param array|string|null $listtype Name/Names of the list item type or null for all
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	public function getRefItems( $domain, $type = null, $listtype = null )
	{
		if( !isset( $this->_refItems[$domain] ) || !isset( $this->_listItems[$domain] ) ) {
			return array();
		}

		$list = array();
		$iface = 'MShop_Common_Item_List_Interface';
		$types = ( is_array( $type ) ? $type : array( $type ) );
		$listtypes = ( is_array( $listtype ) ? $listtype : array( $listtype ) );

		foreach( $this->_listItems[$domain] as $listItem )
		{
			$refId = $listItem->getRefId();

			if( isset( $this->_refItems[$domain][$refId] ) && $listItem instanceof $iface
				&& ( $type === null || in_array( $this->_refItems[$domain][$refId]->getType(), $types ) )
				&& ( $listtype === null || in_array( $listItem->getType(), $listtypes ) )
			) {
				$list[$refId] = $this->_refItems[$domain][$refId];
				$list[$refId]->_position = $listItem->getPosition();
			}
		}

		uasort( $list, array( $this, '_compareRefPosition' ) );

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
	 * @return string Specified text type or label of the item
	 */
	public function getName( $type = 'name' )
	{
		$items = $this->getRefItems( 'text', $type );

		if ( ( $item = reset( $items ) ) !== false ) {
			return $item->getContent();
		}

		return $this->getLabel();
	}


	/**
	 * Compares the positions of two items for sorting.
	 *
	 * @param MShop_Common_Item_Position_Interface $a First item
	 * @param MShop_Common_Item_Position_Interface $b Second item
	 * @return integer -1 if position of $a < $b, 1 if position of $a > $b and 0 if both positions are equal
	 */
	protected function _comparePosition( MShop_Common_Item_Position_Interface $a, MShop_Common_Item_Position_Interface $b )
	{
		if( $a->getPosition() === $b->getPosition() ) {
			return 0;
		}

		return ( $a->getPosition() < $b->getPosition() ) ? -1 : 1;
	}


	/**
	 * Compares the positions of two referenced items for sorting.
	 *
	 * @param MShop_Common_Item_Interface $a First referenced item
	 * @param MShop_Common_Item_Interface $b Second referenced item
	 * @return integer -1 if position of $a < $b, 1 if position of $a > $b and 0 if both positions are equal
	 */
	protected function _compareRefPosition( MShop_Common_Item_Interface $a, MShop_Common_Item_Interface $b )
	{
		if( $a->_position === $b->_position ) {
			return 0;
		}

		return ( $a->_position < $b->_position ) ? -1 : 1;
	}
}
