<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of history order detail HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Account_History_Order_Detail_Default
	extends Client_Html_Common_Summary_Detail_Default
{
	private $_cache;


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view, array &$tags = array(), &$expire = null )
	{
		if( !isset( $this->_cache ) )
		{
			$view = parent::_setViewParams( $view );

			$view->summaryTaxRates = $this->_getTaxRates( $view->summaryBasket );

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}