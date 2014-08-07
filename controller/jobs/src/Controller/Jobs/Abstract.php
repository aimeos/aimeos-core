<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Common methods for Jobs controller classes.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Controller_Jobs_Abstract
{
	private $_arcavias;
	private $_context;


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 * @param Arcavias $arcavias Arcavias main object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Arcavias $arcavias )
	{
		$this->_context = $context;
		$this->_arcavias = $arcavias;
	}


	/**
	 * Returns the context object.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		return $this->_context;
	}


	/**
	 * Returns the Arcavias object.
	 *
	 * @return Arcavias Arcavias object
	 */
	protected function _getArcavias()
	{
		return $this->_arcavias;
	}
}
