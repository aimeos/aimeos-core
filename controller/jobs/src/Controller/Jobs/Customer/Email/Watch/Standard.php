<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Customer
 */


namespace Aimeos\Controller\Jobs\Customer\Email\Watch;


/**
 * Product notification e-mail job controller.
 *
 * @package Controller
 * @subpackage Customer
 */
class Standard
	extends \Aimeos\Controller\Jobs\Base
	implements \Aimeos\Controller\Jobs\Iface
{
	private $client;
	private $warehouses;


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Product notification e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->getContext()->getI18n()->dt( 'controller/jobs', 'Sends e-mails for watched products' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$langIds = array();
		$context = $this->getContext();
		$typeId = $this->getListTypeItem( 'watch' )->getId();

		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );
		$custManager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );

		$localeItems = $localeManager->searchItems( $localeManager->createSearch() );

		foreach( $localeItems as $localeItem )
		{
			$langId = $localeItem->getLanguageId();

			if( isset( $langIds[$langId] ) ) {
				continue;
			}

			$langIds[$langId] = true;
			// fetch language specific text and media items for products
			$context->getLocale()->setLanguageId( $langId );

			$search = $custManager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'customer.languageid', $langId ),
				$search->compare( '==', 'customer.lists.typeid', $typeId ),
				$search->compare( '==', 'customer.lists.domain', 'product' ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', 'customer.id' ) ) );

			$start = 0;

			do
			{
				$customers = $custManager->searchItems( $search );

				$this->execute( $context, $customers, $typeId );

				$count = count( $customers );
				$start += $count;
				$search->setSlice( $start );
			}
			while( $count >= $search->getSliceSize() );
		}
	}


	/**
	 * Sends product notifications for the given customers in their language
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $customers List of customer items implementing \Aimeos\MShop\Customer\Item\Iface
	 * @param string $listTypeId Customer list type ID
	 */
	protected function execute( \Aimeos\MShop\Context\Item\Iface $context, array $customers, $listTypeId )
	{
		$prodIds = $custIds = array();
		$whItem = $this->getWarehouseItem( 'default' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, 'customer/lists' );
		$listItems = $this->getListItems( $context, array_keys( $customers ), $listTypeId );

		foreach( $listItems as $id => $listItem )
		{
			$refId = $listItem->getRefId();
			$custIds[ $listItem->getParentId() ][$id] = $refId;
			$prodIds[$refId] = $refId;
		}

		$date = date( 'Y-m-d H:i:s' );
		$products = $this->getProducts( $context, $prodIds, $whItem->getId() );

		foreach( $custIds as $custId => $list )
		{
			$custListItems = $listIds = array();

			foreach( $list as $listId => $prodId )
			{
				$listItem = $listItems[$listId];

				if( $listItem->getDateEnd() < $date ) {
					$listIds[] = $listId;
				}

				$custListItems[$listId] = $listItems[$listId];
			}

			try
			{
				$custProducts = $this->getListProducts( $custListItems, $products );

				if( !empty( $custProducts ) )
				{
					$this->sendMail( $context, $customers[$custId]->getPaymentAddress(), $custProducts );
					$listIds += array_keys( $custProducts );
				}
			}
			catch( \Exception $e )
			{
				$str = 'Error while trying to send product notification e-mail for customer ID "%1$s": %2$s';
				$msg = sprintf( $str, $custId, $e->getMessage() );
				$context->getLogger()->log( $msg );
			}

			$listManager->deleteItems( $listIds );
		}
	}


	/**
	 * Returns the product notification e-mail client
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @return \Aimeos\Client\Html\Iface Product notification e-mail client
	 */
	protected function getClient( \Aimeos\MShop\Context\Item\Iface $context )
	{
		if( !isset( $this->client ) )
		{
			$templatePaths = $this->getAimeos()->getCustomPaths( 'client/html' );
			$this->client = \Aimeos\Client\Html\Email\Watch\Factory::createClient( $context, $templatePaths );
		}

		return $this->client;
	}


	/**
	 * Returns the list items for the given customer IDs and list type ID
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $custIds List of customer IDs
	 * @param string $listTypeId Customer list type ID
	 * @return array List of customer list items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( \Aimeos\MShop\Context\Item\Iface $context, array $custIds, $listTypeId )
	{
		$listManager = \Aimeos\MShop\Factory::createManager( $context, 'customer/lists' );

		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', 'customer.lists.parentid', $custIds ),
			$search->compare( '==', 'customer.lists.typeid', $listTypeId ),
			$search->compare( '==', 'customer.lists.domain', 'product' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		return $listManager->searchItems( $search );
	}


	/**
	 * Returns a filtered list of products for which a notification should be sent
	 *
	 * @param array $listItems List of customer list items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 * @param array $products List of product items implementing \Aimeos\MShop\Product\Item\Iface
	 * @return array Multi-dimensional associative list of list IDs as key and product / price item maps as values
	 */
	protected function getListProducts( array $listItems, array $products )
	{
		$result = array();
		$priceManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'price' );

		foreach( $listItems as $id => $listItem )
		{
			try
			{
				$refId = $listItem->getRefId();
				$config = $listItem->getConfig();

				if( isset( $products[$refId] ) )
				{
					$prices = $products[$refId]->getRefItems( 'price', 'default', 'default' );
					$currencyId = ( isset( $config['currency'] ) ? $config['currency'] : null );

					$price = $priceManager->getLowestPrice( $prices, 1, $currencyId );

					if( isset( $config['stock'] ) && $config['stock'] == 1 ||
						isset( $config['price'] ) && $config['price'] == 1 &&
						isset( $config['pricevalue'] ) && $config['pricevalue'] > $price->getValue()
					) {
						$result[$id]['item'] = $products[$refId];
						$result[$id]['price'] = $price;
					}
				}
			}
			catch( \Exception $e ) { ; } // no price available
		}

		return $result;
	}


	/**
	 * Returns the products for the given IDs which are in stock in the warehouse
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param array $prodIds List of product IDs
	 * @param string $whId Unique warehouse ID
	 */
	protected function getProducts( \Aimeos\MShop\Context\Item\Iface $context, array $prodIds, $whId )
	{
		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
		$search = $productManager->createSearch( true );
		$domains = array( 'text', 'price', 'media' );

		$stockExpr = array(
			$search->compare( '==', 'product.stock.stocklevel', null ),
			$search->compare( '>', 'product.stock.stocklevel', 0 ),
		);

		$expr = array(
			$search->compare( '==', 'product.id', $prodIds ),
			$search->getConditions(),
			$search->compare( '==', 'product.stock.warehouseid', $whId ),
			$search->combine( '||', $stockExpr ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		return $productManager->searchItems( $search, $domains );
	}


	/**
	 * Returns the customer list type item for the given type code.
	 *
	 * @param string $code Unique code of the list type item
	 * @return \Aimeos\MShop\Common\Item\Type\Iface List type item
	 * @throws \Aimeos\Controller\Jobs\Exception If the list type item wasn't found
	 */
	protected function getListTypeItem( $code )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'customer/lists/type' );

		$search = $manager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'customer.lists.type.code', $code ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'List type for domain "%1$s" and code "%2$s" not found', 'customer', $code ) );
		}

		return $item;
	}


	/**
	 * Returns the warehouse item for the given code.
	 *
	 * @param string $code Unique code of the warehouse item
	 * @return \Aimeos\MShop\Product\Item\Stock\Warehouse\Iface Warehouse item
	 * @throws \Aimeos\Controller\Jobs\Exception If the warehouse item wasn't found
	 */
	protected function getWarehouseItem( $code )
	{
		if( !isset( $this->warehouses ) )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/stock/warehouse' );
			$search = $manager->createSearch( true );

			$this->warehouses = array();
			foreach( $manager->searchItems( $search ) as $whItem ) {
				$this->warehouses[ $whItem->getCode() ] = $whItem;
			}
		}

		if( !isset( $this->warehouses[$code] ) ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'No warehouse "%1$s" found', $code ) );
		}

		return $this->warehouses[$code];
	}


	/**
	 * Sends the notification e-mail for the given customer address and products
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address of the customer
	 * @param array $products List of products a notification should be sent for
	 */
	protected function sendMail( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\MShop\Common\Item\Address\Iface $address, array $products )
	{
		$view = $context->getView();
		$view->extProducts = $products;
		$view->extAddressItem = $address;

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $context->getI18n( $address->getLanguageId() ) );
		$view->addHelper( 'translate', $helper );

		$mailer = $context->getMail();
		$message = $mailer->createMessage();

		$helper = new \Aimeos\MW\View\Helper\Mail\Standard( $view, $message );
		$view->addHelper( 'mail', $helper );

		$client = $this->getClient( $context );
		$client->setView( $view );
		$client->getHeader();
		$client->getBody();

		$mailer->send( $message );
	}
}
