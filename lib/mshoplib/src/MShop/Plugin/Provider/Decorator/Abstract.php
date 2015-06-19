<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Base decorator methods for plugin provider.
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class MShop_Plugin_Provider_Decorator_Abstract
	extends MShop_Plugin_Provider_Order_Abstract
	implements MShop_Plugin_Provider_Decorator_Interface
{
	private $_object;


	/**
	 * Initializes the plugin instance
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Plugin_Item_Interface $item Plugin item object
	 * @param MShop_Plugin_Provider_Interface $item Plugin item object
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Plugin_Item_Interface $item,
		MShop_Plugin_Provider_Interface $provider )
	{
		parent::__construct( $context, $item );

		$this->_object = $provider;
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$this->_object->register( $p );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @param boolean True if successful, false if not
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		return $this->_object->update( $order, $action, $value );
	}


	/**
	 * Returns the next provider or decorator.
	 *
	 * @return MShop_Plugin_Provider_Interface Provider or decorator object
	 */
	protected function _getProvider()
	{
		return $this->_object;
	}
}
