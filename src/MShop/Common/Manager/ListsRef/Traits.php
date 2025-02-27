<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\ListsRef;


/**
 * Trait for managers working with referenced list items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Attribute\Item\Iface New attribute item object
	 */
	abstract public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	abstract protected function context() : \Aimeos\MShop\ContextIface;

	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	abstract protected function object() : \Aimeos\MShop\Common\Manager\Iface;


	/**
	 * Creates a new lists item object
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface New list items object
	 */
	public function createListItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Lists\Iface
	{
		$domain = $this->domain();
		$context = $this->context();

		$values['.date'] = $context->datetime();
		$values[$domain . '.lists.siteid'] = $values[$domain . '.lists.siteid'] ?? $context->locale()->getSiteId();

		return new \Aimeos\MShop\Common\Item\Lists\Standard( $domain . '.lists.', $values );
		return $this->object()->getSubManager( 'lists' )->create( $values );
	}


	/**
	 * Removes the items referenced by the given list items.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface[]|\Aimeos\Map|array $items List of items with deleted list items
	 * @return \Aimeos\MShop\Common\Manager\ListsRef\Iface Manager object for method chaining
	 */
	protected function deleteRefItems( $items ) : \Aimeos\MShop\Common\Manager\ListsRef\Iface
	{
		if( ( $items = map( $items ) )->isEmpty() ) {
			return $this;
		}

		$map = [];

		foreach( $items as $item )
		{
			if( $item instanceof \Aimeos\MShop\Common\Item\ListsRef\Iface )
			{
				foreach( $item->getListItemsDeleted() as $listItem )
				{
					if( $listItem->getRefItem() ) {
						$map[$listItem->getDomain()][] = $listItem->getRefId();
					}
				}
			}
		}

		foreach( $map as $domain => $ids ) {
			\Aimeos\MShop::create( $this->context(), $domain )->begin()->delete( $ids )->commit();
		}

		return $this;
	}


	/**
	 * Returns the list items that belong to the given parent item IDs.
	 *
	 * @param string[] $parentIds List of parent item IDs
	 * @param string[] $ref List of domain names whose referenced items should be attached
	 * @param string $domain Domain prefix
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface with IDs as keys
	 */
	protected function getListItems( array $parentIds, array $ref, string $domain ) : array
	{
		if( empty( $ref ) ) {
			return [];
		}

		$manager = $this->object()->getSubManager( 'lists' );
		$search = $manager->filter()->slice( 0, 0x7fffffff );

		$list = [];
		$len = strlen( $domain );
		$expr = [$search->compare( '==', $domain . '.lists.parentid', $parentIds )];

		foreach( $ref as $key => $type )
		{
			if( is_array( $type ) )
			{
				$key = !strncmp( $key, $domain . '/', $len + 1 ) ? [$key, substr( $key, $len + 1 )] : $key; // remove prefix

				$list[] = $search->and( [
					$search->compare( '==', $domain . '.lists.domain', $key ),
					$search->compare( '==', $domain . '.lists.type', $type ),
				] );
			}
			else
			{
				$type = !strncmp( $type, $domain . '/', $len + 1 ) ? [$type, substr( $type, $len + 1 )] : $type; // remove prefix
				$list[] = $search->compare( '==', $domain . '.lists.domain', $type );
			}
		}

		if( !empty( $list ) ) {
			$expr[] = $search->or( $list );
		}

		return $manager->search( $search->add( $search->and( $expr ) ), $ref )
			->uasort( fn( $a, $b ) => $a->getPosition() <=> $b->getPosition() )
			->all();
	}


	/**
	 * Adds new, updates existing and deletes removed list items and referenced items if available
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with referenced items
	 * @param string $domain Domain of the calling manager
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item with updated referenced items
	 */
	protected function saveListItems( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, string $domain,
		bool $fetch = true ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		$context = $this->context();
		$rmListItems = $rmItems = $refManager = [];
		$listManager = $this->object()->getSubManager( 'lists' );


		foreach( $item->getListItemsDeleted() as $listItem )
		{
			$rmListItems[] = $listItem;

			if( ( $refItem = $listItem->getRefItem() ) !== null ) {
				$rmItems[$listItem->getDomain()][] = $refItem->getId();
			}
		}


		try
		{
			foreach( $rmItems as $refDomain => $list )
			{
				$refManager[$refDomain] = \Aimeos\MShop::create( $context, $refDomain );
				$refManager[$refDomain]->begin();

				$refManager[$refDomain]->delete( $list );
			}

			$listManager->delete( $rmListItems );


			foreach( $item->getListItems( null, null, null, false ) as $listItem )
			{
				$refDomain = $listItem->getDomain();

				if( ( $refItem = $listItem->getRefItem() ) !== null )
				{
					if( !isset( $refManager[$refDomain] ) )
					{
						$refManager[$refDomain] = \Aimeos\MShop::create( $context, $refDomain );
						$refManager[$refDomain]->begin();
					}

					$refItem = $refManager[$refDomain]->save( $refItem );
					$listItem->setRefId( $refItem->getId() );
				}

				if( $listItem->getParentId() && $listItem->getParentId() != $item->getId() ) {
					$listItem->setId( null ); // create new list item if copied
				}

				$listManager->save( $listItem->setParentId( $item->getId() ), $fetch );
			}


			foreach( $refManager as $manager ) {
				$manager->commit();
			}
		}
		catch( \Exception $e )
		{
			foreach( $refManager as $manager ) {
				$manager->rollback();
			}

			throw $e;
		}

		return $item;
	}
}
