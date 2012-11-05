<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id$
 */


/**
 * Manual delivery provider implementation.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Delivery_Manual
	extends MShop_Service_Provider_Delivery_Abstract
	implements MShop_Service_Provider_Delivery_Interface
{
	private $_context;
	private $_serviceItem;


	/**
	 * Initializes a new service provider object using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Service_Item_Interface $serviceItem Service item with configuration for the provider
	 */
	public function __construct(MShop_Context_Item_Interface $context, MShop_Service_Item_Interface $serviceItem)
	{
		$this->_context = $context;
		$this->_serviceItem = $serviceItem;
	}


	/**
	 * Updates the delivery status.
	 *
	 * @param MShop_Order_Item_Interface $order Order instance
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$order->setDeliveryStatus( MShop_Order_Item_Abstract::STAT_PROGRESS );
	}
	
}
