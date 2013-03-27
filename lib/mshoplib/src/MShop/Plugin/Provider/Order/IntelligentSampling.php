<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 * @version $Id: IntelligentSampling.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Intelligent sampling plugin implementation to add special sample packs as teasers to orders of
 * customers if the product wasn't ordered yet by the customer.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_IntelligentSampling implements MShop_Plugin_Provider_Interface
{
	private $_item;
	private $_context;


	/**
	 * Initializes the plugin instance
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Plugin_Item_Interface $item Plugin item object
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Plugin_Item_Interface $item )
	{
		$this->_item = $item;
		$this->_context = $context;
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'setOrder.before' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @todo Add special sample packs as teasers to orders of customers if the product wasn't ordered yet by the customer
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$this->_context->getLogger()->log(__METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG);

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			throw new MShop_Plugin_Exception(sprintf('Received notification from "%1$s" which doesn\'t implement "%2$s"', get_class($order), $class));
		}

		$config = $this->_item->getConfig();
		$this->_context->getLogger()->log(__METHOD__ . ':: config: ' . print_r( $config, true ), MW_Logger_Abstract::DEBUG);


		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_context );
		$orderBaseManager = $orderManager->getSubManager('base');
		$orderProductManager = $orderBaseManager->getSubManager('product');

		$search = $orderManager->createSearch();
		$expr[] = $search->compare( '==', 'order.base.customerid', $order->getCustomerId() );
		$expr[] = $search->compare( '==', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_RECEIVED );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$orderItems = $orderManager->searchItems( $search );

		// do nothing, if firsttime = 1 and there already exist invoices
		if ( isset( $config['firsttime'] ) && $config['firsttime'] != 0 && count( $orderItems ) > 0 )
		{
			$msg = __METHOD__  . ': firsttime param is activate and there are already orders before';
			$this->_context->getLogger()->log( $msg, MW_Logger_Abstract::DEBUG);
			return false;
		}

		// put all curent und past product codes in an array
		$productCodes = array();

		foreach ( $order->getProducts() as $product ) {
			$productCodes[] = $product->getProductCode();
		}

		$baseIds = array();
		foreach( $orderItems as $orderItem ) {
			$baseIds[] = $orderItem->getBaseId();
		}

		$search = $orderProductManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.baseid', $baseIds ) );
		$products = $orderProductManager->searchItems( $search );

		foreach ( $products as $product ) {
			$productCodes[] = $product->getProductCode();
		}

		$criteria = new MW_Common_Criteria_PHP();
		$types = array( 'exists()' => 'bool' );
		$translations = array( 'exists()' => 'in_array($1,$productCodes)' );

		$result = array();

		foreach( array_keys( $config['samples'] ) as $sampleCode )
		{
			$conditions = $criteria->toConditions( $config['samples'][$sampleCode]['conditions'] );

			if ( @eval( "return ( ( " . $conditions->toString( $types, $translations ) . " ) ? true : false );" ) ){
				$result[] = $sampleCode;
			}
		}

		// If no more samples are available add the alternative free accessory product
		if ( ( $sampleCode = reset( $result ) ) === false )
		{
			if( isset( $config['alternative'] ) ) {
				$sampleCode = $config['alternative'];
			} else {
				$msg = __METHOD__  . ': No more samples and no alternative free accessory product are available';
				$this->_context->getLogger()->log($msg, MW_Logger_Abstract::DEBUG);
				return false;
			}
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $productManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'product.code', $sampleCode ) );
		$products = $productManager->searchItems( $search );

		if( ( $product = reset( $products ) ) === false )
		{
			$msg = sprintf( 'An error occured in a search. Product with code "%1$s" not found.', $sampleCode );
			$this->_context->getLogger()->log($msg, MW_Logger_Abstract::NOTICE);
			return false;
		}

		$orderProduct = $orderProductManager->createItem();
		$orderProduct->copyFrom( $product );
		$orderProduct->setQuantity(1);

		$order->addProduct( $orderProduct );

		return true;
	}
}
