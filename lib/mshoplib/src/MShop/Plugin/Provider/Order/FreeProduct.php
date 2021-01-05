<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Plugin
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Adds the configured subscription product to the basket for free
 *
 * Sets the price of the configured product to 0.00 and uses it's price as rebate for
 * the configured number of times. This is bound to the e-mail address of the customer.
 *
 * The following options are available:
 * - productcode: '...' (SKU code of the product that should be available for free)
 * - count: ... (how often the product can be bought for free)
 *
 * @package MShop
 * @subpackage Plugin
 */
class FreeProduct
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'productcode' => array(
			'code' => 'productcode',
			'internalcode' => 'productcode',
			'label' => 'SKU of the free product',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		),
		'count' => array(
			'code' => 'count',
			'internalcode' => 'count',
			'label' => 'Number of times the product is available for free',
			'type' => 'integer',
			'internaltype' => 'integer',
			'default' => 1,
			'required' => true,
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
		$p->attach( $this->getObject(), 'addProduct.after' );
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
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $value );

		$code = $this->getConfigValue( 'productcode' );
		$addresses = $order->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

		if( $value->getProductCode() !== $code || ( $address = current( $addresses ) ) === false ) {
			return $value;
		}

		$email = $address->getEmail();
		$count = $this->getConfigValue( 'count' );
		$status = \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED;

		$manager = \Aimeos\MShop::create( $this->getContext(), 'order' );

		$search = $manager->filter();
		$expr = [
			$search->compare( '==', 'order.base.address.email', $email ),
			$search->compare( '==', 'order.base.product.prodcode', $code ),
			$search->compare( '>=', 'order.statuspayment', $status ),
		];
		$search->setConditions( $search->and( $expr ) );

		$result = $manager->aggregate( $search, 'order.base.address.email', 'order.base.product.quantity', 'sum' );

		if( isset( $result[$email] ) && $result[$email] < $count ) {
			$value->setPrice( $value->getPrice()->setRebate( $value->getPrice()->getValue() )->setValue( '0.00' ) );
		}

		return $value;
	}
}
