<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds product test data and all items from other domains.
 */
class ProductListAddTestData extends \Aimeos\MW\Setup\Task\Base
{

	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'MediaListAddTestData', 'PriceListAddTestData', 'ProductAddTestData', 'TagAddTestData' );
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
	 * Adds product test data.
	 */
	public function migrate()
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding product-list test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'product-list.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for product domain', $path ) );
		}

		$refKeys = [];
		foreach( $testdata['product/lists'] as $dataset ) {
			$refKeys[$dataset['domain']][] = $dataset['refid'];
		}

		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->additional, 'Standard' );

		$refIds = [];
		$refIds['attribute'] = $this->getAttributeData( $refKeys['attribute'] );
		$refIds['media'] = $this->getMediaData( $refKeys['media'] );
		$refIds['price'] = $this->getPriceData( $refKeys['price'] );
		$refIds['text'] = $this->getTextData( $refKeys['text'] );
		$refIds['tag'] = $this->getTagData( $refKeys['tag'] );

		$this->addProductData( $testdata, $productManager, $refIds, $refKeys['product'] );

		$this->status( 'done' );
	}


	/**
	 * Returns required attribute item ids.
	 *
	 * @param array $keys List of keys for search
	 * @return array $refIds List with referenced Ids
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function getAttributeData( array $keys )
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->additional, 'Standard' );
		$attributeTypeManager = $attributeManager->getSubManager( 'type', 'Standard' );

		$codes = $typeCodes = $domains = [];
		foreach( $keys as $dataset )
		{
			$exp = explode( '/', $dataset );

			if( count( $exp ) != 4 ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref attribute are set wrong "%1$s"', $dataset ) );
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

		$typeids = [];
		foreach( $attributeTypeManager->searchItems( $search ) as $item ) {
			$typeids[] = $item->getId();
		}

		$search = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', $codes ),
			$search->compare( '==', 'attribute.typeid', $typeids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$refIds = [];
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
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function getMediaData( array $keys )
	{
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::createManager( $this->additional, 'Standard' );

		$urls = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref media are set wrong "%1$s"', $dataset ) );
			}

			$urls[] = $str;
		}

		$search = $mediaManager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', $urls ) );

		$refIds = [];
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
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function getPriceData( array $keys )
	{
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $this->additional, 'Standard' );
		$priceTypeManager = $priceManager->getSubManager( 'type', 'Standard' );

		$value = $ship = $domain = $code = [];
		foreach( $keys as $dataset )
		{
			$exp = explode( '/', $dataset );

			if( count( $exp ) != 5 ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref price are set wrong "%1$s"', $dataset ) );
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

		$typeids = [];
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

		$refIds = [];
		foreach( $priceManager->searchItems( $search ) as $item ) {
			$refIds['price/' . $item->getDomain() . '/' . $item->getType() . '/' . $item->getValue() . '/' . $item->getCosts()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Returns required text item ids.
	 *
	 * @param array $keys List of keys for search
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function getTextData( array $keys )
	{
		$textManager = \Aimeos\MShop\Text\Manager\Factory::createManager( $this->additional, 'Standard' );

		$labels = [];
		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref text are set wrong "%1$s"', $dataset ) );
			}

			$labels[] = $str;
		}

		$search = $textManager->createSearch();
		$search->setConditions( $search->compare( '==', 'text.label', $labels ) );

		$refIds = [];
		foreach( $textManager->searchItems( $search ) as $item ) {
			$refIds['text/' . $item->getLabel()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Returns the product tag test data.
	 *
	 * @param array $keys List of keys for tag lookup
	 * @return array $refIds List with referenced Ids
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	protected function getTagData( array $keys )
	{
		$tagManager = \Aimeos\MShop\Tag\Manager\Factory::createManager( $this->additional, 'Standard' );

		$prodTag = [];
		foreach( $keys as $key )
		{
			$exp = explode( '/', $key );

			if( count( $exp ) != 2 ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref product tag are set wrong "%1$s"', $key ) );
			}

			$prodTag[] = $exp[1];
		}

		$search = $tagManager->createSearch();
		$search->setConditions( $search->compare( '==', 'tag.label', $prodTag ) );

		$refIds = [];
		foreach( $tagManager->searchItems( $search ) as $item ) {
			$refIds['tag/' . $item->getLabel()] = $item->getId();
		}

		return $refIds;
	}


	/**
	 * Adds the product-list test data.
	 *
	 * @param array $testdata Associative list of key/list pairs
	 * @param \Aimeos\MShop\Common\Manager\Iface $productManager Product Manager
	 * @param array $refIds Associative list of key/list pairs
	 * @throws \Aimeos\MW\Setup\Exception If no type ID is found
	 */
	private function addProductData( array $testdata, \Aimeos\MShop\Common\Manager\Iface $productManager, array $refIds, array $keys )
	{
		$parentIds = $this->getProductIds( $productManager, $testdata );
		$refIds['product'] = $this->getProductRefIds( $productManager, $keys );

		$productListManager = $productManager->getSubManager( 'lists', 'Standard' );
		$listItem = $productListManager->createItem();

		//LIST-PRODUCT
		$this->conn->begin();

		$listItemTypeIds = $this->addListTypeData( $productListManager, $testdata );

		foreach( $testdata['product/lists'] as $dataset )
		{
			if( !isset( $parentIds[$dataset['parentid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No product ID found for "%1$s"', $dataset['parentid'] ) );
			}

			if( !isset( $listItemTypeIds[$dataset['typeid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No product list type ID found for "%1$s"', $dataset['typeid'] ) );
			}

			if( !isset( $refIds[$dataset['domain']][$dataset['refid']] ) ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'No "%1$s" ref ID found for "%2$s"', $dataset['refid'], $dataset['domain'] ) );
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
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Product list manager
	 * @param array $testdata Test data
	 * @param array List of type IDs
	 */
	private function addListTypeData( \Aimeos\MShop\Common\Manager\Iface $manager, array $testdata )
	{
		$listItemTypeIds = [];
		$productListTypeManager = $manager->getSubmanager( 'type', 'Standard' );
		$listItemType = $productListTypeManager->createItem();

		foreach( $testdata['product/lists/type'] as $key => $dataset )
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
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Product manager
	 * @param array $testdata Test data
	 * @throws \Aimeos\MW\Setup\Exception
	 */
	private function getProductIds( \Aimeos\MShop\Common\Manager\Iface $manager, array $testdata )
	{
		$parentCodes = $parentIds = [];

		foreach( $testdata['product/lists'] as $dataset )
		{
			if( ( $pos = strpos( $dataset['parentid'], '/' ) ) === false
				|| ( $str = substr( $dataset['parentid'], $pos + 1 ) ) === false
			) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for parentid are set wrong "%1$s"', $dataset['parentid'] ) );
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
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Product manager
	 * @param string[] keys Unique keys to identify the products
	 * @throws \Aimeos\MW\Setup\Exception
	 */
	private function getProductRefIds( \Aimeos\MShop\Common\Manager\Iface $manager, array $keys )
	{
		$codes = $refIds = [];

		foreach( $keys as $dataset )
		{
			if( ( $pos = strpos( $dataset, '/' ) ) === false || ( $str = substr( $dataset, $pos + 1 ) ) === false ) {
				throw new \Aimeos\MW\Setup\Exception( sprintf( 'Some keys for ref product are set wrong "%1$s"', $dataset ) );
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
