<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Common methods for frontend controller classes.
 *
 * @package Controller
 * @subpackage Frontend
 */
abstract class Controller_Frontend_Abstract
{
	private $_context = null;


	/**
	 * Common initialization for controller classes.
	 *
	 * @param MShop_Context_Item_Interface $context Common MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		$this->_context = $context;
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Interface context object implementing MShop_Context_Item_Interface
	 */
	protected function _getContext()
	{
		return $this->_context;
	}
}
