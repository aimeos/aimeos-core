<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of email html address HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Email_Confirm_Main_Html_Address_Default
	extends Client_Html_Common_Summary_Address_Default
	implements Client_Html_Interface
{
	private $_cache;


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view )
	{
		if( !isset( $this->_cache ) )
		{
			$view->summaryBasket = $view->confirmOrderBaseItem;

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}