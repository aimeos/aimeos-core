<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds media performance records.
 */
class ProductAddAttributeConfigPerfData extends \Aimeos\MW\Setup\Task\ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddBasePerfData', 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Insert attribute items and product/attribute relations.
	 */
	public function migrate()
	{
		$this->msg( 'Adding product config attribute performance data', 0 );


		$this->txBegin();

		$attrList = $this->getAttributeList();

		$this->txCommit();


		$context = $this->getContext();

		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );
		$productListManager = $productManager->getSubManager( 'lists' );
		$productListTypeManager = $productListManager->getSubManager( 'type' );

		$expr = [];
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare( '==', 'product.lists.type.domain', 'attribute' );
		$expr[] = $search->compare( '==', 'product.lists.type.code', 'config' );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems( $search );

		if( ( $productListTypeItem = reset( $types ) ) === false ) {
			throw new \RuntimeException( 'Product list type item not found' );
		}


		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );
		$search->setSlice( 0, 1000 );

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $productListTypeItem->getId() );
		$listItem->setDomain( 'attribute' );


		$start = 0;

		do
		{
			$result = $productManager->searchItems( $search );

			$this->txBegin();

			foreach( $result as $id => $item )
			{
				$pos = 0;
				foreach( $attrList as $attrId => $attrItem )
				{
					$listItem->setId( null );
					$listItem->setParentId( $id );
					$listItem->setRefId( $attrId );
					$listItem->setPosition( $pos++ );

					$productListManager->saveItem( $listItem, false );
				}
			}

			$this->txCommit();

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, 1000 );
		}
		while( $count == $search->getSliceSize() );


		$this->status( 'done' );
	}


	protected function getAttributeList()
	{
		$context = $this->getContext();

		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$priceTypeManager = \Aimeos\MShop\Factory::createManager( $context, 'price/type' );

		$search = $priceTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.type.domain', 'attribute' ),
			$search->compare( '==', 'price.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $priceTypeManager->searchItems( $search );

		if( ( $priceTypeItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No price type "default" found' );
		}

		$priceItem = $priceManager->createItem();
		$priceItem->setTypeId( $priceTypeItem->getId() );
		$priceItem->setDomain( 'attribute' );
		$priceItem->setTaxRate( '20.00' );
		$priceItem->setStatus( 1 );


		$attrManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$attrTypeManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute/type' );

		$search = $attrTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'option' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attrTypeManager->searchItems( $search );

		if( ( $attrTypeItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No attribute type "option" found' );
		}

		$attrItem = $attrManager->createItem();
		$attrItem->setTypeId( $attrTypeItem->getId() );
		$attrItem->setDomain( 'product' );
		$attrItem->setStatus( 1 );


		$listManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute/lists' );
		$listTypeManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute/lists/type' );

		$search = $listTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.lists.type.domain', 'price' ),
			$search->compare( '==', 'attribute.lists.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $listTypeManager->searchItems( $search );

		if( ( $listTypeItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No price list type "default" found' );
		}

		$listItem = $listManager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'price' );
		$listItem->setStatus( 1 );


		$pos = 0;
		$attrList = [];

		foreach( array( 'small sticker' => '+2.50', 'large sticker' => '+7.50' ) as $option => $price )
		{
			$attrItem->setId( null );
			$attrItem->setCode( $option );
			$attrItem->setLabel( $option );
			$attrItem->setPosition( $pos++ );
			$attrManager->saveItem( $attrItem );

			$attrList[$attrItem->getId()] = clone $attrItem;

			if( $price !== null )
			{
				$priceItem->setId( null );
				$priceItem->setValue( $price );
				$priceItem->setLabel( $option );
				$priceManager->saveItem( $priceItem );

				$listItem->setId( null );
				$listItem->setParentId( $attrItem->getId() );
				$listItem->setRefId( $priceItem->getId() );
				$listManager->saveItem( $listItem, false );
			}
		}

		return $attrList;
	}
}
