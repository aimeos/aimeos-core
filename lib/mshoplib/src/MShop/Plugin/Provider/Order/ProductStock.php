<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Checks the products in a basket for sufficient stocklevel
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ProductStock implements MShop_Plugin_Provider_Interface
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
		$p->addListener( $this, 'isComplete.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws MShop_Plugin_Provider_Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$this->_context->getLogger()->log(__METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG);

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			throw new MShop_Plugin_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) ) {
			return true;
		}

		$config = $this->_item->getConfig();
		$this->_context->getLogger()->log(__METHOD__ . ':: config: ' . print_r( $config, true ), MW_Logger_Abstract::DEBUG);

		$siteConfig = $this->_context->getLocale()->getSite()->getConfig();

		$outOfStock = $productQuantities = $positions = array();

		foreach ( $order->getProducts() as $position => $pr )
		{
			$productQuantities[$pr->getProductId()] = $pr->getQuantity();
			$positions[$pr->getProductId()] = $position;
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$stockManager = $productManager->getSubManager('stock');

		$search = $stockManager->createSearch();
		$expr = array( $search->compare( '==', 'product.stock.productid', array_keys( $productQuantities ) ) );

		if( isset( $siteConfig['repository'] ) ) {
			$expr[] = $search->compare( '==', 'product.stock.warehouse.code', $siteConfig['repository'] );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$checkItems = $stockManager->searchItems( $search );

		foreach ( $checkItems as $checkItem )
		{
			if ( $checkItem->getStocklevel() < $productQuantities[$checkItem->getProductId()] ) {
				$outOfStock[$positions[$checkItem->getProductId()]] = 'product.stock';
			}
		}

		if ( count( $outOfStock ) > 0 )
		{
			$code = array( 'product' => $outOfStock );
			throw new MShop_Plugin_Provider_Exception( sprintf( 'Products out of stock' ), -1, null, $code );
		}
		return true;
	}
}
