<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks addresses are available in a basket as configured.
 *
 * @package MShop
 * @subpackage Plugin
 */
class AddressesAvailable
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
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS )
		{
			$problems = array();
			$availableAddresses = $order->getAddresses();

			foreach( $this->getItemBase()->getConfig() as $type => $value )
			{
				if( $value == true && !isset( $availableAddresses[$type] ) ) {
					$problems[$type] = 'available.none';
				}

				if( $value !== null && $value !== '' && $value == false && isset( $availableAddresses[$type] ) ) {
					$problems[$type] = 'available.notallowed';
				}
			}

			if( count( $problems ) > 0 )
			{
				$code = array( 'address' => $problems );
				throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( 'Checks for available addresses in basket failed' ), -1, null, $code );
			}
		}

		return true;
	}
}