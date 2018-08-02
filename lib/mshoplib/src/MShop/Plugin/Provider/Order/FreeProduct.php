<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
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
 * - freeproduct.productcode: '...' (SKU code of the product that should be available for free)
 * - freeproduct.count: ... (how often the product can be bought for free)
 *
 * @package MShop
 * @subpackage Plugin
 */
class FreeProduct
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'freeproduct.productcode' => array(
			'code' => 'freeproduct.productcode',
			'internalcode' => 'freeproduct.productcode',
			'label' => 'SKU of the free product',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		),
		'freeproduct.count' => array(
			'code' => 'freeproduct.count',
			'internalcode' => 'freeproduct.count',
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
	public function checkConfigBE( array $attributes )
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
	public function getConfigBE()
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this->getObject(), 'addProduct.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if an error occurs
	 * @return bool true if subsequent plugins should be processed
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface', $order );
		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Iface', $value );

		$code = $this->getConfigValue( 'freeproduct.productcode' );

		if( $value->getProductCode() !== $code ) {
			return true;
		}

		$addresses = $order->getAddresses();
		if( !isset( $addresses['payment'] ) ) {
			return true;
		}

		$email = $addresses['payment']->getEmail();
		$count = $this->getConfigValue( 'freeproduct.count' );
		$status = \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED;

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'order' );

		$search = $manager->createSearch();
		$expr = [
			$search->compare( '==', 'order.base.address.email', $email ),
			$search->compare( '==', 'order.base.product.prodcode', $code ),
			$search->compare( '>=', 'order.statuspayment', $status ),
		];
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $manager->aggregate( $search, 'order.base.address.email', 'order.base.product.quantity', 'sum' );

		if( isset( $result[$email] ) && $result[$email] < $count ) {
			$value->getPrice()->setRebate( $value->getPrice()->getValue() )->setValue( '0.00' );
		}

		return true;
	}
}
