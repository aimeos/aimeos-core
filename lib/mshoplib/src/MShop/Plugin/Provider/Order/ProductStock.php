<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks the products in a basket for sufficient stocklevel
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductStock
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this, 'check.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		$class = '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface';
		if( !( $order instanceof $class ) ) {
			throw new \Aimeos\MShop\Plugin\Order\Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) ) {
			return true;
		}


		$context = $this->getContext();
		$outOfStock = $productQuantities = $positions = array();
		$siteConfig = $context->getLocale()->getSite()->getConfig();

		foreach( $order->getProducts() as $position => $pr )
		{
			$productQuantities[$pr->getProductId()] = $pr->getQuantity();
			$positions[$pr->getProductId()] = $position;
		}

		$stockManager = \Aimeos\MShop\Factory::createManager( $context, 'product/stock' );

		$search = $stockManager->createSearch();
		$expr = array( $search->compare( '==', 'product.stock.parentid', array_keys( $productQuantities ) ) );

		if( isset( $siteConfig['repository'] ) ) {
			$expr[] = $search->compare( '==', 'product.stock.warehouse.code', $siteConfig['warehouse'] );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$checkItems = $stockManager->searchItems( $search );

		foreach( $checkItems as $checkItem )
		{
			$parentid = $checkItem->getParentId();
			$stocklevel = $checkItem->getStocklevel();

			if( $stocklevel !== null && $stocklevel < $productQuantities[$parentid] ) {
				$outOfStock[$positions[$parentid]] = 'stock.notenough';
			}
		}

		if( count( $outOfStock ) > 0 )
		{
			$code = array( 'product' => $outOfStock );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( 'Products out of stock' ), -1, null, $code );
		}
		return true;
	}
}
