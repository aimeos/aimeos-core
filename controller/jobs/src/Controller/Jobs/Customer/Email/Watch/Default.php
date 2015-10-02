<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Customer
 */


/**
 * Product notification e-mail job controller.
 *
 * @package Controller
 * @subpackage Customer
 */
class Controller_Jobs_Customer_Email_Watch_Default
	extends Controller_Jobs_Abstract
	implements Controller_Jobs_Interface
{
	private $_client;
	private $_warehouses;


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Product notification e-mails' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription()
	{
		return $this->_getContext()->getI18n()->dt( 'controller/jobs', 'Sends e-mails for watched products' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws Controller_Jobs_Exception If an error occurs
	 */
	public function run()
	{
		$langIds = array();
		$context = $this->_getContext();
		$typeId = $this->_getListTypeItem( 'watch' )->getId();

		$localeManager = MShop_Factory::createManager( $context, 'locale' );
		$custManager = MShop_Factory::createManager( $context, 'customer' );

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
				$search->compare( '==', 'customer.list.typeid', $typeId ),
				$search->compare( '==', 'customer.list.domain', 'product' ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );
			$search->setSortations( array( $search->sort( '+', 'customer.id' ) ) );

			$start = 0;

			do
			{
				$customers = $custManager->searchItems( $search );

				$this->_execute( $context, $customers, $typeId );

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
	 * @param MShop_Context_Item_Interface $context Context item object
	 * @param array $customers List of customer items implementing MShop_Customer_Item_Interface
	 * @param string $listTypeId Customer list type ID
	 */
	protected function _execute( MShop_Context_Item_Interface $context, array $customers, $listTypeId )
	{
		$prodIds = $custIds = array();
		$whItem = $this->_getWarehouseItem( 'default' );
		$listManager = MShop_Factory::createManager( $context, 'customer/list' );
		$listItems = $this->_getListItems( $context, array_keys( $customers ), $listTypeId );

		foreach( $listItems as $id => $listItem )
		{
			$refId = $listItem->getRefId();
			$custIds[ $listItem->getParentId() ][$id] = $refId;
			$prodIds[$refId] = $refId;
		}

		$date = date( 'Y-m-d H:i:s' );
		$products = $this->_getProducts( $context, $prodIds, $whItem->getId() );

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
				$custProducts = $this->_getListProducts( $custListItems, $products );

				if( !empty( $custProducts ) )
				{
					$this->_sendMail( $context, $customers[$custId]->getPaymentAddress(), $custProducts );
					$listIds += array_keys( $custProducts );
				}
			}
			catch( Exception $e )
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
	 * @param MShop_Context_Item_Interface $context Context item object
	 * @return Client_Html_Interface Product notification e-mail client
	 */
	protected function _getClient( MShop_Context_Item_Interface $context )
	{
		if( !isset( $this->_client ) )
		{
			$templatePaths = $this->_getAimeos()->getCustomPaths( 'client/html' );
			$this->_client = Client_Html_Email_Watch_Factory::createClient( $context, $templatePaths );
		}

		return $this->_client;
	}


	/**
	 * Returns the list items for the given customer IDs and list type ID
	 *
	 * @param MShop_Context_Item_Interface $context Context item object
	 * @param array $custIds List of customer IDs
	 * @param string $listTypeId Customer list type ID
	 * @return array List of customer list items implementing MShop_Common_Item_List_Interface
	 */
	protected function _getListItems( MShop_Context_Item_Interface $context, array $custIds, $listTypeId )
	{
		$listManager = MShop_Factory::createManager( $context, 'customer/list' );

		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', 'customer.list.parentid', $custIds ),
			$search->compare( '==', 'customer.list.typeid', $listTypeId ),
			$search->compare( '==', 'customer.list.domain', 'product' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		return $listManager->searchItems( $search );
	}


	/**
	 * Returns a filtered list of products for which a notification should be sent
	 *
	 * @param array $listItems List of customer list items implementing MShop_Common_Item_List_Interface
	 * @param array $products List of product items implementing MShop_Product_Item_Interface
	 * @return array Multi-dimensional associative list of list IDs as key and product / price item maps as values
	 */
	protected function _getListProducts( array $listItems, array $products )
	{
		$result = array();
		$priceManager = MShop_Factory::createManager( $this->_getContext(), 'price' );

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
			catch( Exception $e ) { ; } // no price available
		}

		return $result;
	}


	/**
	 * Returns the products for the given IDs which are in stock in the warehouse
	 *
	 * @param MShop_Context_Item_Interface $context Context item object
	 * @param array $prodIds List of product IDs
	 * @param string $whId Unique warehouse ID
	 */
	protected function _getProducts( MShop_Context_Item_Interface $context, array $prodIds, $whId )
	{
		$productManager = MShop_Factory::createManager( $context, 'product' );
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
	 * @return MShop_Common_Item_Type_Interface List type item
	 * @throws Controller_Jobs_Exception If the list type item wasn't found
	 */
	protected function _getListTypeItem( $code )
	{
		$manager = MShop_Factory::createManager( $this->_getContext(), 'customer/list/type' );

		$search = $manager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'customer.list.type.code', $code ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Controller_Jobs_Exception( sprintf( 'List type for domain "%1$s" and code "%2$s" not found', 'customer', $code ) );
		}

		return $item;
	}


	/**
	 * Returns the warehouse item for the given code.
	 *
	 * @param string $code Unique code of the warehouse item
	 * @return MShop_Product_Item_Stock_Warehouse_Interface Warehouse item
	 * @throws Controller_Jobs_Exception If the warehouse item wasn't found
	 */
	protected function _getWarehouseItem( $code )
	{
		if( !isset( $this->_warehouses ) )
		{
			$manager = MShop_Factory::createManager( $this->_getContext(), 'product/stock/warehouse' );
			$search = $manager->createSearch( true );

			$this->_warehouses = array();
			foreach( $manager->searchItems( $search ) as $whItem ) {
				$this->_warehouses[ $whItem->getCode() ] = $whItem;
			}
		}

		if( !isset( $this->_warehouses[$code] ) ) {
			throw new Controller_Jobs_Exception( sprintf( 'No warehouse "%1$s" found', $code ) );
		}

		return $this->_warehouses[$code];
	}


	/**
	 * Sends the notification e-mail for the given customer address and products
	 *
	 * @param MShop_Context_Item_Interface $context Context item object
	 * @param MShop_Common_Item_Address_Interface $address Payment address of the customer
	 * @param array $products List of products a notification should be sent for
	 */
	protected function _sendMail( MShop_Context_Item_Interface $context,
		MShop_Common_Item_Address_Interface $address, array $products )
	{
		$view = $context->getView();
		$view->extProducts = $products;
		$view->extAddressItem = $address;

		$helper = new MW_View_Helper_Translate_Default( $view, $context->getI18n( $address->getLanguageId() ) );
		$view->addHelper( 'translate', $helper );

		$mailer = $context->getMail();
		$message = $mailer->createMessage();

		$helper = new MW_View_Helper_Mail_Default( $view, $message );
		$view->addHelper( 'mail', $helper );

		$client = $this->_getClient( $context );
		$client->setView( $view );
		$client->getHeader();
		$client->getBody();

		$mailer->send( $message );
	}
}
