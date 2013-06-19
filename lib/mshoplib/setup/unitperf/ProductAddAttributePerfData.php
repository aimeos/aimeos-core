<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds media performance records.
 */
class MW_Setup_Task_ProductAddAttributePerfData extends MW_Setup_Task_ProductAddBasePerfData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return array List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'ProductAddBasePerfData', 'MShopAddTypeDataUnitperf' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
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
		$this->_msg('Adding product attribute performance data', 0);


		$this->_txBegin();

		$context =  $this->_getContext();

		$attrManager = MShop_Attribute_Manager_Factory::createManager( $context );
		$attrTypeManager = $attrManager->getSubManager( 'type' );

		$attrTypeItem = $attrTypeManager->createItem();
		$attrTypeItem->setDomain( 'product' );
		$attrTypeItem->setCode( 'unitperf-size' );
		$attrTypeItem->setLabel( 'Size' );
		$attrTypeItem->setStatus( 1 );

		$attrTypeManager->saveItem( $attrTypeItem );

		$attrItem = $attrManager->createItem();
		$attrItem->setTypeId( $attrTypeItem->getId() );
		$attrItem->setDomain( 'product' );
		$attrItem->setStatus( 1 );

		$pos = 0;
		$attrList = array();

		foreach( array( 'xs', 's', 'm', 'l', 'xl' ) as $size )
		{
			$attrItem->setId( null );
			$attrItem->setCode( $size );
			$attrItem->setLabel( $size );
			$attrItem->setPosition( $pos++ );

			$attrManager->saveItem( $attrItem );

			$attrList[ $attrItem->getId() ] = clone $attrItem;
		}

		$this->_txCommit();


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
		while( $count > 0 );

		$this->_txCommit();


		$this->_status( 'done' );
	}
}
