<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Adds records to tables.
 */
class MShopAddDataAbstract extends \Aimeos\MW\Setup\Task\Base
{
	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MW\Setup\DBSchema\Iface $schema
	 * @param \Aimeos\MW\DB\Connection\Iface $conn
	 * @param \Aimeos\MShop\Context\Item\Iface|null $additional
	 * @throws \Aimeos\MW\Setup\Exception
	 */
	public function __construct( \Aimeos\MW\Setup\DBSchema\Iface $schema, \Aimeos\MW\DB\Connection\Iface $conn, $additional = null )
	{
		$iface = '\\Aimeos\\MShop\\Context\\Item\\Iface';
		if( !( $additional instanceof $iface ) ) {
			throw new \Aimeos\MW\Setup\Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		parent::__construct( $schema, $conn, $additional );
	}


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'TablesCreateMShop' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return [];
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		// executed by tasks in sub-directories for specific sites
	}


	/**
	 * Adds the attributes to the given parent in the database.
	 *
	 * @param string $parentid ID of the parent item where the media should be associated to
	 * @param array $data Two dimensional associative list of attribute data
	 * @param string $domain Domain name the texts should be added to, e.g. 'catalog'
	 */
	protected function addAttributes( $parentid, array $data, $domain )
	{
		$context = $this->getContext();
		$attrManager = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );


		$item = $attrManager->createItem();
		$item->setDomain( $domain );

		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'attribute' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setTypeId( $this->getTypeId( 'attribute/type', $domain, $entry['type'] ) );
			$item->setCode( $entry['code'] );
			$item->setLabel( $entry['label'] );
			$item->setPosition( $entry['position'] );
			$item->setStatus( $entry['status'] );

			$attrManager->saveItem( $item );

			$listItem->setId( null );
			$listItem->setTypeId( $this->getTypeId( $domain . '/lists/type', 'attribute', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->saveItem( $listItem, false );


			if( isset( $entry['attribute'] ) ) {
				$this->addAttributes( $item->getId(), $entry['attribute'], 'attribute' );
			}

			if( isset( $entry['media'] ) ) {
				$this->addMedia( $item->getId(), $entry['media'], 'attribute' );
			}

			if( isset( $entry['price'] ) ) {
				$this->addPrices( $item->getId(), $entry['price'], 'attribute' );
			}

			if( isset( $entry['text'] ) ) {
				$this->addTexts( $item->getId(), $entry['text'], 'attribute' );
			}
		}
	}


	/**
	 * Adds the media to the given parent in the database.
	 *
	 * @param string $parentid ID of the parent item where the media should be associated to
	 * @param array $data Two dimensional associative list of media data
	 * @param string $domain Domain name the texts should be added to, e.g. 'catalog'
	 */
	protected function addMedia( $parentid, array $data, $domain )
	{
		$context = $this->getContext();
		$mediaManager = \Aimeos\MShop\Factory::createManager( $context, 'media' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );


		$item = $mediaManager->createItem();
		$item->setDomain( $domain );

		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'media' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setTypeId( $this->getTypeId( 'media/type', $domain, $entry['type'] ) );
			$item->setLanguageId( $entry['languageid'] );
			$item->setMimetype( $entry['mimetype'] );
			$item->setPreview( $entry['preview'] );
			$item->setUrl( $entry['url'] );
			$item->setLabel( $entry['label'] );
			$item->setStatus( $entry['status'] );

			$mediaManager->saveItem( $item );

			$listItem->setId( null );
			$listItem->setTypeId( $this->getTypeId( $domain . '/lists/type', 'media', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->saveItem( $listItem, false );


			if( isset( $entry['attribute'] ) ) {
				$this->addAttributes( $item->getId(), $entry['attribute'], 'media' );
			}

			if( isset( $entry['media'] ) ) {
				$this->addMedia( $item->getId(), $entry['media'], 'media' );
			}

			if( isset( $entry['price'] ) ) {
				$this->addPrices( $item->getId(), $entry['price'], 'media' );
			}

			if( isset( $entry['text'] ) ) {
				$this->addTexts( $item->getId(), $entry['text'], 'media' );
			}
		}
	}


	/**
	 * Adds the prices to the given parent in the database.
	 *
	 * @param string $parentid ID of the parent item where the media should be associated to
	 * @param array $data Two dimensional associative list of price data
	 * @param string $domain Domain name the texts should be added to, e.g. 'catalog'
	 */
	protected function addPrices( $parentid, array $data, $domain )
	{
		$context = $this->getContext();
		$mediaManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );


		$item = $mediaManager->createItem();
		$item->setDomain( $domain );

		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'price' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setLabel( $entry['label'] );
			$item->setTypeId( $this->getTypeId( 'price/type', $domain, $entry['type'] ) );
			$item->setCurrencyId( $entry['currencyid'] );
			$item->setQuantity( $entry['quantity'] );
			$item->setValue( $entry['value'] );
			$item->setCosts( $entry['costs'] );
			$item->setRebate( $entry['rebate'] );
			$item->setTaxRate( $entry['taxrate'] );
			$item->setStatus( $entry['status'] );

			$mediaManager->saveItem( $item );

			$listItem->setId( null );
			$listItem->setTypeId( $this->getTypeId( $domain . '/lists/type', 'price', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->saveItem( $listItem, false );


			if( isset( $entry['attribute'] ) ) {
				$this->addAttributes( $item->getId(), $entry['attribute'], 'price' );
			}

			if( isset( $entry['media'] ) ) {
				$this->addMedia( $item->getId(), $entry['media'], 'price' );
			}

			if( isset( $entry['price'] ) ) {
				$this->addPrices( $item->getId(), $entry['price'], 'price' );
			}

			if( isset( $entry['text'] ) ) {
				$this->addTexts( $item->getId(), $entry['text'], 'price' );
			}
		}
	}


	/**
	 * Adds the texts to the given parent in the database.
	 *
	 * @param string $parentid ID of the parent item where the media should be associated to
	 * @param array $data Two dimensional associative list text data
	 * @param string $domain Domain name the texts should be added to, e.g. 'catalog'
	 */
	protected function addTexts( $parentid, array $data, $domain )
	{
		$context = $this->getContext();
		$textManager = \Aimeos\MShop\Factory::createManager( $context, 'text' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );


		$item = $textManager->createItem();
		$item->setDomain( $domain );

		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'text' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setTypeId( $this->getTypeId( 'text/type', $domain, $entry['type'] ) );
			$item->setLanguageId( $entry['languageid'] );
			$item->setContent( $entry['content'] );
			$item->setLabel( $entry['label'] );
			$item->setStatus( $entry['status'] );

			$textManager->saveItem( $item );

			$listItem->setId( null );
			$listItem->setTypeId( $this->getTypeId( $domain . '/lists/type', 'text', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->saveItem( $listItem, false );


			if( isset( $entry['attribute'] ) ) {
				$this->addAttributes( $item->getId(), $entry['attribute'], 'text' );
			}

			if( isset( $entry['media'] ) ) {
				$this->addMedia( $item->getId(), $entry['media'], 'text' );
			}

			if( isset( $entry['price'] ) ) {
				$this->addPrices( $item->getId(), $entry['price'], 'text' );
			}

			if( isset( $entry['text'] ) ) {
				$this->addTexts( $item->getId(), $entry['text'], 'text' );
			}
		}
	}


	/**
	 * Adds the products to the given parent in the database.
	 *
	 * @param string $parentid ID of the parent item where the products should be associated to
	 * @param array $data Two dimensional associative list of product data
	 * @param string $domain Domain name the texts should be added to, e.g. 'catalog'
	 */
	protected function addProducts( $parentid, array $data, $domain )
	{
		$context = $this->getContext();
		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );


		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'product' );


		$codes = [];

		foreach( $data as $entry ) {
			$codes[$entry['code']] = null;
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array_keys( $codes ) ) );
		$products = $productManager->searchItems( $search );

		foreach( $products as $product ) {
			$codes[$product->getCode()] = $product->getId();
		}


		foreach( $data as $entry )
		{
			if( !isset( $codes[$entry['code']] ) ) {
				throw new \RuntimeException( sprintf( 'No product for code "%1$s" found', $entry['code'] ) );
			}

			$listItem->setId( null );
			$listItem->setTypeId( $this->getTypeId( $domain . '/lists/type', 'product', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $codes[$entry['code']] );

			$listManager->saveItem( $listItem, false );
		}
	}


	/**
	 * Adds stock levels to the given product in the database.
	 *
	 * @param string $productcode Code of the product item where the stock levels should be associated to
	 * @param array $data Two dimensional associative list of product stock data
	 */
	protected function addProductStock( $productcode, array $data )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'stock/type' );

		$types = [];
		foreach( $manager->searchItems( $manager->createSearch() ) as $id => $item ) {
			$types[$item->getCode()] = $id;
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'stock' );

		$item = $manager->createItem();
		$item->setProductCode( $productcode );

		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setDateBack( $entry['dateback'] );
			$item->setStockLevel( $entry['stocklevel'] );
			$item->setTypeId( $types[$entry['typeid']] );

			$manager->saveItem( $item, false );
		}
	}


	/**
	 * Returns the context.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context item
	 */
	protected function getContext()
	{
		return $this->additional;
	}


	/**
	 * Returns the type ID for the given type and domain found by the manager
	 *
	 * @param string $name Manager name like 'catalog/lists/type'
	 * @param string $domain Domain of the type item we are looking for, e.g. 'text'
	 * @param string $type Type code of the item we are looking for, e.g. 'default'
	 * @return string Type ID
	 */
	protected function getTypeId( $name, $domain, $type )
	{
		$key = str_replace( '/', '.', $name );
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $name );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', $key . '.domain', $domain ),
			$search->compare( '==', $key . '.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No type item found for "%1$s/%2$s" using "%3$s"', $domain, $type, $name ) );
		}

		return $item->getId();
	}


	/**
	 * Deletes the demo items from the given parent ID in the database.
	 *
	 * @param string $parentid ID of the parent item where the associated items should be removed from
	 * @param string $name Name of the list manager, e.g. 'catalog/lists'
	 * @param string $domain Name of the domain the items are associated to, e.g. 'catalog'
	 * @param string $refdomain Name of the domain to remove the items from, e.g. 'text'
	 */
	protected function removeItems( $parentid, $name, $domain, $refdomain )
	{
		$context = $this->getContext();
		$key = str_replace( '/', '.', $name );

		$manager = \Aimeos\MShop\Factory::createManager( $context, $refdomain );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, $name );


		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', $refdomain . '.domain', $domain ),
			$search->compare( '=~', $refdomain . '.label', 'Demo' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$ids = array_keys( $manager->searchItems( $search ) );
		$manager->deleteItems( $ids );


		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', $key . '.parentid', $parentid ),
			$search->compare( '==', $key . '.domain', $refdomain ),
			$search->compare( '==', $key . '.refid', $ids ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$listIds = array_keys( $listManager->searchItems( $search ) );
		$listManager->deleteItems( $listIds );
	}


	/**
	 * Deletes the references to non-existent or demo items in the database.
	 *
	 * @param string $parentid ID of the parent item where the associated items should be removed from
	 * @param string $name Name of the list manager, e.g. 'catalog/lists'
	 * @param string $refdomain Name of the domain to remove the items from, e.g. 'product'
	 */
	protected function removeListItems( $parentid, $name, $refdomain )
	{
		$start = 0;
		$context = $this->getContext();
		$key = str_replace( '/', '.', $name );

		$manager = \Aimeos\MShop\Factory::createManager( $context, $refdomain );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, $name );


		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', $key . '.parentid', $parentid ),
			$search->compare( '==', $key . '.domain', $refdomain ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		do
		{
			$refIds = $listIds = $map = [];
			$result = $listManager->searchItems( $search );

			foreach( $result as $id => $listItem )
			{
				$refIds[] = $listItem->getRefId();
				$map[$listItem->getRefId()][] = $id;
			}


			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', $refdomain . '.id', $refIds ) );
			$ids = array_keys( $manager->searchItems( $search ) );

			foreach( array_diff( $refIds, $ids ) as $refId ) {
				$listIds = array_merge( $listIds, $map[$refId] );
			}

			$listManager->deleteItems( $listIds );


			$count = count( $result );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count == $search->getSliceSize() );


		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '=~', $refdomain . '.label', 'Demo' ) );
		$ids = array_keys( $manager->searchItems( $search ) );

		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', $key . '.parentid', $parentid ),
			$search->compare( '==', $key . '.refid', $ids ),
			$search->compare( '==', $key . '.domain', $refdomain ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$listManager->deleteItems( array_keys( $listManager->searchItems( $search ) ) );
	}


	/**
	 * Starts a new transation
	 */
	protected function txBegin()
	{
		$dbm = $this->additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->begin();
		$dbm->release( $conn );
	}


	/**
	 * Commits an existing transaction
	 */
	protected function txCommit()
	{
		$dbm = $this->additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->commit();
		$dbm->release( $conn );
	}
}