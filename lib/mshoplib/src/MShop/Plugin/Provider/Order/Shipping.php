<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Free shipping implementation if ordered product sum is above a certain value
 *
 * Sets the shipping costs to zero if the configured threshold is met or exceeded.
 * Only the costs of the delivery option are set to 0.00, not the shipping costs
 * of specific product items!
 *
 * Example:
 * - threshold: 'EUR' => '50.00'
 *
 * There would be no shipping costs for orders of 50 EUR or above. The rebates
 * granted by coupons for example are included into the calculation of the total
 * basket value.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class Shipping
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'threshold' => array(
			'code' => 'threshold',
			'internalcode' => 'threshold',
			'label' => 'Free shipping threshold per currency',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
			'required' => false,
		),
	);


	/**
	 * Checks the backend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		$errors = parent::checkConfigBE( $attributes );

		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		$plugin = $this->getObject();

		$p->attach( $plugin, 'addCoupon.after' );
		$p->attach( $plugin, 'deleteCoupon.after' );
		$p->attach( $plugin, 'setCoupons.after' );
		$p->attach( $plugin, 'setCoupon.after' );
		$p->attach( $plugin, 'addProduct.after' );
		$p->attach( $plugin, 'deleteProduct.after' );
		$p->attach( $plugin, 'setProducts.after' );
		$p->attach( $plugin, 'addService.after' );
		$p->attach( $plugin, 'deleteService.after' );
		$p->attach( $plugin, 'setServices.after' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Iface::class, $order );

		$services = $order->getServices();
		$currency = $order->getPrice()->getCurrencyId();
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
		$threshold = $this->getItemBase()->getConfigValue( 'threshold/' . $currency );

		if( $threshold && ( $serviceItems = $services->get( $type ) ) )
		{
			foreach( $serviceItems as $key => $service )
			{
				$price = $service->getPrice();

				if( $this->checkThreshold( $order->getProducts(), $threshold ) ) {
					$price = $price->setRebate( $price->getCosts() )->setCosts( '0.00' );
				}

				$serviceItems[$key] = $service->setPrice( $price );
			}

			$order->setServices( $services->set( $type, $serviceItems )->toArray() );
		}

		return $value;
	}


	/**
	 * Tests if the shipping threshold is reached and updates the price accordingly
	 *
	 * @param \Aimeos\Map $orderProducts List of ordered products implementing \Aimeos\MShop\Order\Item\Base\Product\Iface
	 * @param string $threshold Threshold for the actual currency
	 * @return bool True if threshold is reached, false if not
	 */
	protected function checkThreshold( \Aimeos\Map $orderProducts, string $threshold ) : bool
	{
		$sum = \Aimeos\MShop::create( $this->getContext(), 'price' )->create();

		foreach( $orderProducts as $product )
		{
			if( ( $product->getFlags() & \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE ) === 0 ) {
				$sum = $sum->addItem( $product->getPrice(), $product->getQuantity() );
			}
		}

		if( $sum->getValue() + $sum->getRebate() >= $threshold ) {
			return true;
		}

		return false;
	}
}
