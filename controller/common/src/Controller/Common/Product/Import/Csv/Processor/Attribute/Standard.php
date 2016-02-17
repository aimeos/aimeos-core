<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Processor\Attribute;


/**
 * Attribute processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	extends \Aimeos\Controller\Common\Product\Import\Csv\Processor\Base
	implements \Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface
{
	/** controller/common/product/import/csv/processor/attribute/name
	 * Name of the attribute processor implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Controller\Common\Product\Import\Csv\Processor\Attribute\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the processor class name
	 * @since 2015.10
	 * @category Developer
	 */

	private $cache;
	private $listTypes;


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param array $mapping Associative list of field position in CSV as key and domain item key as value
	 * @param \Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface $object Decorated processor
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $mapping,
		\Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface $object = null )
	{
		parent::__construct( $context, $mapping, $object );

		/** controller/common/product/import/csv/processor/attribute/listtypes
		 * Names of the product list types for attributes that are updated or removed
		 *
		 * If you want to associate attribute items manually via the administration
		 * interface to products and don't want these to be touched during the
		 * import, you can specify the product list types for these attributes
		 * that shouldn't be updated or removed.
		 *
		 * @param array|null List of product list type names or null for all
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/common/product/import/csv/domains
		 * @see controller/common/product/import/csv/processor/catalog/listtypes
		 * @see controller/common/product/import/csv/processor/media/listtypes
		 * @see controller/common/product/import/csv/processor/product/listtypes
		 * @see controller/common/product/import/csv/processor/price/listtypes
		 * @see controller/common/product/import/csv/processor/text/listtypes
		 */
		$this->listTypes = $context->getConfig()->get( 'controller/common/product/import/csv/processor/attribute/listtypes');

		$this->cache = $this->getCache( 'attribute' );
	}


	/**
	 * Saves the attribute related data to the storage
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( \Aimeos\MShop\Product\Item\Iface $product, array $data )
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists' );
		$separator = $context->getConfig()->get( 'controller/common/product/import/csv/separator', "\n" );

		$manager->begin();

		try
		{
			$pos = 0;
			$delete = $attrcodes = array();
			$map = $this->getMappedChunk( $data );
			$listItems = $product->getListItems( 'attribute', $this->listTypes );

			foreach( $listItems as $listId => $listItem )
			{
				if( isset( $map[$pos] ) )
				{
					if( !isset( $map[$pos]['attribute.code'] ) || !isset( $map[$pos]['attribute.type'] ) )
					{
						unset( $map[$pos] );
						continue;
					}

					$refItem = $listItem->getRefItem();

					if( $refItem !== null && $map[$pos]['attribute.code'] === $refItem->getCode()
						&& $map[$pos]['attribute.type'] === $refItem->getType()
						&& ( !isset( $map[$pos]['product.lists.type'] ) || isset( $map[$pos]['product.lists.type'] )
						&& $map[$pos]['product.lists.type'] === $listItem->getType() )
					) {
						$pos++;
						continue;
					}
				}

				$listItems[$listId] = null;
				$delete[] = $listId;
				$pos++;
			}

			$listManager->deleteItems( $delete );

			foreach( $map as $pos => $list )
			{
				if( !isset( $list['attribute.code'] ) || $list['attribute.code'] === '' || $list['attribute.type'] === ''
					|| isset( $list['product.lists.type'] ) && $this->listTypes !== null
					&& !in_array( $list['product.lists.type'], (array) $this->listTypes )
				) {
					continue;
				}

				$codes = explode( $separator, $list['attribute.code'] );

				foreach( $codes as $code )
				{
					$attrItem = $this->getAttributeItem( $code, $list['attribute.type'] );
					$attrItem->fromArray( $list );
					$attrItem->setCode( $code );
					$manager->saveItem( $attrItem );

					if( ( $listItem = array_shift( $listItems ) ) === null ) {
						$listItem = $listManager->createItem();
					}

					$typecode = ( isset( $list['product.lists.type'] ) ? $list['product.lists.type'] : 'default' );
					$list['product.lists.typeid'] = $this->getTypeId( 'product/lists/type', 'attribute', $typecode );
					$list['product.lists.refid'] = $attrItem->getId();
					$list['product.lists.parentid'] = $product->getId();
					$list['product.lists.domain'] = 'attribute';

					$listItem->fromArray( $this->addListItemDefaults( $list, $pos ) );
					$listManager->saveItem( $listItem );
				}
			}

			$remaining = $this->getObject()->process( $product, $data );

			$manager->commit();
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			throw $e;
		}

		return $remaining;
	}


	/**
	 * Returns the attribute item for the given code and type
	 *
	 * @param string $code Attribute code
	 * @param string $type Attribute type
	 * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item object
	 */
	protected function getAttributeItem( $code, $type )
	{
		if( ( $item = $this->cache->get( $code, $type ) ) === null )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'attribute' );

			$item = $manager->createItem();
			$item->setTypeId( $this->getTypeId( 'attribute/type', 'product', $type ) );
			$item->setCode( $code );
			$item->setLabel( $type . ' ' . $code );
			$item->setStatus( 1 );

			$manager->saveItem( $item );

			$this->cache->set( $item );
		}

		return $item;
	}
}
