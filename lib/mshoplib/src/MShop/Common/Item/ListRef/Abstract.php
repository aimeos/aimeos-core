<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param string|null $type Name of the list type
	 * @return array List of items implementing MShop_Common_Item_List_Interface
	 */
	public function getListItems( $domain, $type = null )
	{
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

			foreach( $this->_listItems[$domain] as $id => $item )
			{
				if( $item instanceof $iface && $type === $item->getType() ) {
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
	 * Returns the product, text, etc. items, optionally filtered by domain.
	 * The reference parameter in searchItems() must have been set accordingly
	 * to the requested domain to get the items. Otherwise, no items will be
	 * returned by this method.
	 *
	 * @param string $domain Name of the domain (e.g. product, text, etc.)
	 * @param string|null $type Name of the item type
	 * @param string|null $listtype Name of the list item type
	 * @return array List of items implementing MShop_Common_Item_Interface
	 */
	public function getRefItems( $domain, $type = null, $listtype = null )
	{
		if( !isset( $this->_refItems[$domain] ) || !isset( $this->_listItems[$domain] ) ) {
			return array();
		}

		if( !isset( $this->_sortedRefs[$domain] ) )
		{
			$iface = 'MShop_Common_Item_List_Interface';

			foreach( $this->_listItems[$domain] as $listItem )
			{
				$refId = $listItem->getRefId();

				if( isset( $this->_refItems[$domain][$refId] ) && $listItem instanceof $iface )
				{
					$this->_refItems[$domain][$refId]->_listtype = $listItem->getType();
					$this->_refItems[$domain][$refId]->_position = $listItem->getPosition();
				}
			}

			uasort( $this->_refItems[$domain], array( $this, '_compareRefPosition' ) );

			/** @todo: This doesn't work with PHP 5.3 on Solaris -> _listtype gets screwed up if not set again the second time */
			// $this->_sortedRefs[$domain] = true;
		}

		if( $type !== null || $listtype !== null )
		{
			$list = array();
			$iface = 'MShop_Common_Item_Typeid_Interface';

			foreach( $this->_refItems[$domain] as $id => $item )
			{
				if( $item instanceof $iface
					&& ( $type === null || $type === $item->getType() )
					&& ( $listtype === null || $listtype === $item->_listtype ) )
				{
					$list[$id] = $item;
				}
			}
		}
		else
		{
			$list = $this->_refItems[$domain];
		}

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
	 * Returns the localized name of the item or the internal label if no name is available.
	 *
	 * @return string Name or label of the item
	 */
	public function getName()
	{
		$items = $this->getRefItems( 'text', 'name' );

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
