<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
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
		$arcavias = $this->_getArcavias();
		$context = $this->_getContext();
		$config = $context->getConfig();
		$mailer = $context->getMail();
		$view = $context->getView();


		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$client = Client_Html_Email_Watch_Factory::createClient( $context, $templatePaths );


		$typeItem = $this->_getListTypeItem( 'watch' );
		$whItem = $this->_getWarehouseItem( 'default' );

		$productManager = MShop_Factory::createManager( $context, 'product' );
		$listManager = MShop_Factory::createManager( $context, 'customer/list' );
		$manager = MShop_Factory::createManager( $context, 'customer' );

		$search = $manager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'customer.list.typeid', $typeItem->getId() ),
			$search->compare( '==', 'customer.list.domain', 'product' ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'customer.id' ) ) );

		$date = date( 'Y-m-d H:i:s' );
		$domains = array( 'text', 'price', 'media' );
		$start = 0;

		do
		{
			$prodIds = $custIds = array();
			$customers = $manager->searchItems( $search );


			$listSearch = $listManager->createSearch();
			$expr = array(
				$listSearch->compare( '==', 'customer.list.parentid', array_keys( $customers ) ),
				$listSearch->compare( '==', 'customer.list.typeid', $typeItem->getId() ),
				$listSearch->compare( '==', 'customer.list.domain', 'product' ),
			);
			$listSearch->setConditions( $listSearch->combine( '&&', $expr ) );
			$listSearch->setSlice( 0, $listSearch->getSliceSize() * 100 );

			$listItems = $listManager->searchItems( $listSearch );

			foreach( $listItems as $id => $listItem )
			{
				$refId = $listItem->getRefId();
				$custIds[ $listItem->getParentId() ][$id] = $refId;
				$prodIds[$refId] = $refId;
			}


			$prodSearch = $productManager->createSearch( true );
			$expr = array(
				$prodSearch->compare( '==', 'product.id', $prodIds ),
				$prodSearch->getConditions(),
				$prodSearch->compare( '==', 'product.stock.warehouseid', $whItem->getId() ),
				$prodSearch->compare( '>', 'product.stock.stocklevel', 0 ),
			);
			$prodSearch->setConditions( $prodSearch->combine( '&&', $expr ) );
			$prodSearch->setSlice( 0, 0x7fffffff );

			$products = $productManager->searchItems( $prodSearch, $domains );


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

				$custProducts = $this->getProducts( $custListItems, $products );

				try
				{
					if( !empty( $custProducts ) )
					{
						$view->extProducts = $custProducts;
						$view->extAddressItem = $customers[$custId]->getPaymentAddress();

						$helper = new MW_View_Helper_Translate_Default( $view, $context->getI18n( $view->extAddressItem->getLanguageId() ) );
						$view->addHelper( 'translate', $helper );

						$message = $mailer->createMessage();
						$helper = new MW_View_Helper_Mail_Default( $view, $message );
						$view->addHelper( 'mail', $helper );

						$client->setView( $view );
						$client->getHeader();
						$client->getBody();

						$mailer->send( $message );

						$listManager->deleteItems( $listIds + array_keys( $custProducts ) );
					}
				}
				catch( Exception $e )
				{
					$str = 'Error while trying to send product notification e-mail for customer ID "%1$s": %2$s';
					$msg = sprintf( $str, $custId, $e->getMessage() );
					$context->getLogger()->log( $msg );
				}
			}

			$count = count( $customers );
			$start += $count;
			$search->setSlice( $start );
		}
		while( $count >= $search->getSliceSize() );
	}


	public function getProducts( array $listItems, array $products )
	{
		$result = array();

		foreach( $listItems as $id => $listItem )
		{
			$refId = $listItem->getRefId();
			$config = $listItem->getConfig();

			if( isset( $products[$refId] ) )
			{
				$prices = $products[$refId]->getRefItems( 'price', 'default', 'default' );
				$price = reset( $prices );

				if( isset( $config['stock'] ) && $config['stock'] == 1 ||
					isset( $config['price'] ) && $config['price'] == 1 &&
					$price !== false && $config['price-value'] > $price->getValue()
				) {
					$result[$id] = $products[$refId];
				}
			}
		}

		return $result;
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
		$manager = MShop_Factory::createManager( $this->_getContext(), 'product/stock/warehouse' );

		$search = $manager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'product.stock.warehouse.code', $code ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Controller_Jobs_Exception( sprintf( 'No warehouse "%1$s" found', $code ) );
		}

		return $item;
	}
}
