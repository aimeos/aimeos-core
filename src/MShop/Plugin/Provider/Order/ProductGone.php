<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks the current availability of the products in a basket
 *
 * Products can be removed or disabled by the shop owner or the time frame a
 * product is avialable can pass by. In these cases, the plugin notifies the
 * customers that they have to remove the product from the basket before they
 * can proceed in the checkout process.
 *
 * The plugin is executed for the basket and the checkout summary page.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductGone
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MShop\Order\Item\Iface $p ) : \Aimeos\MShop\Plugin\Provider\Iface
	{
		$p->attach( $this->object(), 'check.after' );
		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 */
	public function update( \Aimeos\MShop\Order\Item\Iface $order, string $action, $value = null )
	{
		if( !in_array( 'order/product', (array) $value ) ) {
			return $value;
		}

		$notAvailable = [];
		$productIds = $order->getProducts()->getProductId()->toArray();
		$productManager = \Aimeos\MShop::create( $this->context(), 'product' );

		$filter = $productManager->filter( true )->add( 'product.id', '==', $productIds );
		$checkItems = $productManager->search( $filter );

		foreach( $order->getProducts() as $position => $orderProduct )
		{
			if( ( $product = $checkItems->get( $orderProduct->getProductId() ) ) === null )
			{
				$notAvailable[$position] = 'gone.notexist';
				continue;
			}
		}

		if( count( $notAvailable ) > 0 )
		{
			$code = array( 'product' => $notAvailable );
			$msg = $this->context()->translate( 'mshop', 'Products in basket not available' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $code );
		}

		return $value;
	}
}
