<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds records to tables.
 */
class MShopAddDataAbstract extends Base
{
	public function up()
	{
	}


	/**
	 * Adds the referenced items from the given entry data.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with stock, attribute, media, price, text and product sections
	 * @param int $idx Position of product
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item Updated item
	 */
	protected function addRefItems( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $entry, int $idx = 0 ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		$context = $this->context();
		$manager = \Aimeos\MShop::create( $context, $item->getResourceType() );

		foreach( ['media', 'price', 'text'] as $refDomain )
		{
			if( isset( $entry[$refDomain] ) )
			{
				$refManager = \Aimeos\MShop::create( $context, $refDomain );

				foreach( $entry[$refDomain] as $index => $data )
				{
					$listItem = $manager->createListItem()->setPosition( $index )->fromArray( $data );
					$refItem = $refManager->create()->fromArray( $data );

					if( isset( $data['property'] ) )
					{
						foreach( (array) $data['property'] as $property )
						{
							$propItem = $manager->createPropertyItem()->fromArray( $property );
							$refItem->addPropertyItem( $propItem );
						}
					}

					$item->addListItem( $refDomain, $listItem, $refItem );
				}
			}
		}

		$this->addAttributes( $item, $entry['attribute'] ?? [] );
		$this->addCategories( $item, $entry['catalog'] ?? [], $idx );
		$this->addSuppliers( $item, $entry['supplier'] ?? [], $idx );
		$this->addProducts( $item, $entry['product'] ?? [] );

		return $item;
	}


	/**
	 * Adds the attributes from the given entry data to the passed item.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with attribute, catalog, media, price, text or product sections
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item Updated item
	 */
	protected function addAttributes( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $entries ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		$context = $this->context();
		$refManager = \Aimeos\MShop::create( $context, 'attribute' );
		$manager = \Aimeos\MShop::create( $context, $item->getResourceType() );

		foreach( $entries as $idx => $data )
		{
			$listItem = $manager->createListItem()->setPosition( $idx )->fromArray( $data );
			$refItem = $refManager->create()->fromArray( $data );

			try {
				$refItem = $refManager->find( $refItem->getCode(), [], $item->getResourceType(), $refItem->getType() );
			} catch( \Exception $e ) { ; } // if not found, use the new item

			$refItem = $this->addRefItems( $refItem, $data );
			$item->addListItem( 'attribute', $listItem, $refItem );
		}

		return $item;
	}


	/**
	 * Adds the categories from the given entry data to the passed item.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with attribute, catalog, media, price, text or product sections
	 * @param int $idx Position of product
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item Updated item
	 */
	protected function addCategories( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $entries, int $idx ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		$context = $this->context();
		$refManager = \Aimeos\MShop::create( $context, 'catalog' );
		$manager = \Aimeos\MShop::create( $context, $item->getResourceType() );

		foreach( $entries as $data )
		{
			$listItem = $manager->createListItem()->setPosition( $idx )->fromArray( $data );
			$refItem = $refManager->find( $data['catalog.code'] );

			$item->addListItem( 'catalog', $listItem, $refItem );
		}

		return $item;
	}


	/**
	 * Adds the products from the given entry data to the passed item.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with attribute, catalog, media, price, text or product sections
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item Updated item
	 */
	protected function addProducts( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $entries ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		$context = $this->context();
		$refManager = \Aimeos\MShop::create( $context, 'product' );
		$manager = \Aimeos\MShop::create( $context, $item->getResourceType() );

		foreach( $entries as $idx => $data )
		{
			$listItem = $manager->createListItem()->setPosition( $idx )->fromArray( $data );
			$refItem = $refManager->find( $data['product.code'] );

			$item->addListItem( 'product', $listItem->setRefId( $refItem->getId() ) );
		}

		return $item;
	}


	/**
	 * Adds the suppliers from the given entry data to the passed item.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with list items
	 * @param array $entry Associative list of data with attribute, catalog, media, price, text or product sections
	 * @param int $idx Position of product
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item Updated item
	 */
	protected function addSuppliers( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, array $entries, int $idx ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
	{
		$context = $this->context();
		$refManager = \Aimeos\MShop::create( $context, 'supplier' );
		$manager = \Aimeos\MShop::create( $context, $item->getResourceType() );

		foreach( $entries as $data )
		{
			$listItem = $manager->createListItem()->setPosition( $idx )->fromArray( $data );
			$refItem = $refManager->find( $data['supplier.code'] );

			$item->addListItem( 'supplier', $listItem, $refItem );
		}

		return $item;
	}


	/**
	 * Removes the referenced items from the given items.
	 *
	 * @param \Aimeos\Map $items List of items
	 * @param array $domains List of domain names
	 */
	public function removeRefItems( \Aimeos\Map $items, array $domains )
	{
		$context = $this->context();

		foreach( $domains as $domain )
		{
			$rmItems = map();

			foreach( $items as $item ) {
				$rmItems->merge( $item->getRefItems( $domain, null, null, false )->filter( function( $item ) {
					return strncmp( $item->getLabel(), 'Demo', 4 ) === 0;
				} ) );
			}

			\Aimeos\MShop::create( $context, $domain )->delete( $rmItems );
		}
	}
}
