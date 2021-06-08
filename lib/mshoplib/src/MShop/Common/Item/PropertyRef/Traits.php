<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\PropertyRef;


/**
 * Common trait for items containing property items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	private $propItems = [];
	private $propRmItems = [];
	private $propMax = 0;


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		parent::__clone();

		foreach( $this->propItems as $key => $item ) {
			$this->propItems[$key] = clone $item;
		}

		foreach( $this->propRmItems as $key => $item ) {
			$this->propRmItems[$key] = clone $item;
		}
	}


	/**
	 * Adds a new property item or overwrite an existing one
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface $item New or existing property item
	 * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
	 */
	public function addPropertyItem( \Aimeos\MShop\Common\Item\Property\Iface $item ) : \Aimeos\MShop\Common\Item\PropertyRef\Iface
	{
		$id = $item->getId() ?: '_' . $this->getId() . '_' . $item->getType() . '_' . $item->getLanguageId() . '_' . $item->getValue();

		unset( $this->propItems[$id] ); // append at the end
		$this->propItems[$id] = $item;

		return $this;
	}


	/**
	 * Adds new property items or overwrite existing ones
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Property\Iface $item New or existing property item
	 * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
	 */
	public function addPropertyItems( iterable $items ) : \Aimeos\MShop\Common\Item\PropertyRef\Iface
	{
		foreach( $items as $item ) {
			$this->addPropertyItem( $item );
		}

		return $this;
	}


	/**
	 * Removes an existing property item
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface $item Existing property item
	 * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
	 */
	public function deletePropertyItem( \Aimeos\MShop\Common\Item\Property\Iface $item ) : \Aimeos\MShop\Common\Item\PropertyRef\Iface
	{
		$id = $item->getId();

		if( isset( $this->propItems[$id] ) )
		{
			$this->propRmItems[$id] = $item;
			unset( $this->propItems[$id] );
			return $this;
		}

		$id = '_' . $this->getId() . '_' . $item->getType() . '_' . $item->getLanguageId() . '_' . $item->getValue();

		if( isset( $this->propItems[$id] ) )
		{
			$this->propRmItems[$id] = $item;
			unset( $this->propItems[$id] );
		}

		return $this;
	}


	/**
	 * Removes a list of existing property items
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Property\Iface[] $items Existing property items
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If an item isn't a property item or isn't found
	 */
	public function deletePropertyItems( iterable $items ) : \Aimeos\MShop\Common\Item\PropertyRef\Iface
	{
		foreach( $items as $item ) {
			$this->deletePropertyItem( $item );
		}

		return $this;
	}


	/**
	 * Returns the deleted property items
	 *
	 * @return \Aimeos\Map Property items implementing \Aimeos\MShop\Common\Item\Property\Iface
	 */
	public function getPropertyItemsDeleted() : \Aimeos\Map
	{
		return map( $this->propRmItems );
	}


	/**
	 * Returns the property values for the given type
	 *
	 * @param string $type Type of the properties
	 * @return \Aimeos\Map List of property values
	 */
	public function getProperties( string $type ) : \Aimeos\Map
	{
		$list = [];

		foreach( $this->getPropertyItems( $type ) as $id => $item ) {
			$list[$id] = $item->getValue();
		}

		return map( $list );
	}


	/**
	 * Returns the property item for the given type, language and value
	 *
	 * @param string $type Name of the property type
	 * @param string|null $langId ISO language code (e.g. "en" or "en_US") or null if not language specific
	 * @param string $value Value of the property
	 * @param bool $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Common\Item\Property\Iface|null Matching property item or null if none
	 */
	public function getPropertyItem( string $type, ?string $langId, string $value, bool $active = true ) : ?\Aimeos\MShop\Common\Item\Property\Iface
	{
		foreach( $this->propItems as $propItem )
		{
			if( $propItem->getType() === $type && $propItem->getLanguageId() === $langId
				&& $propItem->getValue() === $value && ( $active === false || $propItem->isAvailable() )
			) {
				return $propItem;
			}
		}

		return null;
	}


	/**
	 * Returns the property items of the product
	 *
	 * @param array|string|null $type Name of the property item type or null for all
	 * @param bool $active True to return only active items, false to return all
	 * @return \Aimeos\Map List of property IDs as keys and property items implementing \Aimeos\MShop\Common\Item\Property\Iface
	 */
	public function getPropertyItems( $type = null, bool $active = true ) : \Aimeos\Map
	{
		$list = [];

		foreach( $this->propItems as $propId => $propItem )
		{
			if( ( $type === null || in_array( $propItem->getType(), (array) $type ) )
				&& ( $active === false || $propItem->isAvailable() )
			) {
				$list[$propId] = $propItem;
			}
		}

		return map( $list );
	}


	/**
	 * Adds a new property item or overwrite an existing one
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Property\Iface[] $items New list of property items
	 * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Self object for method chaining
	 */
	public function setPropertyItems( iterable $items ) : \Aimeos\MShop\Common\Item\PropertyRef\Iface
	{
		$list = [];

		foreach( $items as $p )
		{
			$id = $p->getId() ?: '_' . $this->getId() . '_' . $p->getType() . '_' . $p->getLanguageId() . '_' . $p->getValue();
			unset( $this->propItems[$id] );
			$list[$id] = $p;
		}

		$this->deletePropertyItems( $this->propItems );
		$this->propItems = $list;

		return $this;
	}


	/**
	 * Returns the unique ID of the item.
	 *
	 * @return string|null ID of the item
	 */
	abstract public function getId() : ?string;


	/**
	 * Sets the property items in the trait
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $items Property items
	 */
	protected function initPropertyItems( array $items )
	{
		$this->propItems = $items;
	}
}
