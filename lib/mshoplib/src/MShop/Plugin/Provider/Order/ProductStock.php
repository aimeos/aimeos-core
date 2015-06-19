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
class MShop_Plugin_Provider_Order_ProductStock
	extends MShop_Plugin_Provider_Order_Abstract
	implements MShop_Plugin_Provider_Factory_Interface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'check.after' );
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
		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) ) {
			return true;
		}


		$context = $this->_getContext();
		$outOfStock = $productQuantities = $positions = array();
		$siteConfig = $context->getLocale()->getSite()->getConfig();

		foreach ( $order->getProducts() as $position => $pr )
		{
			$productQuantities[$pr->getProductId()] = $pr->getQuantity();
			$positions[$pr->getProductId()] = $position;
		}

		$stockManager = MShop_Factory::createManager( $context, 'product/stock' );

		$search = $stockManager->createSearch();
		$expr = array( $search->compare( '==', 'product.stock.productid', array_keys( $productQuantities ) ) );

		if( isset( $siteConfig['repository'] ) ) {
			$expr[] = $search->compare( '==', 'product.stock.warehouse.code', $siteConfig['repository'] );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$checkItems = $stockManager->searchItems( $search );

		foreach ( $checkItems as $checkItem )
		{
			$stocklevel = $checkItem->getStocklevel();

			if( $stocklevel !== null && $stocklevel < $productQuantities[$checkItem->getProductId()] ) {
				$outOfStock[$positions[$checkItem->getProductId()]] = 'stock.notenough';
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
