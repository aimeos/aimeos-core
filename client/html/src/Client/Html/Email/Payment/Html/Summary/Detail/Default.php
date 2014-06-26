<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of order summary detail HTML client.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Email_Payment_Html_Summary_Detail_Default
	extends Client_Html_Common_Summary_Detail_Default
{
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
		// we can't cache the calculation because the same client object is used for all e-mails
		$view->summaryTaxRates = $this->_getTaxRates( $view->extOrderBaseItem );

		return $view;
	}
}
