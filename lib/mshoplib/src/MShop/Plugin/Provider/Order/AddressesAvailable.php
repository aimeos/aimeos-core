<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
		if( !( $order instanceof \Aimeos\MShop\Order\Item\Base\Iface ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Object is not of required type "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, '\Aimeos\MShop\Order\Item\Base\Iface' ) );
		}

		if( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS )
		{
			$problems = [];
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
				$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Checks for available addresses in basket failed' );
				throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $code );
			}
		}

		return true;
	}
}
