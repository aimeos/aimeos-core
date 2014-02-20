<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds selection performance records to product table.
 */
class MW_Setup_Task_ProductAddSelectPerfData extends MW_Setup_Task_ProductAddBasePerfData
{
	private $_count = 10000;


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array(
			'LocaleAddPerfData', 'ProductAddBasePerfData', 'ProductAddTextPerfData',
			'ProductAddPricePerfData', 'ProductAddStockPerfData'
		);
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'ProductAddMediaPerfData', 'CatalogRebuildPerfIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		$this->_process();
	}


	/**
	 * Insert product data.
	 */
	protected function _process()
	{
		$this->_msg('Adding product selection performance data', 0);


		$this->_txBegin();

		$selProducts = $this->_getSelectionProductIds();

		$this->_txCommit();


		$productTypeManager = MShop_Factory::createManager( $this->_getContext(), 'product/type' );

		$expr = array();
		$search = $productTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.type.domain', 'product');
		$expr[] = $search->compare('==', 'product.type.code', 'default');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productTypeManager->searchItems($search);

		if ( ($productTypeItem = reset($types)) === false) {
			throw new Exception('Product type item not found');
		}


		$productListTypeManager = MShop_Factory::createManager( $this->_getContext(), 'product/list/type' );

		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.list.type.code', 'default');
		$expr[] = $search->compare('==', 'product.list.type.domain', 'product');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems($search);

		if ( ($listTypeItem = reset($types)) === false) {
			throw new Exception('Product list type item not found');
		}


		$productListManager = MShop_Factory::createManager( $this->_getContext(), 'product/list' );

		$listItem = $productListManager->createItem();
		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'product' );


		$productManager = MShop_Factory::createManager( $this->_getContext(), 'product' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.typeid', $productTypeItem->getId() ) );
		$search->setSortations( array( $search->sort( '-', 'product.id' ) ) );


		$this->_txBegin();

		$selCount = count( $selProducts );
		$selPrices = array();
		$start = $num = 0;

		do
		{
			$result = $productManager->searchItems( $search, array( 'price' ) );

			foreach( $result as $id => $product )
			{
				$pos = (int) ( ($num / 9) % $selCount );
				$prices = $product->getRefItems( 'price', 'default', 'default' );
				$selPrices[$pos] = $this->_getLowestPrice( ( isset( $selPrices[$pos] ) ? $selPrices[$pos] : null ), $prices );

				$listItem->setId( null );
				$listItem->setParentId( $selProducts[$pos] );
				$listItem->setRefId( $id );
				$productListManager->saveItem( $listItem, false );


				$pos = (int) ( ($num / 9 + 1) % $selCount );
				$prices = $product->getRefItems( 'price', 'default', 'default' );
				$selPrices[$pos] = $this->_getLowestPrice( ( isset( $selPrices[$pos] ) ? $selPrices[$pos] : null ), $prices );

				$listItem->setId( null );
				$listItem->setParentId( $selProducts[$pos] );
				$listItem->setRefId( $id );
				$productListManager->saveItem( $listItem, false );


				$pos = (int) ( ($num / 9 + 2) % $selCount );
				$prices = $product->getRefItems( 'price', 'default', 'default' );
				$selPrices[$pos] = $this->_getLowestPrice( ( isset( $selPrices[$pos] ) ? $selPrices[$pos] : null ), $prices );

				$listItem->setId( null );
				$listItem->setParentId( $selProducts[$pos] );
				$listItem->setRefId( $id );
				$productListManager->saveItem( $listItem, false );

				$num++;
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );

		$this->_txCommit();


		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.list.type.code', 'default');
		$expr[] = $search->compare('==', 'product.list.type.domain', 'price');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems($search);

		if ( ($listTypeItem = reset($types)) === false) {
			throw new Exception('Product list type item not found');
		}

		$listItem->setTypeId( $listTypeItem->getId() );
		$listItem->setDomain( 'price' );

		$this->_txBegin();

		foreach( $selPrices as $pos => $priceItem )
		{
			$listItem->setId( null );
			$listItem->setRefId( $priceItem->getId() );
			$listItem->setParentId( $selProducts[$pos] );
			$productListManager->saveItem( $listItem, false );
		}

		$this->_txCommit();


		$this->_status( 'done' );
	}


	protected function _getLowestPrice( MShop_Price_Item_Interface $price = null, array $prices = array() )
	{
		foreach( $prices as $item )
		{
			if( $price === null )
			{
				$price = $item;
				continue;
			}

			if( $price !== null && $item->getValue() < $price->getValue() ) {
				$price = $item;
			}
		}

		return $price;
	}


	protected function _getSelectionProductIds()
	{
		$context = $this->_getContext();


		$textTypeManager = MShop_Factory::createManager( $context, 'text/type' );

		$search = $textTypeManager->createSearch();
		$expr = array(
			$search->compare('==', 'text.type.domain', 'product'),
			$search->combine( '||', array(
				$search->compare('==', 'text.type.code', 'short'),
				$search->compare('==', 'text.type.code', 'long'),
			) ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		foreach( $textTypeManager->searchItems($search) as $item ) {
			$textTypeItems[ $item->getCode() ] = $item;
		}

		if( count( $textTypeItems ) !== 2 ) {
			throw new Exception( 'Text type items not found' );
		}


		$textManager = MShop_Factory::createManager( $context, 'text' );

		$textItem = $textManager->createItem();
		$textItem->setDomain( 'product' );
		$textItem->setStatus( 1 );


		$productListTypeManager = MShop_Factory::createManager( $context, 'product/list/type' );

		$expr = array();
		$search = $productListTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.list.type.domain', 'text');
		$expr[] = $search->compare('==', 'product.list.type.code', 'default');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productListTypeManager->searchItems($search);

		if ( ($productListTypeItem = reset($types)) === false) {
			throw new Exception('Product list type item not found');
		}


		$productListManager = MShop_Factory::createManager( $context, 'product/list' );

		$productListItem = $productListManager->createItem();
		$productListItem->setTypeId( $productListTypeItem->getId() );
		$productListItem->setDomain( 'text' );
		$productListItem->setStatus( 1 );


		$productTypeManager = MShop_Factory::createManager( $context, 'product/type' );

		$expr = array();
		$search = $productTypeManager->createSearch();
		$expr[] = $search->compare('==', 'product.type.domain', 'product');
		$expr[] = $search->compare('==', 'product.type.code', 'select');
		$search->setConditions( $search->combine( '&&', $expr ) );
		$types = $productTypeManager->searchItems($search);

		if ( ($productTypeItem = reset($types)) === false) {
			throw new Exception('Product type item not found');
		}


		$productManager = MShop_Factory::createManager( $context, 'product' );

		$productItem = $productManager->createItem();
		$productItem->setTypeId( $productTypeItem->getId() );
		$productItem->setSupplierCode( 'My selection brand' );
		$productItem->setDateStart( '1970-01-01 00:00:00' );
		$productItem->setStatus( 1 );


		$selProducts = array();

		for( $i = 0; $i < $this->_count; $i++ )
		{
			$productItem->setId( null );
			$productItem->setCode( 'perf-select-' . str_pad( $i, 5, '0', STR_PAD_LEFT ) );
			$productItem->setLabel( 'Selection product ' . ($i+1) );
			$productManager->saveItem( $productItem );

			$selProducts[] = $productItem->getId();


			$textItem->setId( null );
			$textItem->setTypeId( $textTypeItems['short']->getId() );
			$textItem->setLabel( 'Short description for ' . ($i+1) . '. selection product' );
			$textItem->setContent( 'Short description for ' . ($i+1) . '. selection product' );
			$textManager->saveItem( $textItem );

			$productListItem->setId( null );
			$productListItem->setParentId( $productItem->getId() );
			$productListItem->setRefId( $textItem->getId() );
			$productListManager->saveItem( $productListItem, false );


			$textItem->setId( null );
			$textItem->setTypeId( $textTypeItems['long']->getId() );
			$textItem->setLabel( 'Long description for ' . ($i+1) . '. selection product' );
			$textItem->setContent( 'Long description for ' . ($i+1) . '. selection product. This may contain some "Lorem ipsum" text' );
			$textManager->saveItem( $textItem );

			$productListItem->setId( null );
			$productListItem->setParentId( $productItem->getId() );
			$productListItem->setRefId( $textItem->getId() );
			$productListManager->saveItem( $productListItem, false );
		}

		return $selProducts;
	}
}
