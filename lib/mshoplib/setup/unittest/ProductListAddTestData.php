<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds product test data and all items from other domains.
 */
class MW_Setup_Task_ProductListAddTestData extends MW_Setup_Task_Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'MediaListAddTestData', 'PriceListAddTestData', 'ProductAddTestData', 'ProductAddTagTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies()
	{
		return array( 'CatalogRebuildTestIndex' );
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds product test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding product-list test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'product-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$refKeys = array();
		foreach( $testdata['product/list'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $this->additional, 'Default' );

		$refIds = array();
		$refIds['attribute'] = $this->getAttributeData( $refKeys['attribute'] );
		$refIds['media'] = $this->getMediaData( $refKeys['media'] );
		$refIds['price'] = $this->getPriceData( $refKeys['price'] );
		$refIds['text'] = $this->getTextData( $refKeys['text'] );
		$refIds['product/tag'] = $this->getProductTagData( $productManager, $refKeys['product/tag'] );

		$this->addProductData( $testdata, $productManager, $refIds, $refKeys['product'] );

		$this->status( 'done' );
	}


	/**
	 * Returns required attribute item ids.
	 *
	 * @param array $keys List of keys for search
	 * @return array $refIds List with referenced Ids
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function getAttributeData( array $keys )
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $this->additional, 'Default' );
		$attributeTypeManager = $attributeManager->getSubManager( 'type', 'Default' );

		$codes = $typeCodes = $domains = array();
		foreach( $keys as $dataset )
		{
			$exp = explode( '/', $dataset );

			if( count( $exp ) != 4 ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref attribute are set wrong "%1$s"', $dataset ) );
			}

			$domains[] = $exp[1];
			$typeCodes[] = $exp[2];
			$codes[] = $exp[3];
		}

		$search = $attributeTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.type.domain', $domains ),
			$search->compare( '==', 'attribute.type.code', $typeCodes ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$typeids = array();
		foreach( $attributeTypeManager->searchItems( $search ) as $item ) {
			$typeids[] = $item->getId();
		}

		$search = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', $codes ),
			$search->compare( '==', 'attribute.typeid', $typeids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$refIds = array();
		foreach( $attributeManager->searchItems( $search ) as $item ) {
			$refIds['attribute/' . $item->getDomain() . '/' . $item->getType() . '/' . $item->getCode()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Returns required media item ids.
	 *
	 * @param array $keys List of keys for search
	 * @return array $refIds List with referenced Ids
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function getMediaData( array $keys )
	{
		$mediaManager = MShop_Media_Manager_Factory::createManager( $this->additional, 'Default' );

		$urls = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref media are set wrong "%1$s"', $dataset ) );
			}

			$urls[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', $urls ) );

		$refIds = array();
		foreach( $mediaManager->searchItems( $search ) as $item ) {
			$refIds['media/' . $item->getUrl()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Returns required price item ids.
	 *
	 * @param array $keys List with referenced Ids
	 * @return array $refIds List with referenced Ids
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function getPriceData( array $keys )
	{
		$priceManager = MShop_Price_Manager_Factory::createManager( $this->additional, 'Default' );
		$priceTypeManager = $priceManager->getSubManager( 'type', 'Default' );

		$value = $ship = $domain = $code = array();
		foreach( $keys as $dataset )
		{
			$exp = explode( '/', $dataset );

			if( count( $exp ) != 5 ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref price are set wrong "%1$s"', $dataset ) );
			}

			$domain[] = $exp[1];
			$code[] = $exp[2];
			$value[] = $exp[3];
			$ship[] = $exp[4];
		}

		$search = $priceTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.type.domain', $domain ),
			$search->compare( '==', 'price.type.code', $code ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$typeids = array();
		foreach( $priceTypeManager->searchItems( $search ) as $item ) {
			$typeids[] = $item->getId();
		}

		$search = $priceManager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.value', $value ),
			$search->compare( '==', 'price.costs', $ship ),
		$search->compare( '==', 'price.typeid', $typeids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$refIds = array();
		foreach( $priceManager->searchItems( $search ) as $item ) {
			$refIds['price/' . $item->getDomain() . '/' . $item->getType() . '/' . $item->getValue() . '/' . $item->getCosts()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Returns required text item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function getTextData( array $keys )
	{
		$textManager = MShop_Text_Manager_Factory::createManager( $this->additional, 'Default' );

		$labels = array();
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref text are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$refIds = array();
		foreach( $textManager->searchItems( $search ) as $item ) {
			$refIds['text/' . $item->getLabel()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Returns the product tag test data.
	 *
	 * @param MShop_Product_Manager_Interface $productManager Product Manager
	 * @param array $keys List of keys for tag lookup
	 * @return array $refIds List with referenced Ids
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	protected function getProductTagData( $productManager, array $keys )
	{
		$productTagManager = $productManager->getSubManager( 'tag', 'Default' );

		$prodTag = array();
		foreach( $keys as $key )
		{
			$exp = explode( '/', $key );

			if( count( $exp ) != 3 ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref product tag are set wrong "%1$s"', $key ) );
			}

			$prodTag[] = $exp[2];
		}

		$search = $productTagManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.tag.label', $prodTag ) );

		$refIds = array();
		foreach( $productTagManager->searchItems( $search ) as $item ) {
			$refIds['product/tag/' . $item->getLabel()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the product-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param MShop_Product_Manager_Interface $productManager Product Manager
	 * @param array $refIds Associative list of key/list pairs
	 * @throws MW_Setup_Exception If no type ID is found
	 */
	private function addProductData( array $testdata, $productManager, array $refIds, array $keys )
	{
		$parentIds = $this->getProductIds( $productManager, $testdata );
		$refIds['product'] = $this->getProductRefIds( $productManager, $keys );

		$productListManager = $productManager->getSubManager( 'list', 'Default' );
		$listItem = $productListManager->createItem();

		//LIST-PRODUCT
		$this->conn->begin();

		$listItemTypeIds = $this->addListTypeData( $productListManager, $testdata );

		foreach( $testdata['product/list'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No product ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $listItemTypeIds[$dataset['typeid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No product list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new MW_Setup_Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
			}

			$listItem->setId( null );
			$listItem->setParentId( $parentIds[$dataset['parentid']] );
			$listItem->setTypeId( $listItemTypeIds[$dataset['typeid']] );
			$listItem->setRefId( $refIds[$dataset['domain']] [$dataset['refid']] );
			$listItem->setDomain( $dataset['domain'] );
			$listItem->setDateStart( $dataset['start'] );
			$listItem->setDateEnd( $dataset['end'] );
			$listItem->setConfig( $dataset['config'] );
			$listItem->setPosition( $dataset['pos'] );
			$listItem->setStatus( $dataset['status'] );

			$productListManager->saveItem( $listItem, false );
		}

		$this->conn->commit();
	}


	/**
	 * Adds the list types from the test data and returns their IDs.
	 *
	 * @param MShop_Common_Manager_Interface $manager Product list manager
	 * @param array $testdata Test data
	 * @param array List of type IDs
	 */
	private function addListTypeData( MShop_Common_Manager_Interface $manager, array $testdata )
	{
		$listItemTypeIds = array();
		$productListTypeManager = $manager->getSubmanager( 'type', 'Default' );
		$listItemType = $productListTypeManager->createItem();

		foreach( $testdata['product/list/type'] as $key => $dataset )
		{
			$listItemType->setId( null );
			$listItemType->setCode( $dataset['code'] );
			$listItemType->setDomain( $dataset['domain'] );
			$listItemType->setLabel( $dataset['label'] );
			$listItemType->setStatus( $dataset['status'] );

			$productListTypeManager->saveItem( $listItemType );
			$listItemTypeIds[$key] = $listItemType->getId();
		}

		return $listItemTypeIds;
	}


	/**
	 * Returns the product IDs from the test data.
	 *
	 * @param MShop_Common_Manager_Interface $manager Product manager
	 * @param array $testdata Test data
	 * @throws MW_Setup_Exception
	 */
	private function getProductIds( MShop_Common_Manager_Interface $manager, array $testdata )
	{
		$parentCodes = $parentIds = array();

		foreach( $testdata['product/list'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false
				|| ( $str = substr( $dataset['parentid'], $pos + 1 ) ) === false
			) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
			}

			$parentCodes[] = $str;
		}

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array_unique( $parentCodes ) ) );

		foreach( $manager->searchItems( $search ) as $item ) {
			$parentIds['product/' . $item->getCode()] = $item->getId();
		}

		return $parentIds;
	}


	/**
	 * Returns the referenced product IDs from the test data.
	 *
	 * @param MShop_Common_Manager_Interface $manager Product manager
	 * @param string[] keys Unique keys to identify the products
	 * @throws MW_Setup_Exception
	 */
	private function getProductRefIds( MShop_Common_Manager_Interface $manager, array $keys )
	{
		$codes = $refIds = array();

		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new MW_Setup_Exception( sprintf( 'Some keys for ref product are set wrong "%1$s"', $dataset ) );
			}

			$codes[] = $str;
		}

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $codes ) );

		foreach( $manager->searchItems( $search ) as $item ) {
			$refIds['product/' . $item->getCode()] = $item->getId();
		}

		return $refIds;
	}
}
