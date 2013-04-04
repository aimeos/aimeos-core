<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Plugin
 * @version $Id$
 */


/**
 * Checks basket for available service items.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ServicesAvailable implements MShop_Plugin_Provider_Interface
{

	protected $_item;
	protected $_context;


	/**
	 * Initializes the plugin instance
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Plugin_Item_Interface $item Plugin item object
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Plugin_Item_Interface $item )
	{
		$this->_item = $item;
		$this->_context = $context;
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'isComplete.after' );
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
		if( $value & MShop_Order_Item_Base_Abstract::PARTS_SERVICE )
		{
			$config = $this->_item->getConfig();
			$problems = array();

			$availableServices = $order->getServices();

			foreach( $config as $type => $value )
			{
				if ( ( $value == true ) && ( !isset( $availableServices[$type] ) ) ) {
					$problems[$type] = 'available.none';
				}

				if ( ( $value !== null ) && ( $value == false ) && ( isset( $availableServices[$type] ) ) ) {
					$problems[$type] = 'available.notallowed';
				}
			}

			if( count( $problems ) > 0 )
			{
				$code = array( 'service' => $problems );
				throw new MShop_Plugin_Provider_Exception( sprintf( 'Checks for available service items in basket failed.' ), -1, null, $code );
			}
		}
		return true;
	}
}