<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
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
	private array $beConfig = array(
		'single-number-max' => array(
			'code' => 'single-number-max',
			'internalcode' => 'single-number-max',
			'label' => 'Maximum product quantity',
			'type' => 'int',
			'default' => '',
			'required' => false,
		),
		'total-number-max' => array(
			'code' => 'total-number-max',
			'internalcode' => 'total-number-max',
			'label' => 'Maximum total products in basket',
			'type' => 'int',
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
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Subscribes itself to a publisher.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $p Object implementing publisher interface
	 * @return static Plugin object for method chaining
	 */
	public function register( \Aimeos\MShop\Order\Item\Iface $p ) : static
	{
		$plugin = $this->object();

		$p->attach( $plugin, 'addProduct.after' );
		$p->attach( $plugin, 'setProducts.after' );
		$p->attach( $plugin, 'check.after' );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 */
	public function update( \Aimeos\MShop\Order\Item\Iface $order, string $action, $value = null )
	{
		if( $action === 'check.after' && !in_array( 'order/product', (array) $value ) ) {
			return $value;
		}

		// setProducts.after passes the old products and check.after the checked parts
		$list = $action === 'addProduct.after' ? map( [$value] ) : $order->getProducts();
		$list->implements( \Aimeos\MShop\Order\Item\Product\Iface::class, true );

		foreach( $list as $entry )
		{
			// @phpstan-ignore argument.type, argument.type
			$this->checkWithoutCurrency( $order, $entry );
			// @phpstan-ignore argument.type
			$this->checkWithCurrency( $order, $entry );
		}

		$this->checkTotals( $order );

		return $value;
	}


	/**
	 * Checks for the product limits when the configuration doesn't contain limits per currency.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Product\Iface $value Order product item
	 * @return void
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one limit is exceeded
	 */
	protected function checkWithoutCurrency( \Aimeos\MShop\Order\Item\Iface $order,
		\Aimeos\MShop\Order\Item\Product\Iface $value ) : void
	{
		$config = $this->getItemBase()->getConfig();

		if( isset( $config['single-number-max'] ) && !is_array( $config['single-number-max'] )
			&& $value->getQuantity() > (int) $config['single-number-max']
		) {
			$value->setQuantity( (float) $config['single-number-max'] ); // reset to allowed value

			$msg = $this->context()->translate( 'mshop', 'The maximum product quantity is %1$d' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, (int) $config['single-number-max'] ), 409 );
		}
	}


	/**
	 * Checks for the product limits when the configuration contains limits per currency.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
	 * @param \Aimeos\MShop\Order\Item\Product\Iface $value Order product item
	 * @return void
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one limit is exceeded
	 */
	protected function checkWithCurrency( \Aimeos\MShop\Order\Item\Iface $order,
		\Aimeos\MShop\Order\Item\Product\Iface $value ) : void
	{
		$config = $this->getItemBase()->getConfig();
		$currencyId = $value->getPrice()->getCurrencyId();

		if( isset( $config['single-value-max'][$currencyId] )
			&& $value->getPrice()->getValue() * $value->getQuantity() > (float) $config['single-value-max'][$currencyId] // @phpstan-ignore binaryOp.invalid
		) {
			$msg = $this->context()->translate( 'mshop', 'The maximum product value is %1$s' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, (string) $config['single-value-max'][$currencyId] ), 409 );
		}
	}


	/**
	 * Checks for the limits of all products in the basket
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Basket object
	 * @return void
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one limit is exceeded
	 */
	protected function checkTotals( \Aimeos\MShop\Order\Item\Iface $order ) : void
	{
		$totals = [];
		$config = $this->getItemBase()->getConfig();

		if( isset( $config['total-number-max'] ) && !is_array( $config['total-number-max'] )
			&& $order->getProducts()->sum( fn( $product ) => $product->getQuantity() ) > (int) $config['total-number-max']
		) {
			$msg = $this->context()->translate( 'mshop', 'The maximum quantity of all products is %1$d' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, (int) $config['total-number-max'] ), 409 );
		}

		foreach( $order->getProducts() as $product )
		{
			$price = $product->getPrice();
			$currencyId = (string) $price->getCurrencyId();
			$totals[$currencyId] = ( $totals[$currencyId] ?? 0 ) + $price->getValue() * $product->getQuantity(); // @phpstan-ignore binaryOp.invalid
		}

		foreach( $totals as $currencyId => $total )
		{
			if( isset( $config['total-value-max'][$currencyId] ) && $total > (float) $config['total-value-max'][$currencyId] )
			{
				$msg = $this->context()->translate( 'mshop', 'The maximum value of all products is %1$s' );
				throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, (string) $config['total-value-max'][$currencyId] ), 409 );
			}
		}
	}
}
