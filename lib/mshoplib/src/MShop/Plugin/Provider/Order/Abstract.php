<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Abstract class for plugin provider implementations.
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class MShop_Plugin_Provider_Order_Abstract
{
	private $_item;
	private $_context;


	/**
	 * Initializes the plugin instance.
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
	 * Returns the plugin item the provider is configured with.
	 *
	 * @return MShop_Plugin_Item_Interface Plugin item object
	 */
  protected function _getItem()
  {
  	return $this->_item;
  }


  /**
   * Returns the context object.
   *
   * @return MShop_Context_Item_Interface Context item object
   */
  protected function _getContext()
  {
  	return $this->_context;
  }
}