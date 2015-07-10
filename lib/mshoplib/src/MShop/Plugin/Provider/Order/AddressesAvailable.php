<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Checks addresses are available in a basket as configured.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_AddressesAvailable
	extends MShop_Plugin_Provider_Order_Abstract
	implements MShop_Plugin_Provider_Factory_Interface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'check.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws MShop_Plugin_Provider_Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( $value & MShop_Order_Item_Base_Abstract::PARTS_ADDRESS )
		{
			$problems = array();
			$availableAddresses = $order->getAddresses();

			foreach( $this->_getItem()->getConfig() as $type => $value )
			{
				if ( $value == true && !isset( $availableAddresses[$type] ) ) {
					$problems[$type] = 'available.none';
				}

				if ( $value !== null && $value !== '' && $value == false && isset( $availableAddresses[$type] ) ) {
					$problems[$type] = 'available.notallowed';
				}
			}

			if( count( $problems ) > 0 )
			{
				$code = array( 'address' => $problems );
				throw new MShop_Plugin_Provider_Exception( sprintf( 'Checks for available addresses in basket failed' ), -1, null, $code );
			}
		}

		return true;
	}
}