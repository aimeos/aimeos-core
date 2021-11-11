<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks for available service items in the basket
 *
 * There are two services types by default:
 * - delivery
 * - payment
 *
 * For both types can be specifified if they are
 * - required (payment: 1 or delivery: 1)
 * - optional (payment: '' or delivery: '' or not set)
 * - not allowed (payment: 0 or delivery: 0)
 *
 * The checks are executed before the checkout summary page is rendered
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class ServicesAvailable
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	private $beConfig = array(
		'payment' => array(
			'code' => 'payment',
			'internalcode' => 'payment',
			'label' => 'Require payment option',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '',
			'required' => false,
		),
		'delivery' => array(
			'code' => 'delivery',
			'internalcode' => 'delivery',
			'label' => 'Require delivery option',
			'type' => 'boolean',
			'internaltype' => 'boolean',
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
		if( ( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) === 0 ) {
			return $value;
		}

		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Iface::class, $order );

		$problems = [];
		$services = $order->getServices();

		foreach( $this->getItemBase()->getConfig() as $type => $val )
		{
			if( $val == true && ( !isset( $services[$type] ) || empty( $services[$type] ) ) ) {
				$problems[$type] = 'available.none';
			}

			if( $val !== null && $val !== '' && $val == false
				&& isset( $services[$type] ) && !empty( $services[$type] )
			) {
				$problems[$type] = 'available.notallowed';
			}
		}

		if( count( $problems ) > 0 )
		{
			$code = array( 'service' => $problems );
			$msg = $this->getContext()->translate( 'mshop', 'Checks for available service items in basket failed' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $code );
		}

		return $value;
	}
}
