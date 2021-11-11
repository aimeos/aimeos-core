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
 * Checks if ordered product sum and count of products is above a certain value
 *
 * For the basket and checkout summery view, this plugin checks if the products
 * in the basket are still within the configured limits.
 *
 * Available checks are:
 * - min-value: 'EUR' => '10.00' (Minimum total basket value incl. rebates)
 * - max-value: 'EUR' => '10.00' (Maximum total basket value incl. rebates)
 * - min-products: 10 (Minumum number of articles in the basket i.e. basket product * quantity)
 * - max-products: 100 (Maximum number of articles in the basket i.e. basket product * quantity)
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class BasketLimits
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'min-value' => array(
			'code' => 'min-value',
			'internalcode' => 'min-value',
			'label' => 'Minimum basket value',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
			'required' => false,
		),
		'max-value' => array(
			'code' => 'max-value',
			'internalcode' => 'max-value',
			'label' => 'Maximum basket value',
			'type' => 'map',
			'internaltype' => 'array',
			'default' => [],
			'required' => false,
		),
		'min-products' => array(
			'code' => 'min-products',
			'internalcode' => 'min-products',
			'label' => 'Minimum total products',
			'type' => 'integer',
			'internaltype' => 'integer',
			'default' => '1',
			'required' => false,
		),
		'max-products' => array(
			'code' => 'max-products',
			'internalcode' => 'max-products',
			'label' => 'Maximum total products',
			'type' => 'integer',
			'internaltype' => 'integer',
			'default' => '',
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
		$p->attach( $this->getObject(), 'check.after' );
		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		if( ( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) === 0 ) {
			return $value;
		}

		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Iface::class, $order );
		$context = $this->getContext();

		/** mshop/plugin/provider/order/complete/disable
		 * Disables the basket limits check
		 *
		 * If the BasketLimits plug-in is enabled, it enforces the configured
		 * limits before customers or anyone on behalf of them can continue the
		 * checkout process.
		 *
		 * This option enables e.g. call center agents to place orders which
		 * doesn't satisfy all requirements. It may be useful if you want to
		 * allow them to send free or replacements for lost or damaged products.
		 *
		 * @param bool True to disable the check, false to keep it enabled
		 * @category Developer
		 * @category User
		 * @since 2014.03
		 */
		if( $context->getConfig()->get( 'mshop/plugin/provider/order/complete/disable', false ) != true )
		{
			$count = 0;
			$sum = \Aimeos\MShop::create( $context, 'price' )->create();

			foreach( $order->getProducts() as $product )
			{
				$sum->addItem( $product->getPrice(), $product->getQuantity() );
				$count += $product->getQuantity();
			}

			$this->checkLimits( $sum, $count );
		}

		return $value;
	}


	/**
	 * Checks for the configured basket limits.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $sum Total sum of all product price items
	 * @param int $count Total number of products in the basket
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one of the minimum or maximum limits is exceeded
	 */
	protected function checkLimits( \Aimeos\MShop\Price\Item\Iface $sum, int $count )
	{
		$config = $this->getItemBase()->getConfig();

		$this->checkLimitsValue( $config, $sum );
		$this->checkLimitsProducts( $config, $count );
	}


	/**
	 * Checks for the configured basket limits.
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $sum Total sum of all product price items
	 * @param array $config Associative list of configuration key/value pairs
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one of the minimum or maximum limits is exceeded
	 */
	protected function checkLimitsValue( array $config, \Aimeos\MShop\Price\Item\Iface $sum )
	{
		$currencyId = $sum->getCurrencyId();

		if( ( isset( $config['min-value'][$currencyId] ) ) && is_numeric( $config['min-value'][$currencyId] )
			&& ( $sum->getValue() + $sum->getRebate() < $config['min-value'][$currencyId] )
		) {
			$msg = $this->getContext()->translate( 'mshop', 'The minimum basket value of %1$s isn\'t reached' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['min-value'][$currencyId] ) );
		}

		if( ( isset( $config['max-value'][$currencyId] ) ) && is_numeric( $config['max-value'][$currencyId] )
			&& ( $sum->getValue() + $sum->getRebate() > $config['max-value'][$currencyId] )
		) {
			$msg = $this->getContext()->translate( 'mshop', 'The maximum basket value of %1$s is exceeded' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['max-value'][$currencyId] ) );
		}
	}


	/**
	 * Checks for the configured basket limits.
	 *
	 * @param array $config Associative list of configuration key/value pairs
	 * @param int $count Total number of products in the basket
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one of the minimum or maximum limits is exceeded
	 */
	protected function checkLimitsProducts( array $config, $count )
	{
		if( ( isset( $config['min-products'] ) ) && is_numeric( $config['min-products'] )
			&& ( $count < $config['min-products'] )
		) {
			$msg = $this->getContext()->translate( 'mshop', 'The minimum product quantity of %1$d isn\'t reached' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['min-products'] ) );
		}

		if( ( isset( $config['max-products'] ) ) && is_numeric( $config['max-products'] )
			&& ( $count > $config['max-products'] )
		) {
			$msg = $this->getContext()->translate( 'mshop', 'The maximum product quantity of %1$d is exceeded' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $config['max-products'] ) );
		}
	}
}
