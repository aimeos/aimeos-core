<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds media performance records.
 */
class MW_Setup_Task_ProductAddAttributeConfigPerfData extends MW_Setup_Task_ProductAddBasePerfData
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
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Insert attribute items and product/attribute relations.
	 */
	protected function _process()
	{
		$this->_msg('Adding product config attribute performance data', 0);


		$this->_txBegin();

		$attrList = $this->_getAttributeList();

		$this->_txCommit();


		$context =  $this->_getContext();

		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$productListManager = $productManager->getSubManager( 'list' );
		$productListTypeManager = $productListManager->getSubManager( 'type' );

		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.list.type.domain', 'attribute');
		$expr[] = $search->compare('==', 'product.list.type.code', 'config');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems($search);

		if ( ($productListTypeItem = reset($types)) === false) {
			throw new Exception('Product list type item not found');
		}


		$search = $productManager->createSearch();
		$search->setSortations( array( $search->sort( '+', 'product.id' ) ) );

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $productListTypeItem->getId() );
		$listItem->setDomain( 'attribute' );


		$this->_txBegin();

		$start = 0;

		do
		{
			$result = $productManager->searchItems( $search );

			foreach ( $result as $id => $item )
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

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$this->_txCommit();


		$this->_status( 'done' );
	}


	protected function _getAttributeList()
	{
		$context =  $this->_getContext();

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$priceTypeManager = MShop_Factory::createManager( $context, 'price/type' );

		$search = $priceTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.type.domain', 'attribute' ),
			$search->compare( '==', 'price.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $priceTypeManager->searchItems( $search );

		if( ( $priceTypeItem = reset( $result ) ) === false ) {
			throw new Exception( 'No price type "default" found' );
		}

		$priceItem = $priceManager->createItem();
		$priceItem->setTypeId( $priceTypeItem->getId() );
		$priceItem->setDomain( 'attribute' );
		$priceItem->setTaxRate( '20.00' );
		$priceItem->setStatus( 1 );


		$attrManager = MShop_Factory::createManager( $context, 'attribute' );
		$attrTypeManager = MShop_Factory::createManager( $context, 'attribute/type' );

		$search = $attrTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'option' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attrTypeManager->searchItems( $search );

		if( ( $attrTypeItem = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute type "option" found' );
		}

		$attrItem = $attrManager->createItem();
		$attrItem->setTypeId( $attrTypeItem->getId() );
		$attrItem->setDomain( 'product' );
		$attrItem->setStatus( 1 );


		$listManager = MShop_Factory::createManager( $context, 'attribute/list' );
		$listTypeManager = MShop_Factory::createManager( $context, 'attribute/list/type' );

		$search = $listTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.list.type.domain', 'price' ),
			$search->compare( '==', 'attribute.list.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $listTypeManager->searchItems( $search );

		if( ( $listTypeItem = reset( $result ) ) === false ) {
			throw new Exception( 'No price list type "default" found' );
		}

		$listItem = $listManager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'price' );
		$listItem->setStatus( 1 );


		$pos = 0;
		$attrList = array();

		foreach( array( 'small sticker' => '+2.50', 'large sticker' => '+7.50' ) as $option => $price )
		{
			$attrItem->setId( null );
			$attrItem->setCode( $option );
			$attrItem->setLabel( $option );
			$attrItem->setPosition( $pos++ );
			$attrManager->saveItem( $attrItem );

			$attrList[ $attrItem->getId() ] = clone $attrItem;

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
