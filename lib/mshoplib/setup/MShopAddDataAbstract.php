<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds records to tables.
 */
class MShopAddDataAbstract extends Base
{
	private $attributes;


	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Attribute', 'Media', 'Price', 'Product', 'Text'];
	}


	public function up()
	{
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
		$context = $this->context();
		$attrManager = \Aimeos\MShop::create( $context, 'attribute' );
		$listManager = \Aimeos\MShop::create( $context, $domain . '/lists' );


		$item = $attrManager->create();
		$item->setDomain( $domain );

		$listItem = $listManager->create();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'attribute' );


		foreach( $data as $entry )
		{
			if( ( $attrItem = $this->getAttributeItem( $domain, $entry['type'], $entry['code'] ) ) === null )
			{
				$item->setId( null );
				$item->setType( $entry['type'] );
				$item->setCode( $entry['code'] );
				$item->setLabel( $entry['label'] );
				$item->setPosition( $entry['position'] );
				$item->setStatus( $entry['status'] );

				$attrManager->save( $item );
				$id = $item->getId();
			}
			else
			{
				$id = $attrItem->getId();
			}

			$listItem->setId( null );
			$listItem->setType( $entry['list-type'] );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $id );

			$listManager->save( $listItem, false );


			if( isset( $entry['attribute'] ) ) {
				$this->addAttributes( $id, $entry['attribute'], 'attribute' );
			}

			if( isset( $entry['media'] ) ) {
				$this->addMedia( $id, $entry['media'], 'attribute' );
			}

			if( isset( $entry['price'] ) ) {
				$this->addPrices( $id, $entry['price'], 'attribute' );
			}

			if( isset( $entry['text'] ) ) {
				$this->addTexts( $id, $entry['text'], 'attribute' );
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
		$context = $this->context();
		$mediaManager = \Aimeos\MShop::create( $context, 'media' );
		$listManager = \Aimeos\MShop::create( $context, $domain . '/lists' );


		$item = $mediaManager->create();
		$item->setDomain( $domain );

		$listItem = $listManager->create();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'media' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setType( $entry['type'] );
			$item->setLanguageId( $entry['languageid'] );
			$item->setMimetype( $entry['mimetype'] );
			$item->setPreviews( (array) $entry['preview'] );
			$item->setUrl( $entry['url'] );
			$item->setLabel( $entry['label'] );
			$item->setStatus( $entry['status'] );

			$mediaManager->save( $item );

			$listItem->setId( null );
			$listItem->setType( $entry['list-type'] );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->save( $listItem, false );


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
		$context = $this->context();
		$mediaManager = \Aimeos\MShop::create( $context, 'price' );
		$listManager = \Aimeos\MShop::create( $context, $domain . '/lists' );


		$item = $mediaManager->create();
		$item->setDomain( $domain );

		$listItem = $listManager->create();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'price' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setLabel( $entry['label'] );
			$item->setType( $entry['type'] );
			$item->setCurrencyId( $entry['currencyid'] );
			$item->setQuantity( $entry['quantity'] );
			$item->setValue( $entry['value'] );
			$item->setCosts( $entry['costs'] );
			$item->setRebate( $entry['rebate'] );
			$item->setTaxRate( $entry['taxrate'] );
			$item->setStatus( $entry['status'] );

			$mediaManager->save( $item );

			$listItem->setId( null );
			$listItem->setType( $entry['list-type'] );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->save( $listItem, false );


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
		$context = $this->context();
		$textManager = \Aimeos\MShop::create( $context, 'text' );
		$listManager = \Aimeos\MShop::create( $context, $domain . '/lists' );


		$item = $textManager->create();
		$item->setDomain( $domain );

		$listItem = $listManager->create();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'text' );


		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setType( $entry['type'] );
			$item->setLanguageId( $entry['languageid'] );
			$item->setContent( $entry['content'] );
			$item->setLabel( $entry['label'] );
			$item->setStatus( $entry['status'] );

			$textManager->save( $item );

			$listItem->setId( null );
			$listItem->setType( $entry['list-type'] );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $item->getId() );

			$listManager->save( $listItem, false );


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
	 * @param string $domain Domain name the products should be added to, e.g. 'catalog'
	 */
	protected function addProducts( $parentid, array $data, $domain )
	{
		$context = $this->context();
		$productManager = \Aimeos\MShop::create( $context, 'product' );
		$listManager = \Aimeos\MShop::create( $context, $domain . '/lists' );


		$listItem = $listManager->create();
		$listItem->setParentId( $parentid );
		$listItem->setDomain( 'product' );


		$codes = [];

		foreach( $data as $entry ) {
			$codes[$entry['code']] = null;
		}

		$search = $productManager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', array_keys( $codes ) ) );
		$products = $productManager->search( $search );

		foreach( $products as $product ) {
			$codes[$product->getCode()] = $product->getId();
		}


		foreach( $data as $entry )
		{
			if( !isset( $codes[$entry['code']] ) ) {
				throw new \RuntimeException( sprintf( 'No product for code "%1$s" found', $entry['code'] ) );
			}

			$listItem->setId( null );
			$listItem->setType( $entry['list-type'] );
			$listItem->setDateStart( $entry['list-start'] );
			$listItem->setDateEnd( $entry['list-end'] );
			$listItem->setConfig( $entry['list-config'] );
			$listItem->setPosition( $entry['list-position'] );
			$listItem->setStatus( $entry['list-status'] );
			$listItem->setRefId( $codes[$entry['code']] );

			$listManager->save( $listItem, false );
		}
	}


	/**
	 * Adds stock levels to the given product in the database.
	 *
	 * @param string $productId ID of the product item where the stock levels should be associated to
	 * @param array $data Two dimensional associative list of product stock data
	 */
	protected function addProductStock( string $productId, array $data )
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'stock/type' );

		$types = [];
		foreach( $manager->search( $manager->filter() ) as $id => $item ) {
			$types[$item->getCode()] = $id;
		}

		$manager = \Aimeos\MShop::create( $this->context(), 'stock' );

		$item = $manager->create();
		$item->setProductId( $productId );

		foreach( $data as $entry )
		{
			$item->setId( null );
			$item->setDateBack( $entry['dateback'] );
			$item->setStockLevel( $entry['stocklevel'] );
			$item->setType( $entry['type'] );

			$manager->save( $item, false );
		}
	}


	/**
	 * Returns the attribute for the given code, type and domain
	 *
	 * @param string $domain Domain the attribute belongs to
	 * @param string $type Attribute type
	 * @param string $code Attribute code
	 * @return \Aimeos\MShop\Attribute\Item\Iface|null Found attribute item or null if not available
	 */
	protected function getAttributeItem( $domain, $type, $code )
	{
		if( $this->attributes === null )
		{
			$manager = \Aimeos\MShop::create( $this->context(), 'attribute' );

			foreach( $manager->search( $manager->filter() ) as $item ) {
				$this->attributes[$item->getDomain()][$item->getType()][$item->getCode()] = $item;
			}
		}

		if( isset( $this->attributes[$domain][$type][$code] ) ) {
			return $this->attributes[$domain][$type][$code];
		}
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
		$context = $this->context();
		$key = str_replace( '/', '.', $name );

		$manager = \Aimeos\MShop::create( $context, $refdomain );
		$listManager = \Aimeos\MShop::create( $context, $name );


		$search = $manager->filter();
		$expr = array(
			$search->compare( '==', $refdomain . '.domain', $domain ),
			$search->compare( '=~', $refdomain . '.label', 'Demo' ),
		);
		$search->setConditions( $search->and( $expr ) );

		$ids = $manager->search( $search )->keys()->toArray();
		$manager->delete( $ids );


		$search = $listManager->filter();
		$expr = array(
			$search->compare( '==', $key . '.parentid', $parentid ),
			$search->compare( '==', $key . '.domain', $refdomain ),
			$search->compare( '==', $key . '.refid', $ids ),
		);
		$search->setConditions( $search->and( $expr ) );

		$listManager->delete( $listManager->search( $search )->toArray() );
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
		$context = $this->context();
		$key = str_replace( '/', '.', $name );

		$manager = \Aimeos\MShop::create( $context, $refdomain );
		$listManager = \Aimeos\MShop::create( $context, $name );


		$search = $listManager->filter();
		$expr = array(
			$search->compare( '==', $key . '.parentid', $parentid ),
			$search->compare( '==', $key . '.domain', $refdomain ),
		);
		$search->setConditions( $search->and( $expr ) );

		do
		{
			$refIds = $listIds = $map = [];
			$result = $listManager->search( $search );

			foreach( $result as $id => $listItem )
			{
				$refIds[] = $listItem->getRefId();
				$map[$listItem->getRefId()][] = $id;
			}


			$search = $manager->filter();
			$search->setConditions( $search->compare( '==', $refdomain . '.id', $refIds ) );
			$ids = $manager->search( $search )->keys()->toArray();

			foreach( array_diff( $refIds, $ids ) as $refId ) {
				$listIds = array_merge( $listIds, $map[$refId] );
			}

			$listManager->delete( $listIds );


			$count = count( $result );
			$start += $count;
			$search->slice( $start );
		}
		while( $count == $search->getLimit() );


		$search = $manager->filter();
		$search->setConditions( $search->compare( '=~', $refdomain . '.label', 'Demo' ) );
		$ids = $manager->search( $search )->keys()->toArray();

		$search = $listManager->filter();
		$expr = array(
			$search->compare( '==', $key . '.parentid', $parentid ),
			$search->compare( '==', $key . '.refid', $ids ),
			$search->compare( '==', $key . '.domain', $refdomain ),
		);
		$search->setConditions( $search->and( $expr ) );

		$listManager->delete( $listManager->search( $search )->toArray() );
	}


	/**
	 * Starts a new transation
	 */
	protected function txBegin()
	{
		$dbm = $this->context()->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->begin();
		$dbm->release( $conn );
	}


	/**
	 * Commits an existing transaction
	 */
	protected function txCommit()
	{
		$dbm = $this->context()->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->commit();
		$dbm->release( $conn );
	}
}
