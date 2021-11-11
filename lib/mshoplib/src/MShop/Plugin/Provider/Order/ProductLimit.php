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
 * Product limit implementation if count or sum of a single or of all products in an order exceeds given limit
 *
 * Enforces product restrictions like
 * - single-number-max: 10 (Maximum times a single product can be bought in one order)
 * - total-number-max: 100 (Maximum number of products that can be in the basket, e.g. basket product * quantity)
 * - single-value-max: 'EUR' => '100.00' (Maximum amount for one product, i.e. price * quantity)
 * - total-value-max: 'EUR' => '1000.00' (Maximum amount for all product, i.e. basket product * price * quantity)
 *
 * These limits are enforced if any product in the basket changes.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductLimit
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'single-number-max' => array(
			'code' => 'single-number-max',
			'internalcode' => 'single-number-max',
			'label' => 'Maximum product quantity',
			'type' => 'integer',
			'internaltype' => 'integer',
			'default' => '',
			'required' => false,
		),
		'total-number-max' => array(
			'code' => 'total-number-max',
			'internalcode' => 'total-number-max',
			'label' => 'Maximum total products in basket',
			'type' => 'integer',
			'internaltype' => 'integer',
			'default' => '',
			'required' => false,
		),
		'single-value-max' => array(
			'code' => 'single-value-max',
			'internalcode' => 'single-value-max',
			'label' => 'Maximum product value',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => '{}',
			'required' => false,
		),
		'total-value-max' => array(
			'code' => 'total-value-max',
			'internalcode' => 'total-value-max',
			'label' => 'Maximum total basket value',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => '{}',
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
	 * Subscribes itself to a publisher.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		$plugin = $this->getObject();

		$p->attach( $plugin, 'addProduct.after' );
		$p->attach( $plugin, 'setProducts.after' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Iface::class, $order );

		if( is_array( $value ) )
		{
			foreach( $value as $entry )
			{
				\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $entry );

				$this->checkWithoutCurrency( $order, $entry );
				$this->checkWithCurrency( $order, $entry );
			}
		}
		else
		{
			\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $value );

			$this->checkWithoutCurrency( $order, $value );
			$this->checkWithCurrency( $order, $value );
		}

		return $value;
	}


	/**
	 * Checks for the product limits when the configuration doesn't contain limits per currency.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $value Order product item
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one limit is exceeded
	 */
	protected function checkWithoutCurrency( \Aimeos\MShop\Order\Item\Base\Iface $order,
		\Aimeos\MShop\Order\Item\Base\Product\Iface $value )
	{
		$config = $this->getItemBase()->getConfig();

		if( isset( $config['single-number-max'] ) && !is_array( $config['single-number-max'] )
			&& $value->getQuantity() > (int) $config['single-number-max']
		) {
			$value->setQuantity( $config['single-number-max'] ); // reset to allowed value

			$msg = $this->getContext()->translate( 'mshop', 'The maximum product quantity is %1$d' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, (int) $config['single-number-max'] ) );
		}


		if( isset( $config['total-number-max'] ) && !is_array( $config['total-number-max'] ) )
		{
			$total = $value->getQuantity();

			foreach( $order->getProducts() as $product ) {
				$total += $product->getQuantity();
			}

			if( $total > (int) $config['total-number-max'] )
			{
				$msg = $this->getContext()->translate( 'mshop', 'The maximum quantity of all products is %1$d' );
				throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, (int) $config['total-number-max'] ) );
			}
		}
	}


	/**
	 * Checks for the product limits when the configuration contains limits per currency.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $value Order product item
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one limit is exceeded
	 */
	protected function checkWithCurrency( \Aimeos\MShop\Order\Item\Base\Iface $order,
		\Aimeos\MShop\Order\Item\Base\Product\Iface $value )
	{
		$config = $this->getItemBase()->getConfig();
		$currencyId = $value->getPrice()->getCurrencyId();

		if( isset( $config['single-value-max'][$currencyId] )
			&& $value->getPrice()->getValue() * $value->getQuantity() > (float) $config['single-value-max'][$currencyId]
		) {
			$msg = $this->getContext()->translate( 'mshop', 'The maximum product value is %1$s' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['single-value-max'][$currencyId] ) );
		}


		if( isset( $config['total-value-max'][$currencyId] ) )
		{
			$price = clone $value->getPrice();
			$price->setValue( $price->getValue() * $value->getQuantity() );

			foreach( $order->getProducts() as $product ) {
				$price->addItem( $product->getPrice(), $product->getQuantity() );
			}

			if( (float) $price->getValue() > (float) $config['total-value-max'][$currencyId] )
			{
				$msg = $this->getContext()->translate( 'mshop', 'The maximum value of all products is %1$s' );
				throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['total-value-max'][$currencyId] ) );
			}
		}
	}
}
