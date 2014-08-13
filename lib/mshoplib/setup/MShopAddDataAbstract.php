<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Adds records to tables.
 */
class MW_Setup_Task_MShopAddDataAbstract extends MW_Setup_Task_Abstract
{
	public function __construct( MW_Setup_DBSchema_Interface $schema, MW_DB_Connection_Interface $conn, $additional = null )
	{
		$iface = 'MShop_Context_Item_Interface';
		if( !( $additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
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
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function _mysql()
	{
		// executed by tasks in sub-directories for specific sites
		// $this->_process();
	}


	/**
	 * Adds the attributes to the given parent in the database.
	 *
	 * @param string $parentid ID of the parent item where the media should be associated to
	 * @param array $data Two dimensional associative list of attribute data
	 * @param string $domain Domain name the texts should be added to, e.g. 'catalog'
	 */
	protected function _addAttributes( $parentid, array $data, $domain )
	{
		$context =  $this->_getContext();
		$attrManager = MShop_Factory::createManager( $context, 'attribute' );
		$listManager = MShop_Factory::createManager( $context, $domain . '/list' );


		$item = $attrManager->createItem();
		$item->setDomain( $domain );

		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'attribute' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setTypeId( $this->_getTypeId( 'attribute/type', $domain, $entry['type'] ) );
			$item->setCode( $entry['code'] );
			$item->setLabel( $entry['label'] );
			$item->setPosition( $entry['position'] );
			$item->setStatus( $entry['status'] );

			$attrManager->saveItem( $item );

			$listItem->setId( null );
			$listItem->setTypeId( $this->_getTypeId( $domain . '/list/type', 'attribute', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->saveItem( $listItem, false );


			if( isset( $entry['attribute'] ) ) {
				$this->_addAttributes( $item->getId(), $entry['attribute'], 'attribute' );
			}

			if( isset( $entry['media'] ) ) {
				$this->_addMedia( $item->getId(), $entry['media'], 'attribute' );
			}

			if( isset( $entry['price'] ) ) {
				$this->_addPrices( $item->getId(), $entry['price'], 'attribute' );
			}

			if( isset( $entry['text'] ) ) {
				$this->_addTexts( $item->getId(), $entry['text'], 'attribute' );
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
	protected function _addMedia( $parentid, array $data, $domain )
	{
		$context =  $this->_getContext();
		$mediaManager = MShop_Factory::createManager( $context, 'media' );
		$listManager = MShop_Factory::createManager( $context, $domain . '/list' );


		$item = $mediaManager->createItem();
		$item->setDomain( $domain );

		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'media' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setTypeId( $this->_getTypeId( 'media/type', $domain, $entry['type'] ) );
			$item->setLanguageId( $entry['languageid'] );
			$item->setMimetype( $entry['mimetype'] );
			$item->setPreview( $entry['preview'] );
			$item->setUrl( $entry['url'] );
			$item->setLabel( $entry['label'] );
			$item->setStatus( $entry['status'] );

			$mediaManager->saveItem( $item );

			$listItem->setId( null );
			$listItem->setTypeId( $this->_getTypeId( $domain . '/list/type', 'media', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->saveItem( $listItem, false );


			if( isset( $entry['attribute'] ) ) {
				$this->_addAttributes( $item->getId(), $entry['attribute'], 'media' );
			}

			if( isset( $entry['media'] ) ) {
				$this->_addMedia( $item->getId(), $entry['media'], 'media' );
			}

			if( isset( $entry['price'] ) ) {
				$this->_addPrices( $item->getId(), $entry['price'], 'media' );
			}

			if( isset( $entry['text'] ) ) {
				$this->_addTexts( $item->getId(), $entry['text'], 'media' );
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
	protected function _addPrices( $parentid, array $data, $domain )
	{
		$context =  $this->_getContext();
		$mediaManager = MShop_Factory::createManager( $context, 'price' );
		$listManager = MShop_Factory::createManager( $context, $domain . '/list' );


		$item = $mediaManager->createItem();
		$item->setDomain( $domain );

		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'price' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setTypeId( $this->_getTypeId( 'price/type', $domain, $entry['type'] ) );
			$item->setCurrencyId( $entry['currencyid'] );
			$item->setQuantity( $entry['quantity'] );
			$item->setValue( $entry['value'] );
			$item->setCosts( $entry['costs'] );
			$item->setRebate( $entry['rebate'] );
			$item->setTaxRate( $entry['taxrate'] );
			$item->setStatus( $entry['status'] );

			$mediaManager->saveItem( $item );

			$listItem->setId( null );
			$listItem->setTypeId( $this->_getTypeId( $domain . '/list/type', 'price', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->saveItem( $listItem, false );


			if( isset( $entry['attribute'] ) ) {
				$this->_addAttributes( $item->getId(), $entry['attribute'], 'price' );
			}

			if( isset( $entry['media'] ) ) {
				$this->_addMedia( $item->getId(), $entry['media'], 'price' );
			}

			if( isset( $entry['price'] ) ) {
				$this->_addPrices( $item->getId(), $entry['price'], 'price' );
			}

			if( isset( $entry['text'] ) ) {
				$this->_addTexts( $item->getId(), $entry['text'], 'price' );
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
	protected function _addTexts( $parentid, array $data, $domain )
	{
		$context =  $this->_getContext();
		$textManager = MShop_Factory::createManager( $context, 'text' );
		$listManager = MShop_Factory::createManager( $context, $domain . '/list' );


		$item = $textManager->createItem();
		$item->setDomain( $domain );

		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'text' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setTypeId( $this->_getTypeId( 'text/type', $domain, $entry['type'] ) );
			$item->setLanguageId( $entry['languageid'] );
			$item->setContent( $entry['content'] );
			$item->setLabel( $entry['label'] );
			$item->setStatus( $entry['status'] );

			$textManager->saveItem( $item );

			$listItem->setId( null );
			$listItem->setTypeId( $this->_getTypeId( $domain . '/list/type', 'text', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->saveItem( $listItem, false );


			if( isset( $entry['attribute'] ) ) {
				$this->_addAttributes( $item->getId(), $entry['attribute'], 'text' );
			}

			if( isset( $entry['media'] ) ) {
				$this->_addMedia( $item->getId(), $entry['media'], 'text' );
			}

			if( isset( $entry['price'] ) ) {
				$this->_addPrices( $item->getId(), $entry['price'], 'text' );
			}

			if( isset( $entry['text'] ) ) {
				$this->_addTexts( $item->getId(), $entry['text'], 'text' );
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
	protected function _addProducts( $parentid, array $data, $domain )
	{
		$context =  $this->_getContext();
		$productManager = MShop_Factory::createManager( $context, 'product' );
		$listManager = MShop_Factory::createManager( $context, $domain . '/list' );


		$listItem = $listManager->createItem();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'product' );


		$codes = array();

		foreach( $data as $entry ) {
			$codes[ $entry['code'] ] = null;
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array_keys( $codes ) ) );
		$products = $productManager->searchItems( $search );

		foreach( $products as $product ) {
			$codes[ $product->getCode() ] = $product->getId();
		}


		foreach( $data as $entry )
		{
			if( !isset( $codes[ $entry['code'] ] ) ) {
				throw new Exception( sprintf( 'No product for code "%1$s" found', $entry['code'] ) );
			}

			$listItem->setId( null );
			$listItem->setTypeId( $this->_getTypeId( $domain . '/list/type', 'product', $entry['list-type'] ) );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $codes[ $entry['code'] ] );

			$listManager->saveItem( $listItem, false );
		}
	}


	/**
	 * Adds stock levels to the given product in the database.
	 *
	 * @param string $productid ID of the product item where the stock levels should be associated to
	 * @param array $data Two dimensional associative list of product stock data
	 */
	protected function _addProductStock( $productid, array $data )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/stock/warehouse' );

		$warehouses = array();
		foreach( $manager->searchItems( $manager->createSearch() ) as $id => $item ) {
			$warehouses[ $item->getCode() ] = $id;
		}

		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/stock' );

		$item = $manager->createItem();
		$item->setProductId( $productid );

		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setDateBack( $entry['dateback'] );
			$item->setStockLevel( $entry['stocklevel'] );
			$item->setWarehouseId( $warehouses[ $entry['warehouse'] ] );

			$manager->saveItem( $item, false );
		}
	}


	/**
	 * Returns the context.
	 *
	 * @return MShop_Context_Item_Interface Context item
	 */
	protected function _getContext()
	{
		return $this->_additional;
	}


	/**
	 * Returns the type ID for the given type and domain found by the manager
	 *
	 * @param string $name Manager name like 'catalog/list/type'
	 * @param string $domain Domain of the type item we are looking for, e.g. 'text'
	 * @param string $type Type code of the item we are looking for, e.g. 'default'
	 */
	protected function _getTypeId( $name, $domain, $type )
	{
		$key = str_replace( '/', '.', $name );
		$manager = MShop_Factory::createManager( $this->_getContext(), $name );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', $key . '.domain', $domain ),
			$search->compare( '==', $key . '.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No type item found for "%1$s/%2$s" using "%3$s"', $domain, $type, $name ) );
		}

		return $item->getId();
	}


	/**
	 * Deletes the demo items from the given parent ID in the database.
	 *
	 * @param string $parentid ID of the parent item where the associated items should be removed from
	 * @param string $name Name of the list manager, e.g. 'catalog/list'
	 * @param string $domain Name of the domain the items are associated to, e.g. 'catalog'
	 * @param string $refdomain Name of the domain to remove the items from, e.g. 'text'
	 */
	protected function _removeItems( $parentid, $name, $domain, $refdomain )
	{
		$context =  $this->_getContext();
		$key = str_replace( '/', '.', $name );

		$manager = MShop_Factory::createManager( $context, $refdomain );
		$listManager = MShop_Factory::createManager( $context, $name );


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
	 * @param string $name Name of the list manager, e.g. 'catalog/list'
	 * @param string $refdomain Name of the domain to remove the items from, e.g. 'product'
	 */
	protected function _removeListItems( $parentid, $name, $refdomain )
	{
		$start = 0;
		$context =  $this->_getContext();
		$key = str_replace( '/', '.', $name );

		$manager = MShop_Factory::createManager( $context, $refdomain );
		$listManager = MShop_Factory::createManager( $context, $name );


		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', $key . '.parentid', $parentid ),
			$search->compare( '==', $key . '.domain', $refdomain ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		do
		{
			$refIds = $listIds = $map = array();
			$result = $listManager->searchItems( $search );

			foreach( $result as $id => $listItem )
			{
				$refIds[] = $listItem->getRefId();
				$map[ $listItem->getRefId() ][] = $id;
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


	protected function _txBegin()
	{
		$dbm = $this->_additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->begin();
		$dbm->release( $conn );
	}


	protected function _txCommit()
	{
		$dbm = $this->_additional->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->commit();
		$dbm->release( $conn );
	}
}