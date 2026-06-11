<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2025
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
 * In addition, the "available" option re-checks if the delivery/payment services
 * actually stored in the basket are still available according to their service
 * provider (including decorators like customer- or country-specific restrictions):
 * - available: 1 (re-check selected services and remove unavailable ones)
 * - available: '' or not set (don't re-check, only presence/absence is validated)
 *
 * This makes sure that restrictions enforced for the service list display are also
 * enforced server-side during checkout. Services that aren't available any more are
 * removed from the basket so the customer is forced to select a valid option again.
 * The default is off to keep the behavior backward compatible; enable it for shops
 * that rely on provider-based availability checks.
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
	private array $beConfig = array(
		'payment' => array(
			'code' => 'payment',
			'internalcode' => 'payment',
			'label' => 'Require payment option',
			'type' => 'bool',
			'default' => '',
			'required' => false,
		),
		'delivery' => array(
			'code' => 'delivery',
			'internalcode' => 'delivery',
			'label' => 'Require delivery option',
			'type' => 'bool',
			'default' => '',
			'required' => false,
		),
		'available' => array(
			'code' => 'available',
			'internalcode' => 'available',
			'label' => 'Re-check availability of selected services',
			'type' => 'bool',
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
	 * @return array List of attribute definitions implementing \Aimeos\Base\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


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
		if( !in_array( 'order/service', (array) $value ) ) {
			return $value;
		}

		$problems = [];

		if( (bool) $this->getConfigValue( 'available', false ) === true ) {
			$problems = $this->checkAvailable( $order );
		}

		// Re-fetch after checkAvailable() may have removed unavailable services so a
		// removed but required type correctly produces "available.none" below
		$services = $order->getServices();

		foreach( $this->getItemBase()->getConfig() as $type => $val )
		{
			if( $type === 'available' ) { // not a service type but the re-check toggle
				continue;
			}

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
			$msg = $this->context()->translate( 'mshop', 'Checks for available service items in basket failed' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $code );
		}

		return $value;
	}


	/**
	 * Removes the services from the basket that aren't available any more
	 *
	 * Each service stored in the basket is checked against its service provider
	 * (including decorators). Services whose underlying service item doesn't exist
	 * any more (deleted or disabled) or whose provider reports them as not available
	 * are removed from the basket and reported as problem.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shop basket instance
	 * @return array Associative list of service types as keys and error codes as values
	 */
	protected function checkAvailable( \Aimeos\MShop\Order\Item\Iface $order ) : array
	{
		$ids = [];

		foreach( $order->getServices() as $list )
		{
			foreach( $list as $ordService )
			{
				if( $id = $ordService->getServiceId() ) {
					$ids[$id] = null;
				}
			}
		}

		if( empty( $ids ) ) {
			return [];
		}

		$manager = \Aimeos\MShop::create( $this->context(), 'service' );

		// filter( true ) only returns enabled/valid services, so deleted or disabled
		// services are missing from the result and treated as not available
		$filter = $manager->filter( true )->add( ['service.id' => array_keys( $ids )] );
		$items = $manager->search( $filter, ['media', 'price', 'text'] );

		$problems = [];

		foreach( $order->getServices() as $type => $list )
		{
			foreach( $list as $pos => $ordService )
			{
				$item = $items->get( $ordService->getServiceId() );

				if( $item === null
					|| $manager->getProvider( $item, $item->getType() )->isAvailable( $order ) !== true
				) {
					$order->deleteService( $type, $pos );
					$problems[$type] = 'available.notallowed';
				}
			}
		}

		return $problems;
	}
}
