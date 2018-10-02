<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\ListRef;


/**
 * Abstract class for managers working with referenced list items.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	use \Aimeos\MShop\Common\Manager\ListRef\Traits;


	private $typeIds = [];


	/**
	 * Updates the list items to the referenced items for the given domain and type
	 *
	 * @param \Aimeos\MShop\Common\Item\ListRef\Iface $item Item including references to other domain items
	 * @param array $map Associative list of reference ID as key and list of list item properties as key/value pairs
	 * @param string $domain Domain name of the referenced items
	 * @param string $type List type for the referenced items
	 * @deprecated Use list/ref item methods instead
	 */
	public function updateListItems( \Aimeos\MShop\Common\Item\ListRef\Iface $item, array $map, $domain, $type )
	{
		$listManager = $this->getObject()->getSubManager( 'lists' );

		if( !isset( $this->typeIds[$domain][$type] ) )
		{
			$typeManager = $listManager->getSubManager( 'type' );
			$this->typeIds[$domain][$type] = $typeManager->findItem( $type, [], $domain )->getId();
		}

		$listItem = $listManager->createItem();
		$listItem->setTypeId( $this->typeIds[$domain][$type] );
		$listItem->setParentId( $item->getId() );
		$listItem->setDomain( $domain );
		$listItem->setStatus( 1 );

		$pos = 0;
		$listRef = $ids = [];
		$listItems = $item->getListItems( $domain, $type );

		foreach( $listItems as $id => $listItem ) {
			$listRef[$listItem->getRefId()] = $id;
		}

		foreach( $map as $id => $values )
		{
			$copy = $listItem;

			if( !isset( $listRef[$id] ) )
			{
				$copy->setId( null );
				$copy->setRefId( $id );
			}
			else
			{
				$copy = $listItems[$listRef[$id]];
				$ids[] = $listRef[$id];
			}

			if( is_array( $values ) ) {
				$copy->fromArray( $values );
			}

			$copy->setPosition( $pos++ );
			$listManager->saveItem( $copy, false );
		}

		$listManager->deleteItems( array_diff( $listRef, $ids ) );
	}
}
