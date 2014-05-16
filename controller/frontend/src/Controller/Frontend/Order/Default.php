<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Default implementation of the order frontend controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Controller_Frontend_Order_Default
	extends Controller_Frontend_Abstract
	implements Controller_Frontend_Order_Interface
{
	/**
	 * Creates a new order from the given basket.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object to be stored
	 * @return MShop_Order_Item_Interface Order item that belongs to the stored basket
	 */
	public function store( MShop_Order_Item_Base_Interface $basket )
	{
		$context = $this->_getContext();

		MShop_Factory::createManager( $context, 'order/base' )->store( $basket );

		$orderManager = MShop_Factory::createManager( $context, 'order' );

		$orderItem = $orderManager->createItem();
		$orderItem->setBaseId( $basket->getId() );
		$orderItem->setType( MShop_Order_Item_Abstract::TYPE_WEB );
		$orderManager->saveItem( $orderItem );

		return $orderItem;
	}
}
