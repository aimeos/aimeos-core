<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Common methods for frontend controller classes.
 *
 * @package Controller
 * @subpackage Frontend
 */
abstract class Controller_Frontend_Base
{
	private $context = null;


	/**
	 * Common initialization for controller classes.
	 *
	 * @param MShop_Context_Item_Iface $context Common MShop context object
	 */
	public function __construct( MShop_Context_Item_Iface $context )
	{
		$this->context = $context;
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Iface context object implementing MShop_Context_Item_Iface
	 */
	protected function getContext()
	{
		return $this->context;
	}
}
